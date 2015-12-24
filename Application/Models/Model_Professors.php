<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 26.11.15
 * Time: 17:57
 */

namespace Application\Models;


class Model_Professors extends Model_Dashboard
{
    /**
     * Возвращает массив с набором данных о текущем местоположении/статусе преподавателя
     * @param int $professor_id - уникальный номер преподавателя
     * @return array mixed
     * name,department,lesson_num,
     * state = now/next/false
     * now - на текущий момент времени идет пара
     * next - возвращена следующая пара
     * false - пар на сегодня нету
     * group_number, lesson_name, classroom
     */
    function get_professor_state($professor_id){
        $lesson_number = $this->get_lesson_number_by_time(date("G:i:s")); // определяет какая по счету идет пара/ пар нету
        $prof_info = $this->get_professor_info($professor_id);
        if ($lesson_number === false)
            $result["state"] = "false";
        else
            $result = $this->find_professor_day_conformity_with_lesson_number($professor_id,$lesson_number);
        $result['name'] = $prof_info["professor"];
        $result['department'] = $prof_info["depart_name"];
        return $result;
    }

    /** Получение списка преподавателей
     * @return mixed уникальный id,professor(ФИО),depart_name(кафедра)
     */
    function get_professors_list(){
        $query = "SELECT prof.id,prof.professor,list.depart_name FROM professors as prof,departments_list as list WHERE prof.department_id = list.id";
        $result=$this->database->getALL($query);
        return $result;
    }

    /**
     * Возвращает рассписание преподавателя на неделю
     * @param $professor_id
     * @return array mixed
     */
    function get_professor_timetable($professor_id){
        $result["even"] =$this->week_professor_parse($professor_id,'ch');
        $result["uneven"] =$this->week_professor_parse($professor_id,'zn');
        return $result;
    }

    /**Поиск совпадений между парами преподавателя и текущей парой
     * При отсутствии совпадений вывод ближайшей на сегодня
     * при отсутсвиии пар у преподавателя - false
     * @param $professor_id
     * @param $lesson_number
     * @return array mixed
     */
    private function find_professor_day_conformity_with_lesson_number($professor_id,$lesson_number){

        $day = date("w");
        $week_numerator = $this->get_week_numerator();

        $min_dif = 7; // минимальная разница между следующей и текущей парой

        // Получение пар преподавателя на сегодня
        $query = "SELECT group_number, lesson_number,lesson_name,classroom FROM groups WHERE
                  professor_id=?s and day_number=?s and (numerator='all' or numerator =?s)";
        $result_of_query = $this->database->getAll($query, $professor_id, $day, $week_numerator);

        foreach ($result_of_query as $value){  // Поиск совпадений между парами преподавателя и текущей парой
            if ($value["lesson_number"] == $lesson_number) {

                $result["lesson_num"] = $lesson_number;

                if ($this->is_rest($lesson_number) == true) // При перемене перед парой помечаем состояние пары next
                    $result["state"] = "next";
                else
                    $result["state"] = "now";     // Если пара идет в текущей момент времени помечаем состояние now
                $result["group_number"] = $value["group_number"];
                $result["lesson_name"] = $value["lesson_name"];
                $result["classroom"] = $value["classroom"];
                return $result;
            }
            // Поиск минимальной разницы между текущей парой и следующей возможной у преподавателя
            $difference_between_lessons_number = $value["lesson_number"] - $lesson_number;
            if ($difference_between_lessons_number > 0 & $min_dif > $difference_between_lessons_number){
                $min_dif = $difference_between_lessons_number;
                $result["lesson_num"] = $value["lesson_number"];
                $result["state"] = "next";
                $result["group_number"] = $value["group_number"];
                $result["lesson_name"] = $value["lesson_name"];
                $result["classroom"] = $value["classroom"];
            }
        }
        if ($difference_between_lessons_number == 7){ // если начальное значение не изменилось - пар сегодня уже нет/ не было
            $result["state"] = "false";
            return $result["state"];
        }
        else
            return $result;
    }

    /**
     * Возвращает информацию об указанном преподавателе
     * @param $professor_id - уникальный индефикатор преподавателя
     * @return array [professor] [depart_name]
     */
    private function get_professor_info($professor_id){
        $query = "SELECT prof.professor,list.depart_name FROM professors as prof,departments_list as list WHERE prof.department_id = list.id and prof.id=?s";
        $result=$this->database->getRow($query,$professor_id);
        return $result;
    }

    private function week_professor_parse($professor_id,$numerator){
        $query = "SELECT * FROM groups WHERE professor_id=?s and (numerator = 'all' or numerator= ?s)";
        $result_of_query=$this->database->getAll($query,$professor_id,$numerator);

        for ($i=1;$i<=6;$i++)
            $result[$i]=array();

        foreach ($result_of_query as $value) {
            $day = $value['day_number'];
            $lesson_number = $value['lesson_number'];
            $result[$day][$lesson_number]["lesson_name"] = $value["lesson_name"];
            $result[$day][$lesson_number]["group_number"] = $value["group_number"];
        }
        return $result;

    }
}
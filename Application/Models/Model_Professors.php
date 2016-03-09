<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 26.11.15
 * Time: 17:57
 * @author Darevski
 */

namespace Application\Models;

use Application\Models\Base\Model_Dashboard;
use Application\Exceptions\Models_Processing_Except;
/**
 * Класс логики связанный с отображением информации о преподавателях, их рассписание, и т.д.
 * Class Model_Professors
 * @package Application\Models
 */
class Model_Professors extends Model_Dashboard
{
    /**
     * Возвращает массив с набором данных о текущем местоположении/статусе преподавателя
     * @param integer $professor_id уникальный номер преподавателя
     * @return array
     * name,photo_url, department, lesson_num, lesson_state = now/next/false
     * - now на текущий момент времени идет пара
     * - next возвращена следующая пара
     * - false пар на сегодня нету
     *
     * group_number, lesson_name, classroom,start_time,end_time
     *
     * [state] = 'success' || [state] = 'fail' && [message] = string
     * @throws Models_Processing_Except
     */
    function get_professor_state($professor_id)
    {
        if (!is_int($professor_id))
            throw new Models_Processing_Except("Идентификатор $professor_id не является числом");
        else if (!$this->isset_professor($professor_id))
            throw new Models_Processing_Except("Преподавателя с индентификатором - $professor_id не существует");

        $day = $this->date_time_model->get_day()['today'];
        // Если сегодня воскресенье
        if ($day == false) {
            $result['weekend'] = 'true';
        }
        else {
            // определяет какая по счету идет пара/ пар нету
            $lesson_number = $this->get_lesson_number_by_time(date("H:i:s"));
            if ($lesson_number === false)
                $result["lesson_state"] = "false";
            else {
                $result = $this->find_professor_day_conformity_with_lesson_number($professor_id, $lesson_number, $day);
                $start_end_time = $this->lesson_begin_end_time($lesson_number);
                $result['start_time'] = $start_end_time['start_time'];
                $result['end_time'] = $start_end_time['end_time'];
            }
        }

        $prof_info = $this->get_professor_info($professor_id);

        $result['photo_url'] = $prof_info['photo_url'];
        $result['department'] = $prof_info['depart_name'];
        $result['name'] = $prof_info['professor'];

        $result['state'] = 'success';


        return $result;
    }

    /** Получение списка преподавателей
     * @return array уникальный id,professor(ФИО),depart_name(кафедра)
     */
    function get_professors_list(){
        $query = "SELECT prof.id,prof.name,dep_list.depart_name FROM professors as prof,departments_list as dep_list
                  WHERE prof.department_code = dep_list.code";
        $result=$this->database->getALL($query);
        $result['state']='success';
        return $result;
    }

    /** Возвращает всех преподавателей с id, код кафедры, фио
     * @return array
     */
    private function get_professors_thin_list(){
        $professors_query = "SELECT professors.id as professor_id,professors.department_code,professors.name FROM professors";
        $professors = $this->database->getAll($professors_query);
        return $professors;
    }


    /**
     * Создает массив со списком преподавателей и предметов (кафедры преподавателя)
     * @return mixed
     */
    function get_list_professors_with_lessons(){
        //Получение общего списка предметов id, name, dep_code
        $Model_Lessons = new Model_Lessons();
        $lessons = $Model_Lessons->get_list_lessons();
        // Получение списка преподавателей id, фио, dep code
        $professors = $this->get_professors_thin_list();

        $lessons_ordered = array();
        //Перекомпановка массива предметов под каждую кафедру
        foreach ($lessons as $value)
            $lessons_ordered[$value['department_code']][] = $value;

        unset ($value);

        $result = array();
        // Создание результирующего массива вида преподаватель [предметы его ЦК]
        foreach ($professors as $value){
            $value['lessons'] =null;
            if (isset($lessons_ordered[$value['department_code']]))
                $value['lessons'] = $lessons_ordered[$value['department_code']];

            $result[] = $value;
        }
        return $result;
    }

    /**
     * Создает массив предметов с преподавателями кафедры на котором ведется предмет
     * @return mixed
     */
    function get_list_lessons_with_professors(){
        //Получение общего списка предметов id, name, dep_code
        $model_lessons = new Model_Lessons();
        $lessons = $model_lessons->get_list_lessons();
        // Получение списка преподавателей id, фио, dep code
        $professors = $this->get_professors_thin_list();

        $professors_ordered = array();
        //Перекомпановка массива преподавателей под каждую кафедру
        foreach ($professors as $value)
            $professors_ordered[$value['department_code']][] = $value;

        unset ($value);

        $result = array();
        // Создание результирующего массива вида преподаватель [предметы его ЦК]
        foreach ($lessons as $value){
            $value['professors'] =null;
            if (isset($professors_ordered[$value['department_code']]))
                $value['professors'] = $professors_ordered[$value['department_code']];

            $result[] = $value;
        }
        return $result;
    }


    /**
     * Возвращает рассписание преподавателя на неделю
     * @param integer $professor_id уникальный индентификатор преподавателя
     * @return array even/uneven { day { lesson_num { group_number,lesson_name } }
     * @throws Models_Processing_Except
     */
    function get_professor_timetable($professor_id)
    {
        if (!is_int($professor_id))
            throw new Models_Processing_Except("Идентификатор $professor_id не является числом");
        else if (!$this->isset_professor($professor_id))
            throw new Models_Processing_Except("Преподавателя с индентификатором - $professor_id не существует");

        $result["even"] = $this->week_professor_parse($professor_id, 'ch');
        $result["uneven"] = $this->week_professor_parse($professor_id, 'zn');
        $result['state'] = 'success';

        return $result;
    }

    /**
     * Проверяет существование преподавателя по указанному id
     * @param integer $id
     * @return bool
     */
    public function isset_professor($id){
        $query = 'SELECT * FROM professors WHERE id = ?s';
        $result = $this->database->query($query,$id);
        if ($this->database->numRows($result) > 0)
            return true;
        else
            return false;
    }

    /**
     * Поиск совпадений между парами преподавателя и текущей парой
     * При отсутствии совпадений вывод ближайшей пары преподавателя, которую он ведет сегодня
     * при отсутсвиии пар у преподавателя - false
     * @param integer $professor_id
     * @param integer $lesson_number
     * @param integer $day день недели
     * @return array
     */
    private function find_professor_day_conformity_with_lesson_number($professor_id,$lesson_number,$day){
        $week_numerator = $this->date_time_model->get_week_numerator();
        $result = false;
        $min_dif = 7; // минимальная разница между следующей и текущей парой

        // Получение пар преподавателя на сегодня
        $query = "SELECT group_number, lesson_number,lesson_name,classroom FROM groups WHERE
                  professor_id=?s and day_number=?s and (numerator='all' or numerator =?s)";
        $result_of_query = $this->database->getAll($query, $professor_id, $day, $week_numerator);

        foreach ($result_of_query as $value){  // Поиск совпадений между парами преподавателя и текущей парой
            if ($value["lesson_number"] == $lesson_number) {

                $result["lesson_num"] = $lesson_number;

                if ($this->is_rest($lesson_number) == true) // При перемене перед парой помечаем состояние пары next
                    $result["lesson_state"] = "next";
                else
                    $result["lesson_state"] = "now";     // Если пара идет в текущей момент времени помечаем состояние now
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
                $result["lesson_state"] = "next";
                $result["group_number"] = $value["group_number"];
                $result["lesson_name"] = $value["lesson_name"];
                $result["classroom"] = $value["classroom"];
            }
        }
        if ($min_dif == 7){ // если начальное значение не изменилось - пар сегодня уже нет/ не было
            $result["lesson_state"] = "false";
            return $result;
        }
        else
            return $result;
    }

    /**
     * Возвращает информацию об указанном преподавателе
     * @param integer $professor_id - уникальный индефикатор преподавателя
     * @return array [professor] [depart_name] [photo_url]
     */
    private function get_professor_info($professor_id){
        $query = "SELECT prof.name,dep_list.depart_name,photo_url FROM professors as prof,departments_list as dep_list
                  WHERE prof.department_code = dep_list.code and prof.id=?s";
        $result=$this->database->getRow($query,$professor_id);
        return $result;
    }
    /**
     * Создание рассписания преподавателя на неделю с учетом нумератора недели
     * @param integer $professor_id
     * @param string $numerator
     * @return array Номер дня, номер пары, название предмета, номер группы у которой ведет преподаватель
     */
    private function week_professor_parse($professor_id,$numerator){
        $query = "SELECT * FROM groups WHERE professor_id=?s and (numerator = 'all' or numerator= ?s)";
        $result_of_query=$this->database->getAll($query,$professor_id,$numerator);
        $result = null;
        for ($i=1;$i<=6;$i++)
            $result[$i]=array();

        foreach ($result_of_query as $value) {
            $day = $value['day_number'];
            $lesson_number = $value['lesson_number'];
            $result[$day][$lesson_number]["lesson_name"] = $value["lesson_name"];
            $result[$day][$lesson_number]["group_number"] = $value["group_number"];
        }
        $max_min = $this->week_max_min($result);
        $result['max']=$max_min['max'];
        $result['min']=$max_min['min'];
        return $result;
    }


}
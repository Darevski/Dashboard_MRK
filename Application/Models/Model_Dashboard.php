<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 22.09.15
 * Time: 14:29
 */

namespace Application\Models;
use Application\Core;

class Model_Dashboard extends Core\Model
{
    /**
     * Возвращает информацию о выбранном преподавателе
     */
    function get_professor_state($professor_id){
        $lesson_number = $this->get_lesson_number_by_time(date("G:i:s"));
        $day = date("w");
        $week_numerator = $this->get_week_numerator();

        $prof_info = $this->get_professor_info($professor_id);
        $result['name'] = $prof_info["professor"];
        $result['department'] = $prof_info["depart_name"];
        if ($lesson_number == false){
            $result["state"] = "false";
            return $result["state"];
        }
        else {
            $min_dif = 7;

            $query = "SELECT group_number, lesson_number,lesson_name,classroom FROM groups WHERE
                  professor_id=?s and day_number=?s and (numerator='all' or numerator =?s)";
            $result_of_query = $this->database->getAll($query, $professor_id, $day, $week_numerator);

            foreach ($result_of_query as $value){
                if ($value["lesson_number"] == $lesson_number) {

                    $result["lesson_num"] = $lesson_number;

                    if ($this->is_rest($lesson_number) == true)
                        $result["state"] = "next";
                    else
                        $result["state"] = "now";
                    $result["group_number"] = $value["group_number"];
                    $result["lesson_name"] = $value["lesson_name"];
                    $result["classroom"] = $value["classroom"];
                    return $result;
                }
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
            if ($difference_between_lessons_number == 7){
                $result["state"] = "false";
                return $result["state"];
            }
            else
                return $result;

        }

    }

    /**
     * Возвращает рассписание преподавателя на неделю
     */
    function get_professor_timetable($professor_id){

    }

    /**
     * Возвращает информацию об указанном преподавателе ФИО + кафедра
     */
    private function get_professor_info($professor_id){
        $query = "SELECT prof.professor,list.depart_name FROM professors as prof,departments_list as list WHERE prof.department_id = list.id and prof.id=?s";
        $result=$this->database->getRow($query,$professor_id);
        return $result;
    }

    /** Возвращает список преподавателей уникальный id + фио + кафедра */
    function get_professors_list(){
        $query = "SELECT prof.id,prof.professor,list.depart_name FROM professors as prof,departments_list as list WHERE prof.department_id = list.id";
        $result=$this->database->getALL($query);
        return $result;
    }

    /**
     * Возвращает рассписание на неделю (числитель + знаменатель)
     * @param $group_number
     * @return mixed
     * числитель,знаменатель{
     *  дни недели{
     *      номер пары{
     *          название пары
     *          имя преподавателя
     *      }
     *  }
     * }
     */
    function get_week_timetable($group_number)
    {
        $timetable['even']=$this->week_timetable($group_number,'ch');
        $timetable['uneven']=$this->week_timetable($group_number,'zn');
        return $timetable;
    }

    /**
     * Получение рассписания на неделю (6 дней пн-сб) с учетом нумератора недели
     * @param string $group_number
     * @param string $numerator
     * @return mixed - массив с рассписанием на неделю
     * дни недели{
     *  номер пары{
     *      название пары
     *      имя преподавателя
     *  }
     * }
     */
    private function week_timetable($group_number,$numerator){
        $query = "SELECT * FROM groups,professors WHERE groups.professor_id=professors.id AND group_number=?s AND (numerator=?s OR numerator='all') ";
        $query_week = $this->database->getAll($query,$group_number,$numerator);

        for ($i=1;$i<=6;$i++)
            $week[$i]=array();

        foreach ($query_week as $value){
            $day = $value['day_number'];
            $week[$day][]=$value;
        }
        foreach($week as &$value)
            $value = $this->parse_timetable($value, true); // приведение списка к пронумерованному виду пар

        $max_min =$this->week_max_min($week);
        $week['max'] =$max_min['max'];
        $week['min'] = $max_min['min'];
        return $week;
    }

    private function week_max_min($week)
    {
        $max = 1;
        $min = 7;
        foreach ($week as $days)
            foreach ($days as $key => $lesson_num)
                if ($lesson_num != null) {
                    $max = max($max, $key);
                    $min = min($min, $key);
                }
        $result['max']=$max;
        $result['min']=$min;
        return $result;
    }

    /** Callback функция для сортировки списка групп по их номеру */

    private function Groups_Sort_CallBack($a, $b) {
        if ($a['group_number'] == $b['group_number']) {
            return 0;
        }
        return ($a['group_number'] < $b['group_number']) ? -1 : 1;
    }

    /**
     * Получает список всех групп (курс группы).
     * Сортирует его согласно номер групп по возрастанию
     * @return array с номер группы и курсом
     */
    function get_list_group(){
        $result=$this->database->getALL("SELECT group_number,grade FROM groups_list");
        //сортировка полученного списка в соответсвии с их номером по возрастанию
        usort($result,array($this,'Groups_Sort_CallBack'));
        return $result;
    }

    /**
     * возвращает рассписание на сегодня и на след учебный день
     * @param int $group_number номер группы
     * @return mixed -
     * сегодня,завтра{
     *  номер пары{
     *      название пары
     *      имя преподавателя
     *      аудитория
     *      состояние пары
     *  }
     * }
     */
    function get_actual_dashboard($group_number){

        $day= date('w'); //получение номера дня в неделе
        $numerator = $this->get_week_numerator(); // получение значения нумератора для текущей недели

        $query = "SELECT * FROM groups,professors WHERE groups.professor_id=professors.id AND group_number=?s AND day_number=?s AND (numerator=?s or numerator='all')";

        if ($day ==0)
            $day=6;

        $result_today=$this->database->getAll($query,$group_number,$day,$numerator);

        if ($day==6 || $day == 0)
            $day=1;
        else
            $day++;

        $result_tomorrow = $this->database->getAll($query,$group_number,$day,$numerator);

        $result['today']=$this->parse_timetable($result_today);
        $result['tomorrow']=$this->parse_timetable($result_tomorrow);
        return $result;
    }

    /**
     * Формирует массив с пронумерованными парами и содержаем внутри их
     * @param $dashboard - массив с рассписание группы на выбранный день
     * полученный из базы данных
     * @param bool|false $isweek - при рассписании на неделю не отображает состояние пар и аудитории
     * @return mixed - массив приведенный к виду отображаемому в приложении
     * название пары
     * имя преподавателя
     * аудитория
     * состояние пары (идет сейчас пара/перемена(следующая пара становится активной) или пары кончились/прошли ).
     */
    private function parse_timetable($dashboard,$isweek = false){

        for ($i=1;$i<=7;$i++)   // Всего 7 пар
            $result[$i]=null;
        $lesson_exist=false;
        foreach ($dashboard as $value) {
            $num = $value['lesson_number'];
            $result[$num]['lesson_name'] = $value['lesson_name'];
            $result[$num]['professor'] = $value['professor'];

            if ($isweek == false) {         // если рассписание не на неделю, требуется состояние пар
                $result[$num]['classroom'] = $value['classroom'];
                //Определяет идет ли сейчас пара
                $is_lesson_going = $this->is_lesson_going($num);
                $result[$num]['state'] = $is_lesson_going;

                if ($is_lesson_going == true) // если существует пара которая в данный момент идет
                    $lesson_exist = true;
            }
        }
        if ($lesson_exist == false & $isweek == false)  // если пары на текуший момент времени не существует возможны 2 варианта
            foreach ($dashboard as $value){             // Пары на сегодня прошли || сейчас перемена
                $num = $value['lesson_number'];
                $result[$num]['state'] = $this->is_rest($num);
            }
        return $result;
    }

    /**
     * Определяет идет пара или нет
     * @param $lesson_number - номер пары
     * @return bool true - пара сейчас идет
     *              false - пара не идет (прошла/будет)
     */
    private function is_lesson_going($lesson_number){
        $result=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number);
        $end_of_lesson=$result['end_time'];
        $start_of_lesson = $result['start_time'];
        $now_time = date("G:i:s");
        if ($end_of_lesson>=$now_time & $start_of_lesson<=$now_time)
            return true;
        else
            return false;
    }

    /**
     * Определяет идет ли переменна перед выбранной парой.
     * @param $lesson_number - номер следующей пары
     * @return bool true - сейчас перемена
     *              false - пара идет/ пары закончились
     */
    private function is_rest($lesson_number){
        $time_of_lesson=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number);
        $start_of_lesson = $time_of_lesson['start_time'];
        $now_time = date("G:i:s");
        if ($lesson_number == 0 & $start_of_lesson >= $now_time) // время перед 1 парой считаем как перемену
            return true;
        $previous_time_of_lesson=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number-1);
        $end_of_previous_lesson = $previous_time_of_lesson['end_time'];
        if ($start_of_lesson >= $now_time & $now_time>=$end_of_previous_lesson)
            return true;
        else
            return false;
    }

    /**
     * Получение значения нумератора текущей недели
     * @return mixed
     */
    private function get_week_numerator(){
        $week = date('W'); //Дата недели с начала года
        if ($week % 2 ==0)
            $result = $this->database->getOne("SELECT even FROM Config");
        else
            $result = $this->database->getOne("SELECT uneven FROM Config");
        return $result;
    }

    /**
     * Возвращает номер пары по указанному времени
     * @param $time - Gis
     * @return mixed  - int номер текущей или следующей пары(перемена)
     *                - bool false - пар нету
     */
    private function get_lesson_number_by_time($time){
        $query = "SELECT num_lesson FROM timetable WHERE start_time <= ?s and end_time >= ?s";
        $result =  $this->database->getCol($query,$time,$time);

        if (count($result) > 0)     // Если сейчас идет какая-то пара то возвращаем ее номер
            return $result[0];
        else{                       //Возможно идет перемена или пар нету
            $result = $this->database->getAll("SELECT * FROM timetable");
            for ($i = 0; $i<6;$i++) {
                if ($i = 0 & $result[$i]["start_time"] >= $time)  // считаем время перед первой парой переменой
                    return $result[$i]["num_lesson"];
                if ($result[$i]["end_time"] <= $time & $result[$i+1]["start_time"] >= $time)
                    return $result[$i+1]["num_lesson"];
            }
            return false;           // Пар на сегодня нету
        }

    }
}
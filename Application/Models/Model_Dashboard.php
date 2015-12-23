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
     * Получает список всех групп (курс группы).
     * @return array с номер группы и курсом
     */
    function get_list_group(){
        $result=$this->database->getALL("SELECT group_number,grade FROM groups_list");
        return $result;
    }

    /**
     * @param int $group_number номер группы
     * @return mixed - возвращает рассписание на сегодня и на след учебный день
     * структура:
     */
    function get_actual_dashboard($group_number){

        $day= date('w'); //получение номера дня в неделе
        $numerator = $this->get_week_numerator(); // получение значения нумератора для текущей недели

        $query = "SELECT * FROM groups WHERE group_number=?s AND day_number=?s AND (numerator=?s or numerator='all')";

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
     * @param $dashboard - массив с рассписание группы на выбранный день
     * полученный из базы данных
     * @return mixed - массив приведенный к виду отображаемому в приложении
     */
    private function parse_timetable($dashboard){

        for ($i=1;$i<=7;$i++)   // Всего 7 пар
            $result[$i]=null;

        foreach ($dashboard as $value){
            $num=$value['lesson_number'];
            $result[$num]['lesson_number']=$num;
            $result[$num]['lesson_name'] = $value['lesson_name'];
            $result[$num]['professor']=$value['professor'];
            $result[$num]['classroom']=$value['classroom'];
            //Определяет прошла ли пара в реальном времени
            $result[$num]['state']=$this->lesson_state($num);
        }
        return $result;
    }

    /**
     * Определяет прошла ли пара, или нет
     * @param $lesson_number - номер пары
     * @return bool true - пары не было
     *              false - пара прошла
     */
    function lesson_state($lesson_number){
        $result=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number);
        $end_of_lesson=$result['end'];
        $now_time = date("G:i:s");
        if ($end_of_lesson<=$now_time)
            return false;
        else
            return true;
    }

    /**
     * Получение значения нумератора текущей недели
     * @return mixed
     */
    function get_week_numerator(){
        $week = date('W'); //Дата недели с начала года
        if ($week % 2 ==0)
            $result = $this->database->getOne("SELECT even FROM Config");
        else
            $result = $this->database->getOne("SELECT uneven FROM Config");
        return $result;
    }

}
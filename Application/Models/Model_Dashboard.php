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

    /**
     * Получает список всех групп (курс группы).
     * @return array с номер группы и курсом
     */
    function get_list_group(){
        $result=$this->database->getALL("SELECT group_number,grade FROM groups_list");
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

            if ($isweek == false) {         // если рассписание не на неделю требуется состояние пар
                $result[$num]['classroom'] = $value['classroom'];
                //Определяет идет ли сейчас пара
                $is_lesson_going = $this->is_lesson_going($num);
                $result[$num]['state'] = $is_lesson_going;

                if ($is_lesson_going == true) // если существует пара которая в данный момент идет
                    $lesson_exist = true;
            }
        }
        if ($lesson_exist == false & $isweek == false)                // если пары на текуший момент времени не существует
            foreach ($dashboard as $value){        // Пары на сегодня прошли || сейчас перемена
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
        $end_of_lesson=$result['end'];
        $start_of_lesson = $result['start'];
        $now_time = date("G:i:s");
        if ($end_of_lesson>=$now_time & $start_of_lesson<=$now_time)
            return true;
        else
            return false;
    }
    /**
     * Определяет сейчас перемена
     * @param $lesson_number - номер пары
     * @return bool true - сейчас перемена
     *              false - пара идет/ пары закончились
     */
    private function is_rest($lesson_number){
        $time_of_lesson=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number);
        $start_of_lesson = $time_of_lesson['start'];
        $now_time = date("G:i:s");
        if ($lesson_number == 0 & $start_of_lesson >= $now_time)
            return true;
        $previous_time_of_lesson=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number-1);
        $end_of_previous_lesson = $previous_time_of_lesson['end'];
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

}
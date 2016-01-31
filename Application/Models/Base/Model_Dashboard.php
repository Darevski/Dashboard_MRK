<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 22.09.15
 * Time: 14:29
 * @author Darevski
 */

namespace Application\Models\Base;
use Application\Core;

/**
 * Класс связанный с вспомогательным функционалом
 *
 * Class Model_Dashboard
 * @package Application\Models\Base
 * @see Application\Models\Model_Professors
 * @see Application\Models\Model_Timetable
 * @see Application\Models\Model_List_Groups
 * @see Application\Models\Model_Notifications
 *
 */
class Model_Dashboard extends Core\Model
{
    // переменная для хранения объекта содержащего методы работы с Датами и временем
    protected $date_time_model;

    /**
     * Создает объект для работы с датой и временем
     */
    public function __construct(){
        parent::__construct();
        $this->date_time_model = new Model_Date_Time();
    }
    /**
     * Определяет идет пара или нет
     * @param integer $lesson_number номер пары
     * @return bool true - пара сейчас идет
     *              false - пара не идет (прошла/будет)
     */
    protected function is_lesson_going($lesson_number){
        $result=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number);
        $end_of_lesson=$result['end_time'];
        $start_of_lesson = $result['start_time'];
        $now_time = date("H:i:s");
        if ($end_of_lesson>=$now_time & $start_of_lesson<=$now_time)
            return true;
        else
            return false;
    }

    /**
     * Определяет идет ли переменна перед выбранной парой.
     * @param integer $lesson_number номер пары
     * @return bool true - сейчас перемена
     *              false - пара идет/ пары закончились
     */
    protected function is_rest($lesson_number){
        $time_of_lesson=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number);
        $start_of_lesson = $time_of_lesson['start_time'];
        $now_time = date("H:i:s");
        $previous_time_of_lesson=$this->database->getRow("SELECT * FROM timetable WHERE num_lesson=?s",$lesson_number-1);
        $end_of_previous_lesson = $previous_time_of_lesson['end_time'];
        if ($start_of_lesson >= $now_time & $now_time>=$end_of_previous_lesson)
            return true;
        else
            return false;
    }

    /**
     * Возвращает номер пары по указанному времени
     * @param string $time - His
     * @return mixed integer | bool номер текущей или следующей пары(перемена) | false - пар нету
     */
    protected function get_lesson_number_by_time($time){
        $query = "SELECT num_lesson FROM timetable WHERE start_time <= ?s and end_time >= ?s";
        $result =  $this->database->getCol($query,$time,$time);

        if (count($result) > 0)     // Если сейчас идет какая-то пара то возвращаем ее номер
            return $result[0];
        else{                       //Возможно идет перемена или пар нету
            $result = $this->database->getAll("SELECT * FROM timetable");
            for ($i = 0; $i<6;$i++) {
                if ($i == 0 & $result[$i]["start_time"] >= $time)  // считаем время перед первой парой переменой
                    return $result[$i]["num_lesson"];
                if ($result[$i]["end_time"] <= $time & $result[$i+1]["start_time"] >= $time)
                    return $result[$i+1]["num_lesson"];
            }
            return false;           // Пар на сегодня нету
        }

    }

    /**
     * Поиск максимальной и минимальной пары на неделе
     * @param array $week - массив рассписания занятий на неделюы
     * @result array max,min максимальная и минимальная пара на неделе
     */
    protected function week_max_min($week)
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
     * Возвращает время начала и конца выбранной пары
     * @param integer $number_lesson
     * @return array|FALSE
     */
    protected function lesson_begin_end_time($number_lesson){
        $query = "SELECT start_time,end_time FROM timetable WHERE num_lesson=?s";
        $result =  $this->database->getRow($query,$number_lesson);
        return $result;
    }
}
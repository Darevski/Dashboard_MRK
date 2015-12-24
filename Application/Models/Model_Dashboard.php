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
     * Определяет идет пара или нет
     * @param $lesson_number - номер пары
     * @return bool true - пара сейчас идет
     *              false - пара не идет (прошла/будет)
     */
    protected function is_lesson_going($lesson_number){
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
    protected function is_rest($lesson_number){
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
     * @return string - ch/zn
     */
    protected function get_week_numerator(){
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
    protected function get_lesson_number_by_time($time){
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
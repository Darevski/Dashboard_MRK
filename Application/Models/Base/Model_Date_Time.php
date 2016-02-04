<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 27.01.16
 * Time: 19:08
 */

namespace Application\Models\Base;
use Application\Core\Model;
use DateTime;

/**
 * Класс отвечающий за даты в системе рассписания
 * Class Model_Date_Time
 * @package Application\Models\Base
 */

class Model_Date_Time extends Model
{
    /**
     * Возвращает название дня по его номеру
     * @param int $day
     * @return string
     */
    public function get_name_day($day){
        $day_name = array("Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота");
        return $day_name[$day];
    }

    /**
     * Возвращает день недели на сегодня/завтра
     * Если день 6 (суббота) - возвращается 1(понедельник) для следующего дня и 6 для сегодняшнего
     * Если воскресенье - возврашается 1(понедельник) для текущего дня и 2 (вторник) для следующего
     *
     * @return mixed $date [today,tomorrow]
     */
    public function get_day(){
        $day = (int)date('w');
        $date['today']=$day;
        if ($day == 0){
            $date['today']=1;
            $date['tomorrow']=2;
        }
        else if ($day == 6)
            $date['tomorrow']=1;
        else
            $date['tomorrow']=$day+1;
        return $date;
    }

    /**
     * Получение значения нумератора текущей недели
     * @return string - ch/zn
     */
    public function get_week_numerator(){
        $week = date('W'); //Дата недели с начала года
        if ($week % 2 ==0)
            $result = $this->database->getOne("SELECT even FROM Config");
        else
            $result = $this->database->getOne("SELECT uneven FROM Config");
        return $result;
    }

    /**
     * Проверяет Дату на валидность указанному формату
     * @param string $date
     * @param string $format по умолчанию Y-m-d
     * @return bool
     */
    public function validateDate($date,$format = 'Y-m-d')
    {
        $date_format = DateTime::createFromFormat($format, $date);
        return $date_format && $date_format->format($format) == $date;
    }

    /**
     * Проверяет есть ли занятия у выбранной группы на выбранную дату
     * @param $date - дата в формате GIS
     * @return bool false - если занятий нету, true - если занятия есть
     */
    public function is_lessons_today($group_number,$date){
        $query = "SELECT * FROM holidays WHERE (group_number=?s or group_number ='all') and date = ?s";
        $result=$this->database->getAll($query,$group_number,$date);
        $count_rows = count($result);
        if ($count_rows >0)
            return false;
        else
            return true;
    }
}
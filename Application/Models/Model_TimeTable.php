<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 27.11.15
 * Time: 1:10
 * @author Darevski
 */

namespace Application\Models;

/**
 * Класс логики связанный с отображением рассписания, уведомлений и т.д. занятий выбранных групп
 * Class Model_TimeTable
 * @package Application\Models
 */
class Model_TimeTable extends Model_Dashboard
{
    /**
     * Возвращает массив с уведомлениями для выбранной группы
     * состояние уведомления
     * Сортировка по дате, в начале новейшие
     * @param integer $number_group
     * @return array {string 'state' critical|warning|info , string text}
     */
    function get_notification_for_group($number_group){
        $today = date("Ymd");
        $query = "SELECT state,text,starting_date FROM notification WHERE (group_number=?s or group_number=0) and ending_date>=$today";
        $result_of_query = $this->database->getAll($query,$number_group);
        // сортируем по дате добавления в начале новейшие
        usort($result_of_query,array($this,'Notifications_Sort_by_date_CallBack'));
        foreach ($result_of_query as $value)
        {
            $array_temp['state']=$value['state'];
            $array_temp['text']=$value['text'];
            $result[]=$array_temp;
        }
        return $result;
    }

    /**
     * Возвращает рассписание на 2 недели (числитель + знаменатель + all)
     * Возвращаемая структура {string 'even'/'uneven' {
     *
     * @param integer $number_group
     * @return array
     * integer day {
     *
     * integer lesson_number | null {
     * - string lesson_name
     * - string professor_name
     * } } }
     *
     */
    function get_week_timetable($number_group)
    {
        $timetable['even']=$this->week_timetable($number_group,'ch');
        $timetable['uneven']=$this->week_timetable($number_group,'zn');
        return $timetable;
    }

    /**
     * Получает список всех групп (курс группы).
     * Сортирует его согласно номеру групп по возрастанию
     * @return array номер группы + курс
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
     * сегодня,завтра {
     *  номер пары {
     *      название пары,
     *      имя преподавателя,
     *      аудитория,
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

    /**
     * Поиск максимальной и минимальной пары на неделе
     * @param array $week - массив рассписания занятий на неделюы
     * @result array max,min максимальная и минимальная пара на неделе
     */
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
     * Формирует массив с пронумерованными парами и содержаем внутри их
     * @param $dashboard - массив с рассписание группы на выбранный день
     * полученный из базы данных
     * @param bool|false $isweek - при рассписании на неделю не отображает состояние пар и аудитории
     * @return mixed - массив приведенный к виду отображаемому в приложении
     *
     * название пары,
     * имя преподавателя,
     * аудитория,
     * состояние пары (идет сейчас пара/перемена(следующая пара становится активной) или пары кончились/прошли ).
     */
    private function parse_timetable($dashboard,$isweek = false){
        $result = null;
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
     * Callback функция для сортировки списка групп по их номеру
     * @param array $a ячейка группы
     * @param array $b ячейка группы
     * @return int результат сравнения номера группы
     */
    private function Groups_Sort_CallBack($a, $b) {
        if ($a['group_number'] == $b['group_number']) {
            return 0;
        }
        return ($a['group_number'] < $b['group_number']) ? -1 : 1;
    }

    /**
     * Callback функция для сортировки уведомлений по дате их старта
     * @param array $a ячейка уведомления
     * @param array $b ячейка уведомления
     * @return int результат дат уведомлений
     */
    private function Notifications_Sort_by_date_CallBack($a,$b){
        if ($a['starting_date'] == $b['starting_date']) {
            return 0;
        }
        return ($a['starting_date'] > $b['starting_date']) ? -1 : 1;
    }
}
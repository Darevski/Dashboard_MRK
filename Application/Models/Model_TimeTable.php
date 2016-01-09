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
     * По ввведеным данным(номер группы, номер пары), выводит информацию о паре
     * @param integer $number_group
     * @param integer $lesson_number
     * Содердит
     * - кабинет,
     * - название пары,
     * - кафедру,
     * - ФИО преподаваеля,
     * - url фото преподавателя,
     * - время пары
     * - bool multiple при наличии нескольких преподавателей ведущих пары одновременно у одной группы - true
     */
    function get_lesson_info_by($number_group,$lesson_number){
        $today = $this->get_day()['today'];
        $numerator = $this->get_week_numerator();
        $query = "SELECT * FROM groups,professors,departments_list WHERE groups.professor_id=professors.id AND
        professors.department_id = departments_list.id AND group_number=?s AND day_number=?s AND lesson_number=?s
        AND (numerator='all' or numerator=?s)";
        $result_of_query = $this->database->getALL($query,$number_group,$today,$lesson_number,$numerator);

        //Разбор полученного запроса (преподаватели, которые ведут у группы одновремеенно)
        foreach($result_of_query as $value){
            // одинаковые поля у всех преподавателей
            $result['lesson_name'] = $value['lesson_name'];
            $result['department'] = $value['depart_name'];

            // поля, различные у разных преподавателей
            // если преподавателей больше 2 выводится массивы из соответствующих параметров
            if (count($result_of_query)>1){
                $result['professor_id'][]=$value['professor_id'];
                $result['classroom'][] = $value['classroom'];
                $result['professor'][] = $value['professor'];
                $result['multiple'] = true;
            }
            else{
                // если преподавателей = 1 или 0 то выводится его параметры или null для всех свойств
                $result['professor_id']=$value['professor_id'];
                $result['classroom'] = $value['classroom'];
                $result['professor'] = $value['professor'];
                $result['photo_url'] = $value['photo_url'];
                $result['multiple'] = false;
            }

        }
        $start_end_time=$this->lesson_begin_end_time($lesson_number);
        $result['time']=date('G:i',strtotime($start_end_time['start_time'])).' - '.date('G:i',strtotime($start_end_time['end_time']));
        return $result;
    }

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
     * возвращает рассписание на сегодня и на след учебный день
     * @param int $group_number номер группы
     * @return mixed -
     * сегодня,завтра {
     *  название дня недели,
     *  номер пары {
     *      название пары,
     *      имя преподавателя,
     *      аудитория,
     *      состояние пары
     *  }
     * }
     */
    function get_actual_dashboard($group_number){
        $numerator = $this->get_week_numerator(); // получение значения нумератора для текущей недели

        $query = "SELECT * FROM groups,professors WHERE groups.professor_id=professors.id AND group_number=?s AND day_number=?s AND (numerator=?s or numerator='all')";

        //Получение дней на сегодня и завтра
        $day=$this->get_day();
        $today = $day['today'];
        $tomorrow = $day['tomorrow'];

        $result_today=$this->database->getAll($query,$group_number,$today,$numerator);
        $result['today']=$this->parse_timetable($result_today);

        $result['today']['day_name'] = $this->get_name_day($today); // Получение названия дня

        $result_tomorrow = $this->database->getAll($query,$group_number,$tomorrow,$numerator);

        $result['tomorrow']=$this->parse_timetable($result_tomorrow);

        $result['tomorrow']['day_name'] = $this->get_name_day($tomorrow); // Получение названия дня

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
     * Формирует массив с пронумерованными парами и содержаем внутри их
     * @param $dashboard - массив с рассписание группы на выбранный день
     * полученный из базы данных
     * @param bool|false $isweek - при рассписании на неделю не отображает состояние пар и аудитории
     * @return mixed - массив приведенный к виду отображаемому в приложении
     *
     * название пары,ФИО преподавателя
     * аудитория,
     * состояние пары (идет сейчас пара/перемена(следующая пара становится активной) или пары кончились/прошли ).
     */
    private function parse_timetable($dashboard,$isweek = false){
        $result = null;
        for ($i=1;$i<=7;$i++)   // Всего 7 пар
            $result[$i]=null;
        $lesson_exist=false;
        usort($dashboard,array($this,'lessons_number_sort_CallBack')); // сортировка ноперов пар по возрастанию
        // проверка на то что пара сейчас идет
        foreach ($dashboard as $value) {
            $num = $value['lesson_number'];
            $result[$num]['lesson_name'] = $value['lesson_name'];
            $result[$num]['professor'] = $value['professor'];
            if ($isweek == false) {         // если рассписание не на неделю, требуется состояние пар
                //Определяет идет ли сейчас пара
                $is_lesson_going = $this->is_lesson_going($num);
                $result[$num]['state'] = $is_lesson_going;

                if ($is_lesson_going == true) // если существует пара которая в данный момент идет
                    $lesson_exist = true;
            }
        }
        // проверка на время перед парами
        if ($lesson_exist == false & $isweek == false & count($dashboard)>0){
            $lesson_number = $dashboard[0]['lesson_number'];
            $start_lesson_time = $this->lesson_begin_end_time($lesson_number);
            if ($start_lesson_time['start_time']>=date("H:i:s")){
                $result[$lesson_number]['state'] = true;
                $lesson_exist = true;
            }

        }
        // проверка на перемены перед парами
        if ($lesson_exist == false & $isweek == false)  // если пары на текуший момент времени не существует возможны 2 варианта
            foreach ($dashboard as $value){             // Пары на сегодня прошли || сейчас перемена
                $num = $value['lesson_number'];
                $result[$num]['state'] = $this->is_rest($num);
            }
        return $result;
    }

    /**
     * Callback функция для сортировки пар по возрастанию
     * @param array $a ячейка пары
     * @param array $b ячейка пары
     * @return int результат сравнения пар
     */
    private function lessons_number_sort_CallBack($a, $b) {
        return ($a['lesson_number'] < $b['lesson_number']) ? -1 : 1;
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
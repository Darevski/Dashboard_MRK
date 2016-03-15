<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 27.11.15
 * Time: 1:10
 * @author Darevski
 */

namespace Application\Models;

use Application\Models\Base\Model_Dashboard;
use Application\Exceptions\Models_Processing_Except;

/**
 * Класс логики связанный с отображением рассписания, уведомлений и т.д. занятий выбранных групп
 * Class Model_TimeTable
 * @package Application\Models
 */
class Model_TimeTable extends Model_Dashboard
{
    //ADMIN start!
    /**
     * Заносит в БД рассписание на определенный день
     * @param $group_number integer - номер групппы
     * @param $numerator string - нумератор недели "ch/zn/all"
     * @param $day_number integer - номер дня недели
     * @param $timetable array рассписани на день структура: [num_lesson:[prof_id,lesson_id]]
     * @throws Models_Processing_Except
     */
    function set_timetable_for_day($group_number,$numerator,$day_number,$timetable){
        // Проверка входных данных на валидность
        $groups_model = new Model_List_Groups();
        if (!is_int($group_number))
            throw new Models_Processing_Except("Номер группы - $group_number не является числом");

        else if (!$groups_model->isset_group($group_number))
            throw new Models_Processing_Except("Группы - $group_number не существует");

        else if ($numerator != 'all' && $numerator != 'zn' && $numerator != 'ch')
            throw new Models_Processing_Except("$numerator не может быть нумератором недели");
        // при не существовании дня недели
        else if (!is_int($day_number) || $day_number<0 || $day_number>6)
            throw new Models_Processing_Except("Значение $day_number не является днем недели");

        // Проверка массива рассписания на валидность значений
        $professors_model = new Model_Professors();
        $lessons_model = new Model_Lessons();
        foreach ($timetable as $item){
            if (!is_int($item['num_lesson']) || $item['num_lesson']<0 || $item['num_lesson']>8)
                throw new Models_Processing_Except("Не верное значение номера пары - ".$item['num_lesson']);
            else if (!is_int($item['prof_id']))
                throw new Models_Processing_Except("Идентификатор преподавателя ".$item['prof_id']." не являтся числом");
            else if (!is_int($item['lesson_id']))
                throw new Models_Processing_Except("Идентификатор предмета ".$item['lesson_id']." не являтся числом");
            else if (!$professors_model->isset_professor($item['prof_id']))
                throw new Models_Processing_Except("Преподавателя с индентификатором ".$item['prof_id']." не существует");
            else if (!$lessons_model->is_lesson_set($item['lesson_id']))
                throw new Models_Processing_Except("Предмета с идентификатором ".$item['lesson_id']." не существует");
        }

        //Удаление записей рассписания на указанный день

        //при нумераторе all удаляются все нумераторы
        if ($numerator == 'all'){
            $delete_query = "DELETE FROM groups WHERE group_number=?i and day_number=?i";
            $this->database->query($delete_query,$group_number,$day_number);
        }
        // при знаменателе или числителе удаляются только соответсвующие нумератору
        else{
            $delete_query = "DELETE FROM groups WHERE group_number=?i and numerator=?s and day_number=?i";
            $this->database->query($delete_query,$group_number,$numerator,$day_number);
        }

        $insert_query = "INSERT INTO groups (group_number,day_number,lesson_number,professor_id,lesson_id,numerator)
                          VALUES (?i,?i,?i,?i,?i,?s)";
        foreach ($timetable as $lesson)
            $this->database->query($insert_query,$group_number,$day_number,$lesson['num_lesson'],
                $lesson['prof_id'],$lesson['lesson_id'],$numerator);

        $result['state'] = 'success';
        return $result;
    }
    //ADMIN end!

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
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws Models_Processing_Except
     */
    function get_lesson_info_by($number_group,$lesson_number)
    {
        $groups_model = new Model_List_Groups();
        if (!is_int($number_group))
            throw new Models_Processing_Except("Номер группы - $number_group не является числом");

        else if (!$groups_model->isset_group($number_group))
            throw new Models_Processing_Except("Группы - $number_group не существует");

        else if (!is_int($lesson_number))
            throw new Models_Processing_Except("Номер пары - $lesson_number не является числом");

        $today = $this->date_time_model->get_day()['today'];
        $numerator = $this->date_time_model->get_week_numerator();

        $query = "SELECT * FROM groups,professors,departments_list,lessons_list WHERE groups.professor_id=professors.id AND
                  professors.department_code = departments_list.code AND groups.lesson_id=lessons_list.id
                   AND group_number=?s AND day_number=?s AND lesson_number=?s AND (numerator='all' OR numerator=?s)";
        $result_of_query = $this->database->getALL($query, $number_group, $today, $lesson_number, $numerator);

        //Разбор полученного запроса (преподаватели, которые ведут у группы одновремеенно)
        foreach ($result_of_query as $value) {
            // одинаковые поля у всех преподавателей
            $result['lesson_name'] = $value['lesson_name'];
            $result['department'] = $value['depart_name'];

            // поля, различные у разных преподавателей
            // если преподавателей больше 2 выводится массивы из соответствующих параметров
            if (count($result_of_query) > 1) {
                $result['professor_id'][] = $value['professor_id'];
                $result['classroom'][] = $value['classroom'];
                $result['professor'][] = $value['professor_name'];
                $result['multiple'] = true;
            }
            else {
                // если преподавателей = 1 или 0 то выводится его параметры или null для всех свойств
                $result['professor_id'] = $value['professor_id'];
                $result['classroom'] = $value['classroom'];
                $result['professor'] = $value['professor_name'];
                $result['photo_url'] = $value['photo_url'];
                $result['multiple'] = false;
            }

        }
        $start_end_time = $this->lesson_begin_end_time($lesson_number);
        $result['time'] = date('G:i', strtotime($start_end_time['start_time'])) . ' - ' . date('G:i', strtotime($start_end_time['end_time']));
        $result['state'] = 'success';

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
     * [state] = 'fail' // [state] ='success' && [message] = string ...
     * @throws Models_Processing_Except
     */
    function get_week_timetable($number_group){
        $groups_model = new Model_List_Groups();
        if (!is_int($number_group))
            throw new Models_Processing_Except("Номер группы - $number_group не является числом");

        else if (!$groups_model->isset_group($number_group))
            throw new Models_Processing_Except("Группы - $number_group не существует");

        $timetable['even'] = $this->week_timetable($number_group, 'ch');
        $timetable['uneven'] = $this->week_timetable($number_group, 'zn');
        $timetable['state'] = 'success';

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
     * [state] = 'fail' // [state] ='success' && [message] = string ...
     * @throws Models_Processing_Except
     */
    function get_actual_dashboard($group_number)
    {
        $groups_model = new Model_List_Groups();
        if (!is_int($group_number))
            throw new Models_Processing_Except("Номер группы - $group_number не является числом");


        else if (!$groups_model->isset_group($group_number))
            throw new Models_Processing_Except("Группы - $group_number не существует");


        $numerator = $this->date_time_model->get_week_numerator(); // получение значения нумератора для текущей недели

        $query = "SELECT * FROM groups,professors,lessons_list WHERE groups.professor_id=professors.id AND group_number=?s
                            AND groups.lesson_id=lessons_list.id AND day_number=?s AND (numerator=?s OR numerator='all')";

        //Получение дней на сегодня и завтра
        $day = $this->date_time_model->get_day();
        $today = $day['today'];
        $tomorrow = $day['tomorrow'];

        $result_today = $this->database->getAll($query, $group_number, $today, $numerator);
        $result['today'] = $this->parse_timetable($result_today);

        $result['today']['day_name'] = $this->date_time_model->get_name_day($today); // Получение названия дня

        $result_tomorrow = $this->database->getAll($query, $group_number, $tomorrow, $numerator);

        $result['tomorrow'] = $this->parse_timetable($result_tomorrow);

        $result['tomorrow']['day_name'] = $this->date_time_model->get_name_day($tomorrow); // Получение названия дня

        $result['state'] = 'success';


        return $result;
    }

    /**
     * Возвращает массив дат по которым проходят занятия на ближайший месяц
     * @param integer $group_number
     * @return mixed
     * [state] = 'success' || [state] = 'fail' && [message] = string ...
     * @throws Models_Processing_Except
     */
    function get_working_days_group_for_month($group_number)
    {
        $groups_model = new Model_List_Groups();
        if (!is_int($group_number))
            throw new Models_Processing_Except("Номер группы - $group_number не является числом");

        else if (!$groups_model->isset_group($group_number))
            throw new Models_Processing_Except("Группы - $group_number не существует");

        $result['days'] = [];
        for ($i = 0; $i < 31; $i++) {
            $date = date("Y-m-d", strtotime(date("Y-m-d") . '+' . $i . 'day'));
            if ($this->date_time_model->is_lessons_today($group_number, $date))
                $result['days'][] = $date;
        }
        $result['state'] = 'success';

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
        $query = "SELECT * FROM groups,professors,lessons_list WHERE groups.lesson_id=lessons_list.id AND
                  groups.professor_id=professors.id AND group_number=?s AND (numerator=?s OR numerator='all')";
        $query_week = $this->database->getAll($query,$group_number,$numerator);
        $week = array();
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
            $result[$num]['professor'] = $value['professor_name'];
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
}
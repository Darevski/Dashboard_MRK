<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 09.01.16
 * Time: 17:52
 */

namespace Application\Models;

use Application\Models\Base\Model_Dashboard;
use Application\Exceptions\Models_Processing_Except;

/**
 * Class Model_Notifications
 * Класс логики связанный с уведомлениями
 * @package Application\Models
 */
class Model_Notifications extends Model_Dashboard
{
    /**
     * Добавляет уведомление в базу данных
     * @param string $type
     * @param integer $target 0 - for all groups
     * @param string $text
     * @param string $ending_date в формате Y-m-d // string "tomorrow" - подставляется дата следующего дня
     * @return mixed [state] = 'success' // [state] = 'fail' && [message] = string ...
     * @throws Models_Processing_Except
     */
    public function add_notification($type,$target,$text,$ending_date)
    {
        $groups_model = new Model_List_Groups();
        //Проверка на валидности даты
        if (!$this->date_time_model->validateDate($ending_date, 'Y-m-d') && $ending_date !== "tomorrow")
            throw new Models_Processing_Except('Задана некорректная дата');

        //При дате меньше текущей на сервере инициализация ошибки
        else if ($ending_date < date("Y-m-d") && $ending_date != "tomorrow")
            throw new Models_Processing_Except('Дата окончания не может быть меньше текущей даты');

        else if (!is_int($target))
            throw new Models_Processing_Except("Номер группы - $target не является числом");

        // проверка на существование группы
        else if ($target != 0 && !$groups_model->isset_group($target))
            throw new Models_Processing_Except("Группы - $target не существует");

        $today_date = date("Y-m-d");
        // При флаге равном 'tomorrow' устанавливается дата следующего дня
        if ($ending_date === "tomorrow")
            $ending_date = date("Y-m-d", strtotime($today_date . '+1 day'));

        $query = "INSERT INTO notification SET state=?s,group_number=?i,text=?s,starting_date=?s,ending_date=?s";
        $this->database->query($query, $type, $target, $text, $today_date, $ending_date);
        $result['state'] = 'success';

        return $result;
    }

    /**
     * Удаляет уведомления из БД по идентификатору
     * @param integer $id
     * @return array состояние выполнения удаления [state] = 'fail' // [state] ='success' && [message] = string ...
     * @throws Models_Processing_Except
     */
    public function delete_notification($id)
    {
        if (!is_int($id))
            throw new Models_Processing_Except("Идентификатор $id не является числом");

        //проверка на существование записи в БД
        $test_query = "SELECT * FROM notification WHERE id = ?i";
        if (count($this->database->getAll($test_query, $id)) <= 0)
            throw new Models_Processing_Except("Уведомления с идентификатором - $id не существует");

        //непосредственное удаление строки из бд
        $delete_query = "DELETE FROM notification WHERE id = ?i";
        $this->database->query($delete_query, $id);
        $result['state'] = 'success';

        return $result;
    }

    /**
     * Возвращает список всех не просроченных уведомлений в БД
     * @return array 'notifications' {
     *  integer id;
     *  string state;
     *  integer group_number;
     *  date starting_date,ending_date Y-m-d;
     * }
     * [state] = 'success'
     */
    public function get_active_notifications(){
        $query = "SELECT * FROM notification WHERE ending_date>=?s";
        $today = date('Ymd');
        $result = $this->database->getAll($query,$today);
        usort($result,array($this,'Notifications_Sort_by_date_CallBack'));
        $result['state']='success';
        return $result;
    }

    /**
     * Возвращает массив с уведомлениями для выбранной группы
     * состояние уведомления
     * Сортировка по дате, в начале новейшие
     * @param integer $number_group
     * @return array {string 'state' critical|warning|info , string text} [state] = 'success' || [state] = 'fail' && [message] = string
     * @throws Models_Processing_Except
     */
    public function get_notification_for_group($number_group)
    {
        $groups_model = new Model_List_Groups();
        if (!is_int($number_group))
            throw new Models_Processing_Except("Номер группы - $number_group не является числом");

        else if (!$groups_model->isset_group($number_group))
            throw new Models_Processing_Except("Группы - $number_group не существует");

        $today = date("Ymd");
        // Получение уведомлений для указанной группы, дата которых больше сегодняшней (уведомления актуальны)
        $query = "SELECT state,text,starting_date,ending_date FROM notification WHERE (group_number=?i or
                                                                            group_number= 0) and ending_date>=?s";
        $result_of_query = $this->database->getAll($query, $number_group,$today);
        // сортируем по дате добавления в начале новейшие
        foreach ($result_of_query as $value) {
            $array_temp['state'] = $value['state'];
            $array_temp['text'] = $value['text'];
            $result[] = $array_temp;
        }
        $result['state'] = 'success';

        return $result;
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
<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 09.01.16
 * Time: 17:52
 */

namespace Application\Models;

use Application\Models\Base\Model_Dashboard;
class Model_Notifications extends Model_Dashboard
{
    /**
     * Добавляет уведомление в базу данных
     * @param string $type
     * @param integer $target 0 - for all groups
     * @param string $text
     * @param string $ending_date в формате Y-m-d // string "tomorrow" - подставляется дата следующего дня
     * @return mixed
     */
    public function add_notification($type,$target,$text,$ending_date)
    {
        //Проверка на валидности даты
        if ($this->date_time_model->validateDate($ending_date, 'Y-m-d') || $ending_date === "tomorrow"){
            if ($ending_date<date("Y-m-d")){
                $result['state'] = 'fail';
                $result['message'] = 'Дата окончания не может быть меньше текущей даты';
            }
            else{
                // При флаге равном tomorrow устанавливается дата следующего дня
                if ($ending_date === "tomorrow")
                    $ending_date = date("Y-m-d",strtotime(date("Y-m-d").'+1 day'));

                $today_date = date("Y-m-d");
                $query = "INSERT INTO notification SET state=?s,group_number=?i,text=?s,starting_date=?s,ending_date=?s";
                $this->database->query($query,$type,$target,$text,$today_date,$ending_date);
                $result['state'] = 'success';
            }
        }
        else{
            $result['state'] = 'fail';
            $result['message'] = 'Некорректная дата';
        }
        return $result;
    }

    /**
     * Удаляет уведомления из БД по идентификатору
     * @param integer $id
     * @return array состояние выполнения удаления string state = 'fail' /'success' && string message on fail
     */
    public function delete_notification($id){
        $test_query = "SELECT * FROM notification WHERE id = ?i";
        if (count($this->database->getAll($test_query,$id))>0){
            $delete_query = "DELETE FROM notification WHERE id = ?i";
            $this->database->query($delete_query,$id);
            $result['state'] = 'success';
        }
        else{
            $result['state'] = 'fail';
            $result['message'] = 'Уведомления с данным индефикатором не существует';
        }
        return $result;
    }

    /**
     * Возвращает список всех не просроченных уведомлений в БД
     * @return array 'notifications'
     */
    public function get_active_notifications(){
        $result=[];
        $query = "SELECT * FROM notification WHERE ending_date>=?s";
        $today = date('Ymd');
        $result = $this->database->getAll($query,$today);
        usort($result,array($this,'Notifications_Sort_by_date_CallBack'));
        return $result;
    }

    /**
     * Возвращает массив с уведомлениями для выбранной группы
     * состояние уведомления
     * Сортировка по дате, в начале новейшие
     * @param integer $number_group
     * @return array {string 'state' critical|warning|info , string text}
     */
    public function get_notification_for_group($number_group){
        $result=[];
        $today = date("Ymd");
        $query = "SELECT state,text,starting_date FROM notification WHERE (group_number=?s or group_number=0) and ending_date>=$today";
        $result_of_query = $this->database->getAll($query,$number_group);
        // сортируем по дате добавления в начале новейшие
        foreach ($result_of_query as $value)
        {
            $array_temp['state']=$value['state'];
            $array_temp['text']=$value['text'];
            $result[]=$array_temp;
        }
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
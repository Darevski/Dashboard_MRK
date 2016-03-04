<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 11:29
 */

namespace Application\Controllers\Admin;

use Application\Controllers\Controller_admin;
use Application\Exceptions\UFO_Except;

/**
 * Class Controller_notifications
 * Контроллер отвечающий за функционал администратора связанный с уведомлениями
 * @package Application\Controllers\Admin
 */
class Controller_notifications extends Controller_admin
{
    /**
     * Добавление В базу данных нового уведомления
     * Входные данные через Post строку
     * parameters{
     *  string type = 'critical|warning|info'
     *  integer target = кому предназначены, 0 - для всех
     *  date ending_date - дата окончания уведомления в формате Y-m-d / string "tomorrow" - подставляется завтрашняя дата
     * }
     * string text - текст уведомления
     *
     * Ответ - mixed [state] = 'success' // [state] = 'fail' && [message] = string
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_add(){
        //$_POST['json_input'] = '{"parameters":{"type":"info","target":32494,"ending_date":"20170301"},"text":"123"}';

        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            // Удаление запрещенных символов
            $data = $this->secure_array($data);
            if (isset($data['parameters']['type']) && isset($data['parameters']['target']) && isset($data['text']) &&
                isset($data['parameters']['ending_date'])){
                $type = $data['parameters']['type'];
                $target =(integer)$data['parameters']['target'];
                $ending_date = $data['parameters']['ending_date'];
                $message = $data['text'];

                $result = $this->notification_model->add_notification($type, $target, $message, $ending_date);

                $this->view->output_json($result);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }

    /**
     * Удаление уведомления по его индексу
     * Входной индефикатор через POST строку json_input
     * integer id
     * Ответ - mixed [state] = 'success' // [state] = 'fail' && [message] = string
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_delete(){
        //$_POST['json_input'] = '{"id":22}';
        if (isset($_POST['json_input'])){
            $data = json_decode($_POST['json_input'],JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['id'])){
                $id = (integer) $data['id'];
                $result = $this->notification_model->delete_notification($id);
                $this->view->output_json($result);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }

    /**
     * Вывод списка активных уведомлений отсортированнного по дате создания (возрастание)
     * array 'notifications' {
     *  integer id;
     *  string state;
     *  integer group_number;
     *  date starting_date,ending_date Y-m-d;
     * }
     * [state] = 'success'
     * @api
     */
    function action_get_active(){
        $result = $this->notification_model->get_active_notifications();
        $this->view->output_json($result);
    }
}
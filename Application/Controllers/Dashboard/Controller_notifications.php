<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 11:39
 */

namespace Application\Controllers\Dashboard;
use Application\Controllers\Controller_dashboard;
use Application\Exceptions\UFO_Except;

/**
 * Class Controller_notifications
 * Отвечает за действия с уведомлениями для непривелигированного пользователя
 * @package Application\Controllers\Dashboard
 */
class Controller_notifications extends Controller_dashboard
{
    /**
     * Выводит Json строку содежащую уведомления для указанной группы
     *
     * Входной параметр через JSON строку(POST) integer 'group_number'
     *
     * Структура результата:
     * {string 'type' critical|warning|info , string text}
     * [state] = 'success' || [state] = 'fail' && [message] = string ....
     * @throws UFO_Except code 400 при неверных post данных или при их отсутвии
     * @api
     */
    function action_get_for_group(){
        //$_POST['json_input'] = '{"group_number":32791}';
        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['group_number'])) {
                $group_number = (integer)$data['group_number'];
                $notification = $this->notification_model->get_notification_for_group($group_number);
                $this->view->output_json($notification);
            }
            else
                throw new UFO_Except("Неверные параметры запроса",400);
        }
        else
            throw new UFO_Except("Данные запроса не обнаружены",400);
    }
}
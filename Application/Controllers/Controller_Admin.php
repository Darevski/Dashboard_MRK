<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 18:43
 * @author Darevski
 */

namespace Application\Controllers;
use Application\Core;
use Application\Exceptions\UFO_Except;
use Application\Models;

/**
 * Контроллер Администратора обеспечивает действия связанные с привелегией "администратор"
 * Class Controller_Admin
 * @package Application\Controllers
 */
class Controller_Admin extends Core\Controller{
    /**
     * @var Models\Model_Admin набор логики связанной с администратором
     */
    private $admin_model;
    /**
     * Проверка разрешения на доступ к данной информации по индефикатору пользователя
     * @throws UFO_Except при не совпадении индификатора пользователя вброс исключения с ошибкой доступа
     */
    function __construct(){
        parent::__construct();
        $this->validate();
        $this->admin_model = new Models\Model_Admin();
    }

    /**
     * Проверка наличия индификатора пользователя, проверка на соответсвиие индификатора значению Admin
     * @throws UFO_Except - при не совпадении вброс исключения, с сообщением о недоступности
     */
    private function validate(){
        // Получение значения привелегии
        $result = $this->state_authorization();
        if ($result !== 'Admin')
            throw new UFO_Except ('У вас не достаточно прав для просмотра данного контента', 403);
    }

    /**
     * Базовое действие контроллера
     */
    public function action_start(){
        $this->view->generate("Admin_View.php");
    }

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
    function action_add_notification(){
        //$_POST['json_input'] = '{"parameters":{"type":"info","target":32494,"ending_date":"tomorrow"},"text":"123"}';

        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);
            // Удаление запрещенных символов
            $data = $this->secure_array($data);
            if (isset($data['parameters']['type']) && isset($data['parameters']['target']) && isset($data['text']) &&
                isset($data['parameters']['ending_date'])){
                $type = $data['parameters']['type'];
                $target = $data['parameters']['target'];
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
    function action_delete_notification(){
        //$_POST['json_input'] = '{"id":22}';
        if (isset($_POST['json_input'])){
            $data = json_decode($_POST['json_input'],JSON_UNESCAPED_UNICODE);
            $data = $this->secure_array($data);
            if (isset($data['id'])){
                $id = $data['id'];
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
    function action_get_active_notifications(){
        $result = $this->notification_model->get_active_notifications();
        $this->view->output_json($result);
    }
}
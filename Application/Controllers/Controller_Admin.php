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
     * Проверка разрешения на доступ к данной информации по индефикатору пользователя
     * @throws UFO_Except при не совпадении индификатора пользователя вброс исключения с ошибкой доступа
     */
    function __construct(){
        parent::__construct();
        $this->validate();
        $this->model = new Models\Model_Admin();
        $this->list_group_model = new Models\Model_List_Groups();
        $this->notifications =new Models\Model_Notifications();
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
     * Добавление новой группу в список групп
     * Входные данные через JSON строку(POST) с полями integer 'grade' и 'group_number'
     *
     * Вывод результата выполнения array string state:success||fail , string message
     *
     * @api
     */
    function action_add_group(){
        //$_POST['json_input'] = '{"group_number":"32494","grade":"3"}';
        if (isset($_POST['json_input'])) {
            $data= json_decode($_POST['json_input'],JSON_UNESCAPED_UNICODE);
            if (isset($data['grade']) && isset($data['group_number'])) {

                $grade = $this->security_variable($data['grade']);
                $group_number = $this->security_variable($data['group_number']);

                $result = $this->list_group_model->group_add($grade, $group_number);

                //Вывод результата выполнения
                $this->view->output_json($result);
            }
        }
    }

    /**
     * Добавление В базу данных нового уведомления
     * Входные данные через Post строку
     * parameters{
     *  type = 'critical|warning|info'
     *  target = кому предназначены, 0 - для всех
     *  ending_date - дата окончания уведомления в формате Y-m-d / string "tomorrow" - подставляется завтрашняя дата
     * }
     * text - текст уведомления
     *
     * @api
     */
    function action_add_notification(){
        //$_POST['json_input'] = '{"parameters":{"type":"info","target":"32494","ending_date":"tomorrow"},"text":"123"}';

        if (isset($_POST['json_input'])) {
            $data = json_decode($_POST['json_input'], JSON_UNESCAPED_UNICODE);

            if (isset($data['parameters']['type']) && isset($data['parameters']['target']) && isset($data['text']) &&
                isset($data['parameters']['ending_date'])){

                $type = $this->security_variable($data['parameters']['type']);
                $target = $this->security_variable($data['parameters']['target']);
                $ending_date = $this->security_variable($data['parameters']['ending_date']);
                $message = $this->security_variable($data['text']);

                $result = $this->notifications->add_notification($type, $target, $message, $ending_date);

                $this->view->output_json($result);
            }
        }

    }
}
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
        $this->view->generate("Admin_view.php");
    }

    /**
     * Вывод Json строки, содержащей отсортированный по возраст список групп
     * integer group_number
     *
     * @api
     */
    function action_get_list_group_without_grade(){
        $list_group=$this->list_group_model->get_list_group_without_grade();
        $this->view->output_json($list_group);
    }

    /**
     * Добавление новой группу в список групп
     * Входные данные через integer Post 'grade' и 'group_number'
     *
     * Вывод результата выполнения array string state:success||fail , string message
     */
    function action_add_group(){
        $_POST['grade'] = 1;
        $_POST['group_number'] = 12494;
        if (isset($_POST['grade']) && isset($_POST['group_number'])){
            $grade = $this->security_variable($_POST['grade']);
            $group_number = $this->security_variable($_POST['group_number']);

            $result = $this->list_group_model->group_add($grade,$group_number);

            //Вывод результата выполнения
            $this->view->output_json($result);
        }
    }
}
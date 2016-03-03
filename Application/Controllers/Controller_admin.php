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
 * Class Controller_admin
 * @package Application\Controllers
 */
class Controller_admin extends Core\Controller{

    /**
     * Проверка разрешения на доступ к данной информации по идентификатору пользователя
     * @throws UFO_Except при не совпадении идентификатора пользователя вброс исключения с ошибкой доступа
     */
    function __construct(){
        parent::__construct();
        $this->validate();
    }

    /**
     * Проверка наличия идентификатора пользователя, проверка на соответсвиие идентификатора значению Admin
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
}
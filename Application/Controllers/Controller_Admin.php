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
       //$this->model->group_add(32494,'ch',1,2,'apanasevich','oaip');
    }
}
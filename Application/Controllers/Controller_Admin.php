<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 18:43
 */

namespace Application\Controllers;
use Application\Core;
use Application\Exceptions\UFO_Except;
use Application\Models;

class Controller_Admin extends Core\Controller{
    function __construct(){
        parent::__construct();
        $this->validate();
        $this->model = new Models\Model_Dashboard();
    }

    /**
     * Проверка на соответствие пользователя следующим критериям
     * 1) Пользователь входил в систему. В сессиях весит хэш и логин
     * 2) Хэш совпадает с хэшом в бд
     * 3) Пользователь имеет необходимые привелегии
     * В противном случае выбрасывается исключение
     * @throws UFO_Except
     */
    private function validate(){
        if (isset($_SESSION['login']) & isset($_SESSION['hash'])){
            $result=$this->auth_model->take_privilege($_SESSION['login'],$_SESSION['hash']);
            if ($result == false) { // при не совпадении очистка сесии
                $this->auth_model->clear_hash($_SESSION['login']);
                throw new UFO_Except ('Доступ заблокирован, несовпадение контрольного хэша, перезайдите в систему', 601);
            }
            if ($result !== 'Admin')
                throw new UFO_Except ('У вас не достаточно прав для просмотра данного контента', 403);
        }
        else
            throw new UFO_Except ('Доступ запрещен в раздел запрещен не авторизированным пользоваталям, войдите в систему',401);
    }
    public function action_start(){
       echo 'Теперь вы диктуете правила!';
    }
}
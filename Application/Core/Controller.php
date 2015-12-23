<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 */
namespace Application\Core;
use Application\Models;
use Application\Exceptions\UFO_Except;
class Controller {

    public $model;
    public $view;
    public $auth_model;

    function __construct()
    {
        $this->view = new View();
        $this->auth_model = new Models\Model_Auth();
    }
    protected function security_variable($variable){
        $variable=htmlentities($variable);
        $variable=strip_tags($variable);
        return $variable;
    }

    /**
     * Проверка на сущуствование сессии для пользователя
     * Проверка хэша пользователя, при не совпадении хэша очистка сессии в бд и на стороне клинета
     * @return bool|string
     * @throws UFO_Except
     */
    public function state_authorization(){
        if (isset($_SESSION['login']) & isset($_SESSION['hash'])) {
            $result = $this->auth_model->take_privilege($_SESSION['login'], $_SESSION['hash']);
            if ($result == false) { // при не совпадении очистка сесии в БД
                $this->auth_model->clear_hash($_SESSION['login']);
                throw new UFO_Except ('Доступ заблокирован, несовпадение контрольного хэша, перезайдите в систему', 601);
            }
            return $result;
        }
        else
            return false;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 03.03.16
 * Time: 19:43
 */

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Exceptions\UFO_Except;
/**
 * Class Controller_service
 * Вспомогательные функции
 * @package Application\Controllers
 */
class Controller_service extends Controller
{
    /**
     * Выводит json строку со временем на сервере
     * string now_time - unix timestamp
     * [state] = 'success'
     * @api
     */
    public function action_get_time(){
        $date['now_time'] = date("U");
        $date['state'] = 'success';
        $this->view->output_json($date);
    }

    /**
     * Функция входа
     * Обеспечивает запись в сессию данных пользователя, для последующей индентификации в системе
     * @throws UFO_Except при не верном пароле вброс исключения с ошибкой не прошедшей авторизации
     */
    function action_authorization(){
        if (isset ($_POST['login']) & isset($_POST['password'])) {
            $login = $this->security_variable($_POST['login']);
            $password = $this->security_variable($_POST['password']);
            $result = $this->auth_model->check_password($login,$password);
            if ($result === false)
                throw new UFO_Except("Не верный пароль",403);
            else{
                if (isset ($_POST['catch'])){
                    $_SESSION['login'] = $login;
                    $_SESSION['hash'] = $result;
                    $host =  $_SERVER['HTTP_HOST'];
                    header("Location: http://$host");
                }
            }
        }
    }
}
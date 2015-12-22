<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 30.09.15
 * Time: 14:34
 */

namespace Application\Controllers;
use Application\Core\Controller;
use Application\Exceptions\UFO_Except;
use Application\Models\Model_Auth;

class Controller_Auth extends Controller
{
    function __construct(){
        $this->model = new Model_Auth();
    }
    function action_enter(){
        if (isset ($_POST['login']) & isset($_POST['password'])) {
            $login = $this->security_variable($_POST['login']);
            $password = $this->security_variable($_POST['password']);
            $result = $this->model->check_password($login,$password);
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
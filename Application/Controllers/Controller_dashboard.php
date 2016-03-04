<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 9:21
 * @author Darevski
 */
namespace Application\Controllers;
use Application\Core;
use Application\Models;
use Application\Exceptions\UFO_Except;
/**
 * Контроллер базовых функций рассписания
 * Class Controller_dashboard
 * @package Application\Controllers
 */
class Controller_dashboard extends Core\Controller
{
    /**
     * Стартовое действие (по-умолчанию)
     */
    function action_start(){
        $this->view->generate("Dashboard_View.php");
    }
}
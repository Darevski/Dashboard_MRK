<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 16.09.15
 * Time: 11:47
 * @author Darevski
 */

namespace Application\Controllers;
use Application\Core;

/**
 * Обработчик ошибок связанных с недоступностью страниц, запрещенным доступом и т.д.
 * Class Controller_UFO
 * @package Application\Controllers
 */
class Controller_UFO extends Core\Controller
{
    /**
     * Вывод страницы с полученными данными
     * @param array $data содержит ошибки и данные для вывода
     */
    function display($data){
        $this->view->display('UFO_view.php',$data);
    }
}
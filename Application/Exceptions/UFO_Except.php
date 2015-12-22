<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 17:49
 */

namespace Application\Exceptions;
use Application\Controllers;

class UFO_Except extends \Exception
{
    function classificate_error(UFO_except $error){
        $code = $error->code;
        switch ($code) {
            case 404:
                $data['Error_status'] = '404 Bad Gateway';
                $data['Message'] = 'Увы такой страницы не существует';
                $data['Code'] = 404;
                break;
        }
        $controller = new Controllers\Controller_UFO();
        $controller->display($data);

    }
}
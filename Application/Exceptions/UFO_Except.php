<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 17:49
 */

namespace Application\Exceptions;
use Application\Controllers;

class UFO_Except extends Main_Except
{
    function classification_error(UFO_except $error){
        $code = $error->code;
        switch ($code) {
            case 404:   //Отсутсвие страницы
                $data['Error_status'] = '404 Bad Gateway';
                $data['Message'] = 'Увы такой страницы не существует';
                $data['Code'] = 404;
                break;
            case 601:   //Не совпадение хэша с логином при проверке
                $_SESSION = array();
                $data['Error_status'] = 'Warning Security problem';
                $data['Message'] = $error->message;
                $data['Code'] = 401;
                break;
            case 401:
                $data['Error_status'] = '401 Unauthorized';
                $data['Message'] = $error->message;
                $data['Code'] = 401;
                break;
            case 403:
                $data['Error_status'] = '403 Forbidden';
                $data['Message'] = $error->message;
                $data['Code'] = 403;
                break;
        }
        $controller = new Controllers\Controller_UFO();
        $controller->display($data);

    }
}
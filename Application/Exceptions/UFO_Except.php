<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 17:49
 * @author Darevski
 */

namespace Application\Exceptions;
use Application\Controllers;
use Application\Core\View;
/**
 * Обработка исключений связанных с несуществующими страницами,ошибками доступа и т.д., вывод страниц ошибок
 * Class UFO_Except
 * @package Application\Exceptions
 */
class UFO_Except extends Main_Except
{
    /**
     * Определение кода ошибки, вывод соответсвующей страницы
     * @param UFO_Except $error полученнное исключенение
     */
    function classification_error(UFO_except $error){
        $code = $error->code;
        switch ($code) {
            case 404:   //Отсутсвие страницы
                $data['error_status'] = '404 Bad Gateway';
                $data['message'] = 'Увы такой страницы не существует';
                $data['code'] = 404;
                break;
            case 601:   //Не совпадение хэша с логином при проверке
                $_SESSION = array();
                $data['error_status'] = 'Warning Security problem';
                $data['message'] = $error->message;
                $data['code'] = 401;
                break;
            case 401:
                $data['error_status'] = '401 Unauthorized';
                $data['message'] = $error->message;
                $data['code'] = 401;
                break;
            case 403:
                $data['error_status'] = '403 Forbidden';
                $data['message'] = $error->message;
                $data['code'] = 403;
                break;
            case 400:
                $data['error_status'] = '400 Bad Request';
                $data['message'] = $error->message;
                $data['code'] = 400;
                break;
            default:
                $data['error_status'] = '400 Bad Request';
                $data['message'] = $error->message;
                $data['code'] = 400;
                break;
        }
        View::display('UFO_View.php',$data);
    }
}
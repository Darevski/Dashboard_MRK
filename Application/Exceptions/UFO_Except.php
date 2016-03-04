<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 17:49
 * @author Darevski
 */

namespace Application\Exceptions;
use Application\Core\Config;
use Application\Core\View;
/**
 * Обработка исключений связанных с несуществующими страницами,ошибками доступа и т.д., вывод страниц ошибок
 * Class UFO_Except
 * @package Application\Exceptions
 */
class UFO_Except extends Main_Except
{
    /**
     * @var string Хранит в себе дебаг сообщение об ошибке
     */
    private $debug_message;
    /**
     * Определение кода ошибки, вывод соответсвующей страницы
     * @param UFO_Except $error полученнное исключенение
     */
    function classification_error(UFO_except $error){
        $code = $error->getCode();
        switch ($code) {
            case 404:   //Отсутсвие страницы
                $data['title'] = '404 Bad Gateway';
                $data['message'] = 'Увы такой страницы не существует';
                $this->debug_message = $error->message;
                $data['error_code'] = 404;
                break;
            case 601:   //Не совпадение хэша с логином при проверке
                $_SESSION = array();
                $data['title'] = 'Warning Security problem';
                $data['message'] = $error->message;
                $data['error_code'] = 401;
                break;
            case 401:
                $data['title'] = '401 Unauthorized';
                $data['message'] = $error->message;
                $data['error_code'] = 401;
                break;
            case 403:
                $data['title'] = '403 Forbidden';
                $data['message'] = $error->message;
                $data['error_code'] = 403;
                break;
            case 400:
                $data['title'] = '400 Bad Request';
                $data['message'] = $error->message;
                $data['error_code'] = 400;
                break;
            default:
                $data['title'] = '400 Bad Request';
                $data['message'] = $error->message;
                $data['error_code'] = 400;
                break;
        }
        $this->print_error($data);
    }

    /**
     * выводит информацию об ошибке в браузер
     * @param $data
     */
    private function print_error($data){
        // Вывод в json строку
        $json['state'] = 'fail';
        $json['message'] = $data['message'];
        $json['error_code'] =$data['error_code'];
        $data['json']=View::get_json($json);
        // Вывод кода ответа сервера
        $data['response_code'] = $data['error_code'];
        // 'Видимый' вывод при дебаге
        if (Config::get_instance()->get_build()['debug']) {
            $data['debug_message'] = $this->debug_message;
            $data['display_view'] = 'block';
        }
        else
            $data['display_view'] = 'none';

        View::display('Error_View.php',$data);
    }
}
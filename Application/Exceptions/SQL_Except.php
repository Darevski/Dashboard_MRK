<?php

/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 20.09.15
 * Time: 21:43
 * @author darevski
 */
namespace Application\Exceptions;
use Application\Core\Config;
use Application\Core\View;
/**
 * Обработчик исключений связанных с базой данных
 * Class SQL_Except
 * @package Application\Exceptions
 */
class SQL_Except extends Main_Except
{

    const Error_code = 'db_error';
    // Значения для отображения в Web форме
    const Response_code = 500;
    const Error_title = 'Fail DataBase';
    /**
     * Выводит информацию об ошибке клиентским устройствам
     * @param $error array Содержит в себе информацию об ошибке
     */
    private function print_error($error){
        // Формирование массива для вывода в форму
        $error['json']=View::get_json($error);
        $error['response_code'] = self::Response_code;
        $error['title'] = self::Error_title;
        View::display('Error_View.php',$error);
    }

    /**
     * Ведет журнал ошибок
     */
    public function log_errors(){ }

    /**
     * Формирует массив с данными о возникшей ошибке
     */
    public function output_error(){
        $output = [];
        $output['state'] = 'fail';
        $output['error_code'] = self::Error_code;
        $output['message'] = "Ошибка при работе С БД";
        // При отладочной версии приложения вывод отладочного сообщения
        if (Config::get_instance()->get_build()['debug'])
            $output['Debug_message'] = self::getMessage();
        $this->print_error($output);
    }
}
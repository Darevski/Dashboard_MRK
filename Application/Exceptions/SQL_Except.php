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

    /**
     * SQL_Except constructor. создание объекта контроллера для вывода ошибок
     */
    public function __construct($message){
        parent::__construct($message);;
    }

    /**
     * Выводит информацию об ошибке клиентским устройствам
     * @param $error array Содержит в себе информацию об ошибке
     */
    private function print_error($error){
        View::output_json($error);
    }

    /**
     * @param $error array Содержит в себе информацию об ошибке
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
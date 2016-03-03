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
     * @param $data array Содержит в себе информацию об ошибке
     */
    private function print_error($data){
        // Формирование массива для вывода в форму
        $data['json']=View::get_json($data);
        $data['response_code'] = self::Response_code;
        $data['title'] = self::Error_title;

        // "Видимый" ответ сервера при дебаге
        if (Config::get_instance()->get_build()['debug']){
            $data['display_view'] = 'block';
            // При отладочной версии приложения вывод сообщения об ошибке
            $data['debug_message'] = self::getMessage();
        }
        else
            $data['display_view'] = 'none';


        View::display('Error_View.php',$data);
    }

    /**
     * Ведет журнал ошибок
     */
    public function log_errors(){ }

    /**
     * Формирует массив с данными о возникшей ошибке
     */
    public function output_error(){
        $output['state'] = 'fail';
        $output['error_code'] = self::Error_code;
        $output['message'] = "Ошибка при работе С БД";
        $this->print_error($output);
    }
}
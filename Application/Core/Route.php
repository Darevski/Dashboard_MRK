<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:37
 */
namespace Application\Core;
use Application\Controllers;
use Application\Exceptions;

class Route extends Exceptions\UFO_Except
{
    // default controller and him action
    private $controller_name = 'Dashboard';
    private $action_name = 'start';

    function start()
    {
        // Разбор Запроса
        $this->Exploding_URI();


        // Добавление префиксов
        $controller_name = 'Controller_'.$this->correct_name($this->controller_name);;
        $action_name = 'action_'.$this-> action_name;


        // Загрзука файла контроллера
        $controller_file = $controller_name.'.php';
        $controller_path = "Application/Controllers/".$controller_file;

        if(file_exists($controller_path))
            require_once "Application/Controllers/".$controller_file;
        else
            throw new Exceptions\UFO_Except("Controller $controller_name does not exist", 404);


        // Создание Контроллера
        $controller_name = 'Application\\Controllers\\'.$controller_name;
        $controller = new $controller_name;

        $action = $action_name;
        // Проверка наличия в контроллере экшена
        if(method_exists($controller, $action))
            $controller->$action();
        else
            throw new Exceptions\UFO_Except("Action $action in $controller_name controller does not exist",404);
    }

    private function Exploding_URI(){

        if (preg_match('/\.\w+$/',$_SERVER['REQUEST_URI']))
            //Вброс исключения при обращениии к несуществующим файлам
            throw new Exceptions\UFO_Except("File is not exist",404);

        $routes = explode('/', $_SERVER['REQUEST_URI']);
        // Имя контроллера
        if ( !empty($routes[1]) )
            $this->controller_name = $routes[1];
        // Экшен
        if ( !empty($routes[2]) )
            $this->action_name = $routes[2];
    }

    // преобразование к виду Большаябукваоставшийсямелкийтекст
    private function correct_name($name){
        $first_letter = substr($name,0,1);
        $remaining_letters = substr($name,1,strlen($name)-1);
        $first_letter=strtoupper($first_letter);
        $remaining_letters= strtolower($remaining_letters);
        return $first_letter.$remaining_letters;
    }



}
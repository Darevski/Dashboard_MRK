<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:37
 */
namespace Application\Core;
use Application\Controllers;

class Route
{
    // default controller and him action
    private $controller_name = 'Dashboard';
    private $action_name = 'Start';

    function start()
    {
        // Разбор Запроса
        $this->Exploding_URI();

        // Добавление префиксов
        $model_name = 'Model_'.$this->controller_name;
        $controller_name = 'Controller_'.$this->controller_name;
        $action_name = 'action_'.$this-> action_name;

        // Загрузка файла модели при ее наличии

        $model_file = strtolower($model_name).'.php';
        $model_path = "Aplication/Models/".$model_file;

        if(file_exists($model_path))
            include "Application/Models/".$model_file;


        // Загрзука файла контроллера
        $controller_file = strtolower($controller_name).'.php';
        $controller_path = "Application/Controllers/".$controller_file;

        if(file_exists($controller_path))
            require_once "Application/Controllers/".$controller_file;
        else
        {
            // Исключение
        }

        // Создание Контроллера
        $controller_name = 'Application\\Controllers\\'.$controller_name;
        $controller = new $controller_name;

        $action = $action_name;
        // Проверка наличия в контроллере экшена
        if(method_exists($controller, $action))
            $controller->$action();
        else
        {
            // Исключение
        }
    }

    private function Exploding_URI(){
        if (preg_match('/\.\w+$/',$_SERVER['REQUEST_URI']))
        {
            // Исключение обращение к несуществующему файлу
        }

        $routes = explode('/', $_SERVER['REQUEST_URI']);
        // Имя контрллера
        if ( !empty($routes[1]) )
            $this->controller_name = $routes[1];
        // Экшен
        if ( !empty($routes[2]) )
            $this->action_name = $routes[2];
    }

}
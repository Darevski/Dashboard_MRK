<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:37
 * @author Darevski
 */
namespace Application\Core;
use Application\Controllers;
use Application\Exceptions;

/**
 * Класс преобразования URL запросов в управляющие команды
 *
 * Class Route
 * @package Application\Core
 */
class Route
{
    /**
     * Переменная храняшая имя контролера
     * @var string $controller_name
     * @see starting_Values
     */
    private $controller_name;
    /**
     * Переменная хранящая действие контроллера
     * @var string $action_name
     */
    private $action_name ;

    /**
     * Получение имени контроллера и его действия из URL запроса
     * @throws Exceptions\UFO_Except при отсутсвии контроллера указанного в URL вбрасывается исключение
     * отсутсвия страницы (404)
     */
    function start()
    {
        //Установка значений контроллера и действия по умолчанию
        $this->starting_Values();
        // Разбор Запроса
        $this->Exploding_URI();


        // Добавление префиксов
        $controller_name = 'Controller_'.$this->correct_name($this->controller_name);;
        $action_name = 'action_'.$this-> action_name;


        // Проверка на наличие файла контроллера
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
            // Запуск действия контроллера при его наличии
            $controller->$action();
        else
            throw new Exceptions\UFO_Except("Action $action in $controller_name controller does not exist",404);
    }

    /**
     * Установка значений по умолчанию для контроллера и действия контроллера
     * @see $controller_name устаналиваемое свойство - имя контроллера
     * @see $action_name устанавливаемое свойство - имя действия контроллера
     * @throws Exceptions\UFO_Except
     */
    private function starting_Values(){
        $controller = new Controller();
        //Получение привелегии пользователя
        $auth_state=$controller->state_authorization();
        $this->action_name = 'start';
        if ($auth_state == false)
            $this->controller_name = 'Dashboard';
        else
            switch ($auth_state){
                case 'Admin':
                    $this->controller_name = 'Admin';
                    break;
                default:
                    $this->controller_name = 'Dashboard';
            }
    }

    /**
     * Разбор URI запроса на имя контроллера и действия
     * @see $controller_name устанавливаемое свойство - имя контролера
     * @see $action_name устанавливаемое свойсво - действие контроллера
     * @throws Exceptions\UFO_Except при обращениии к несуществующему файлу вброс 404 исключения
     */
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

    /**
     * Преобразование строки к виду Большаябукваоставшийсямелкийтекст
     * @param string $name
     * @return string
     */
    private function correct_name($name){
        $first_letter = substr($name,0,1);
        $remaining_letters = substr($name,1,strlen($name)-1);
        $first_letter=strtoupper($first_letter);
        $remaining_letters= strtolower($remaining_letters);
        return $first_letter.$remaining_letters;
    }



}
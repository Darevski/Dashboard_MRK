<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:37
 * @author Darevski
 */
namespace Application\Core;
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
     * Определяет директорию расположения контроллеров для динамического создания объектов контроллеров
     */
    const namespace_controllers = 'Application\Controllers\\';

    /**
     * Префикс имени класса контроллеров
     */
    const prefix_controller = 'Controller_';

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
     * Создает объект запрошенного пользователем контроллера и вызывает его действие
     * @param array mixed информация об контроллере и экшене из запроса пользователя
     * @throws Exceptions\UFO_Except при отсутсвии контроллера(метода указанного в контроллере) указанного в запросе
     * вбрасывается исключение отсутсвия страницы (404)
     */
    function start($route_result)
    {
        if (!isset($route_result['target']))
            throw new Exceptions\UFO_Except('Роутинг не обнаружил совпадений',404);

        // Установка именов контроллеров и действий полученных из запроса
        if (isset($route_result['params']['controller']) && isset($route_result['params']['action'])){
            // Выбор директории расположения контроллеров для Администратора и обычного пользователя
            if ($route_result['target'] == 'admin')
                $namespace = self::namespace_controllers.'Admin\\';
            else if ($route_result['target'] == 'dashboard')
                $namespace = self::namespace_controllers.'Dashboard\\';
            else
                $namespace = self::namespace_controllers;

            $this->controller_name=$namespace.self::prefix_controller.$route_result['params']['controller'];
            $this->action_name=$route_result['params']['action'];
        }
        // При отсутсвии контроллеров в запросе и при обращение к модулям администратора или рассписания
        // происходит установка контроллеров указанных в конфиге роутинга
        else if($route_result['target'] !== 'home'){
            $control_info= explode('#',$route_result['target']);
            $this->controller_name =self::namespace_controllers.self::prefix_controller.$control_info[0];
            $this->action_name = $control_info[1];
        }
        // При обращении к корню сайта, для контроллера устанавливаются значения на оснавании привелегий пользователя
        else
            $this->starting_Values();


        // Преобразование пространства имен в путь до файла
        $file = preg_replace('/\\\/','/',$this->controller_name).'.php';
        //Проверка на существование файла класса
        if (file_exists($file)){
            $controller = new $this->controller_name;
            $action = 'action_'.$this->action_name;
            // При существовании метода в объекте происходит его вызов, при отсутствии происходит вброс исключения
            if (method_exists($controller,$action))
                $controller->$action();
            else
                throw new Exceptions\UFO_Except ("Метод $action не существует в классе $this->controller_name",404);
        }
        else
            throw new Exceptions\UFO_Except("Класc $this->controller_name не существует",404);
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
        $namespace = self::namespace_controllers.self::prefix_controller;
        $this->action_name = 'start';
        if ($auth_state == false)
            $this->controller_name = $namespace.'Dashboard';
        else
            switch ($auth_state){
                case 'Admin':
                    $this->controller_name = $namespace.'Admin';
                    break;
                default:
                    $this->controller_name = $namespace.'Dashboard';
            }
    }
}
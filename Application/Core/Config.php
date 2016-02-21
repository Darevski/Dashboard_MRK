<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 21.02.16
 * Time: 19:08
 */

namespace Application\Core;

/**
 * Class Config singleton
 * Предоставляет конфигурационную информацию
 * @package Core
 */
class Config
{
    /**
     * @var Config singleton object
     */
    private static $instance =null;

    /**
     * Singleton
     * @return Config
     */
    public static function get_instance(){
        if (is_null(self::$instance))
            self::$instance = new Config();
        return self::$instance;
    }

    /**
     * @var array конфигурация для родключения к БД
     */
    private $database_config;

    /**
     * Состояние проекта отладка или релиз
     * @var string debug/production
     */
    private $build;

    /**
     * Парсерит конфигурационный файл приложения
     * Config constructor.
     */
    private function __construct(){
        $Config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/Application/Config.ini',true);
        $this->database_config = $Config['Data_Base_config'];
        $this->build = $Config['Build'];
    }

    private function __clone(){}

    /**
     * Возвращает массив с параметрами подключения к БД
     * @return array массив с данным для подключения к БД
     */
    public function get_database_config(){
        return $this->database_config;
    }

    /**
     * Возвращает значение отладки true/false
     * @return bool
     */
    public function get_build(){
        return $this->build;
    }
}
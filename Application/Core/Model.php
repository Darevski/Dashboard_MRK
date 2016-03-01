<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 * @author darevski
 */
namespace Application\Core;
/**
 * Базовый класс модели
 * подключение базы данных
 * Class Model
 * @package Application\Core
 */
class Model
{
    /**
     * Переменная хранящая объект для работы с БД
     * @var Safe_SQL
     */
    protected $database;
    // Загрузука конфига С параметрами базы данных

    /**
     * Получение конфигурации для подключение к БД, получение объекта для работы с бд
     * @see $database созданный объект SafeSQL для работы с БД
     */
    function __construct(){
        $data_base_opt = Config::get_instance()->get_database_config();
        /** @var Safe_SQL database singleton */
        $this->database = Safe_SQL::get_instance($data_base_opt);
    }
}
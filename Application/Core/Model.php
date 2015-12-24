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
     * Загрузка конфигурации из файла, подключение базы данных
     * @see $database созданный объект SafeSQL для работы с БД
     */
    function __construct(){
        $data = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/Application/Config.ini',true);
        $data_base_opt = $data['Data_Base_config'];
        $this->database = new Safe_SQL($data_base_opt);
    }
}
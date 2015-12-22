<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 14.09.15
 * Time: 22:40
 */
namespace Application\Core;

class Model
{
    protected $database;
    // Загрузука конфига С параметрами базы данных
    protected function __construct(){
        $data = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'Application/Config.ini',true);
        $data_base_opt = $data['Data_Base_config'];
        $this->database = new Safe_SQL($data_base_opt);
    }
}
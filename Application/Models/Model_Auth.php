<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 23:27
 */

namespace Application\Models;


use Application\Core\Model;

class Model_Auth extends Model{
    private $table;

    // установка имени таблицы по умолчанию
    public function __construct($table= 'users'){
        parent::__construct();
        $this->table = $table;
    }

    /**
     * Проверяет вводимый логин и пароль, при совпадении генерирует хэщ, который записывается в бд
     * @param $login
     * @param $password
     * @return mixed $new_hash || false
     */
    public function check_auth($login,$password){
        $password= md5($password);
        $result = $this->database->getRow("SELECT * FROM $this->table WHERE login = ?s and password = ?s",$login,$password);
        if (isset($result)){
            $request = "UPDATE $this->table SET hash=?s WHERE login=?s and password =?s";
            $new_hash =$this->gen_Hash(15);
            $this->database->query ($request,$new_hash,$login,$password);
            return $new_hash;
        }
        else
            return false;
    }
    /**
     * Генерирует строку состоящую из [a-z,A-Z,0-9]
     * @param int $length -длинна строки по-умолчанию 15
     * @return string
     */
    private function gen_Hash($length=15){
        $chars = "abdefhiknrstyzABDEFGHKNQRSTYZ23456789";
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++){
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }



}
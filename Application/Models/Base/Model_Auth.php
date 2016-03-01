<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 29.09.15
 * Time: 23:27
 * @author Darevski
 */

namespace Application\Models\Base;

use Application\Core\Model;

/**
 * Класс связанный с авторизацией пользователей
 * Class Model_Auth
 * @package Application\Models
 */
class Model_Auth extends Model{

    /**
     * Проверяет вводимый логин и пароль, при совпадении генерирует хэщ, который записывается в бд
     * @param string $login
     * @param string $password
     * @return mixed $new_hash|false новый хэш при входе/ false - при несовпадении пароля+логин
     */
    public function check_password($login,$password){
        $password= md5($password);
        $result = $this->database->getRow("SELECT * FROM users WHERE login = ?s and password = ?s",$login,$password);
        if (isset($result)){
            $request = "UPDATE users SET hash=?s WHERE login=?s and password =?s";
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

    /**
     * Возвращает привелегии при существовании хэша
     * @param string $login
     * @param string $hash записанный в БД
     * @return string|bool привеления, при существующей паре логин - хэш || false - не существует результата
     * для пары hash+login
     */

    public function take_privilege($login,$hash){
        $result = $this->database->getRow("SELECT * FROM users WHERE login = ?s and hash = ?s",$login,$hash);
        if (isset($result))
            return $result['privilege'];
        else
            return false;
    }

    /**
     * Сброс хэша указанного пользователя
     * @param string $login
     */
    public function clear_hash($login){
        $request = "UPDATE users SET hash=?s WHERE login=?s";
        $this->database->query ($request,'',$login);
    }


}
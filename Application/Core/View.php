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
 * Класс вывода данных
 * Class View
 * @package Application\Core
 */
class View
{
    //public $template_view; // здесь можно указать общий вид по умолчанию.
    /**
     * Генерация вида страницы
     * @param string $template_view
     */
    static function generate($template_view){
        include 'Application/Views/'.$template_view;
    }

    /**
     * Вывод контента в выбранную страницу
     * @param $content_view
     * @param null $data
     */
    static function display($content_view,$data = null){
        include 'Application/Views/'.$content_view;
    }

    /**
     * Вывод Json данных на страницу c подписью md5
     * @param array $data
     */
    static function output_json($data){
        // При дебаге версии "видимый" вывод ответа
        if (Config::get_instance()->get_build()['debug'] === true)
            echo "<json>";
        else
            echo "<json style=\"display: none\">";
        // непосредственный вывод строки
        echo self::generate_json($data);

        echo "</json>";
    }

    /**
     * Возвращает Json строку подписанную md5
     * @param $data
     * @return string
     */
    static function get_json($data){
        return self::generate_json($data);
    }

    /**
     * генерирует json строку из объектов и подписывает md5
     * @param $data
     * @return string
     */
    private static function generate_json($data){
        $json=json_encode($data,JSON_UNESCAPED_UNICODE );
        $md5 = md5($json);
        $value['md5']=$md5;
        $json=json_encode($data,JSON_UNESCAPED_UNICODE );
        return $json;
    }
}
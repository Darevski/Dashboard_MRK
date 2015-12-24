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
     * @param string $content_view
     * @param string $auth_view
     * @param string $template_view
     * @param null $data
     */
    function generate($content_view='Blocks_view.php', $auth_view='AuthForm_view.php', $template_view='Tmp_view.php', $data = null){
        include 'Application/Views/'.$template_view;
    }

    /**
     * Вывод контента в выбранную страницу
     * @param $content_view
     * @param null $data
     */
    function display($content_view,$data = null){
        include 'Application/Views/'.$content_view;
    }

    /**
     * Вывод Json данных на страницу
     * @param array $value
     */
    function output_json($value){
        $json=json_encode($value,JSON_UNESCAPED_UNICODE );
        echo $json;
    }
}
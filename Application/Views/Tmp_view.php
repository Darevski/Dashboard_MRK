<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 15.09.15
 * Time: 9:19
 * @author darevski
 */
?>
<html>
	<head>
		<title>Расписание</title>
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<script src="/js/click-handler.js" type="text/javascript"></script>
		<script src="/js/login.js" type="text/javascript"></script>
        <meta charset="utf-8" />
	</head>
    <body>
        <div id="multi-close" onclick="CloseMultiContainer()"></div>
        <div id="multiuse-container">
        <!-- Блок Выплывающего меню-->
            <?php include_once 'Application/Views/'.$auth_view;?>
		</div> 
        <div id="login-box" class="icon-electronic" onclick="OnclickShowMultiContainer()"></div>
        <div id="top-wrapper">
		  <div id="logo-top" class="icon-electronic">МГРК ПРИ БГУИР</div>
		  <div id="qsearch-top"><input type="text"></div>
	   </div>
        <div id="main-container">
            <div id="block-container">
                <!--Блоки-->
                <?php include_once 'Application/Views/'.$content_view;?>
            </div>
            <div id="button-back" onclick="OnClickSetBlocksByDefault()">НАЗАД</div>
            <div id="get-info-block"></div>
        </div>
    </body>
</html>
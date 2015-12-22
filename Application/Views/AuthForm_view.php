<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 24.10.15
 * Time: 19:59
 */
?>
<form id="login-form" action="/Auth/Enter" class="icon-user" method="post" onsubmit="return false;">
	<input type="text" id="username-input" placeholder="Login" name="login">
    <input type="password" id="password-input" placeholder="Password" name="password">
	<input type="hidden" name="catch" value="1">
	<div id="login-error-state"></div>
	<div id="login-button" onclick="trylogin()">Войти</div>
</form>
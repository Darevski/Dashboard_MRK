function trylogin()
{
	document.getElementById("login-error-state").style.height = "0px";
	var xhr = new XMLHttpRequest(); // creating XMLHttpRequest object
	xhr.open('POST', '/Auth/Enter', true); //configurating
	var body = 'login=' + encodeURIComponent(document.getElementById("username-input").value) + '&password=' + encodeURIComponent(document.getElementById("password-input").value); //creating body
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); //setting headers
	xhr.onreadystatechange = function() // now listen
	{
		if ((xhr.readyState == 4)  && (xhr.status == 200))
			{
				//action on good answer
				if (xhr.responseText == "")
						document.getElementById("login-form").submit();
				else
					{
						document.getElementById("login-error-state").innerHTML = "НЕВЕРНЫЙ ЛОГИН ИЛИ ПАРОЛЬ";
						document.getElementById("login-error-state").style.height = "40px";
						alert(xhr.responseText);
					}
			}
			else
			{
				if ((xhr.status != 200) && (xhr.status != 403))
						//error connection
					{
						document.getElementById("login-error-state").innerHTML = "ОШИБКА СОЕДИНЕНИЯ: " + xhr.status;
						document.getElementById("login-error-state").style.height = "40px";
					}
				else
					if (xhr.status == 403)
					{
						document.getElementById("login-error-state").innerHTML = "НЕВЕРНЫЙ ЛОГИН ИЛИ ПАРОЛЬ";
						document.getElementById("login-error-state").style.height = "40px";
					}
			}
	}
	xhr.send(body); // send request
}
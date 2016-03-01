/*** --- Очищает BODY
Input:
	none
Output:
	none
***/
function ClearBody()
{
    document.body.innerHTML = "";
}

/*** --- Возвращает COOKIE
Input:
	NAME - параметр COOKIE
Output:
	VALUE of NAME ::: FALSE
***/
function getCookie(name) {
  var matches = document.cookie.match(
      new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
  return matches ? decodeURIComponent(matches[1]) : false;
}

/*** --- Устанавливает COOKIE
Input:
	NAME - параметр COOKIE
	VALUE - значение COOKIE
	OPTIONS - свойства -> истечение, путь, домен, secure
Output:
	none
***/
function setCookie(name, value, options) {
  options = options || {};
  var expires = options.expires;
  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }
  value = encodeURIComponent(value);
  var updatedCookie = name + "=" + value;
  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }
  document.cookie = updatedCookie;
}

/*** --- Удаляет COOKIE
Input:
	NAME - паарметр COOKIE
Output:
	none
***/
function deleteCookie(name) {
  setCookie(name, "", {
    expires: -1
  })
}

/*** --- Создает новый запрос
Input:
	ROUTE - адрес запроса
	BODY - ТЕЛО ЗАПРОСА
	CALLBACK - возвращение значения в функцию
Output:
	CALLBACK - возвращение значения в функцию
***/
function NewXHR(route, body, callback)
{
	var xhr = new XMLHttpRequest(); // creating XMLHttpRequest object
	var ERROR_STATE = false; // setup error checker
	xhr.open('POST', route, true); //configurating xhr
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); //setting xhr headers
	xhr.onreadystatechange = function() // listening
	{
		if (xhr.readyState == 4)
			if (xhr.status == 200) // if all OK
			{
				var json_response = document.createElement("html");
				json_response.innerHTML = xhr.responseText;
				var answer = json_response.getElementsByTagName("json")[0];
				if (answer != void(0))
					callback(answer.innerHTML);
				else
					callback(xhr.responseText);
			}
			else
			{
                if (!ERROR_STATE)
				   {
						var json_response = document.createElement("html");
						json_response.innerHTML = xhr.responseText;
						var answer = json_response.getElementsByTagName("json")[0];
						if (answer != void(0))
							callback(answer.innerHTML);
						else
							{
								var ans = {};
								ans.check = false;
								ans.status = xhr.status;
								ans.readyState = xhr.readyState;
								ans.ResponseText = xhr.responseText;
								callback(ans);
							}
						ERROR_STATE = true;
				   }
			}
	}
	xhr.send(body); // send request
}

/*** --- Удаляет сообщение JS
Input:
	E - ELEMENT
Output:
	none
***/
function RemoveEx(e)
{
	CHECK_stop = false;
	//Dashboard_CHECK();
	e.parentNode.style.opacity = "0";
	setTimeout(function () { e.parentNode.remove(); }, 600);
}

/*** --- Создает сообщение JS
Input:
	MESSAGE - сообщение
Output:
	none
***/
function CreateEx(message)
{
	CHECK_stop = true;
	var layer1 = document.createElement("div");
	layer1.className = "js-ex";
	var layer2 = document.createElement("div");
	layer2.className = "js-ex-text";
	layer2.innerHTML = message;
	var layer3 = document.createElement("div");
	layer3.className = "js-ex-close";
	layer3.onclick = function () { RemoveEx(this.parentNode) };
	layer3.innerHTML = "OK";
	layer2.appendChild(layer3);
	layer1.appendChild(layer2);
	layer1.style.opacity = "0";
	document.body.appendChild(layer1);
	setTimeout(function () { layer1.style.opacity = "1"; }, 0);
}

/*** --- Создает LOADER
Input:
	X - начальное отклонение x, относительно (100% - 1000px) / 2
	Y - начальное отклонение y, относительно (100% - 500px) / 2
Output:
	LOADER - возвращает LOADER типа ELEMENT
***/
function CreateLoader(block, allBlock, fullscreen)
{
    var loader = document.createElement("div");
	if ((fullscreen != undefined) || (fullscreen != null))
		{
            loader.style.left = "0";
            loader.style.top = "0";			
			loader.style.width = "100%";
			loader.style.height = "100%";
		}
	else
		{
			loader.style.width = block.offsetWidth + "px";
			loader.style.height = block.offsetHeight + "px";
			if ((allBlock == undefined) || (allBlock == null))
				{
					loader.style.left = "calc(50% - 500px + " + block.offsetLeft + "px )";
					loader.style.top = "calc(50% - 250px + " + block.offsetTop + "px )";
				}
			else
				{
					loader.style.left = block.offsetLeft + "px";
					loader.style.top = block.offsetTop + "px";
				}
		}
    loader.className='loader';
    var span_loader = document.createElement("span");
    span_loader.className = "loader-container";
    for (var i =0; i<4; i++)
        {
            var div_loader = document.createElement("div");
            span_loader.appendChild(div_loader);
        }
    loader.appendChild(span_loader);
    loader.zIndex = 10;
    return loader;
}
/*** --- Гененирует основное меню
Input:
	none
Output:
	none
***/
function Dashboard_Load()
{
	CHECK_stop = false;
    var body = document.body;
	body.style.opacity = "0";
    var loader = CreateLoader(body, 1);    
	setTimeout(function (){
		ClearBody();
		body.style.opacity = "";
        document.body.appendChild(loader);
		loader.style.opacity = "1";
        NewXHR("/Application/Views/Skeletons/Main_Dashboard.html", null, function (data){
            if (data.status != "fail")
                setTimeout(function () {
                    loader.style.opacity = "";
                    body.style.opacity = "0";
                    setTimeout(function () {
                        loader.remove();
                        body.style.opacity = "";
                        body.innerHTML = data;
                        Dashboard_CHECK();
                    }, 600);
                }, 600);
            else
                {
                    //exception handler
                    CreateEx(data.message);
                }
    });
	}, 600);
}
/*** --- Генерирует меню выбора группы
Input:
	none
Output:
	SetCookie
***/
function GroupChoice()
{
	CHECK_stop = true;
	deleteCookie("group");
    var body = document.body;
    var loader = CreateLoader(body, 1);
    body.appendChild(loader);
    setTimeout( function() {
        loader.style.opacity = "1";
        setTimeout( function() {
            if (document.getElementById("main-container") != undefined)
                document.getElementById("main-container").remove();
            NewXHR("/Dashboard/get_list_group", null, function(ResponseText) {
                if (ResponseText.status != "fail") {
                    var answer = JSON.parse(ResponseText);
                    var div_container = document.createElement("div");
                    div_container.id = "container-gr";
                    var div_header = document.createElement("div");
                    div_header.id = "header";
                    div_header.innerHTML = "Пожалуйста, выберите группу";
                    div_container.appendChild(div_header);
                    for (var i = 1; i <= 4; i++)
                        {
                            var div_list = document.createElement("div");
                            div_list.className = "grade-list";
                            var p_temp = document.createElement("p");
                            p_temp.innerHTML = i + " курс";
                            div_list.appendChild(p_temp);
                            var ul_temp = document.createElement("ul");
                            div_list.appendChild(ul_temp);
                            if (answer[i] != null)
                                {
                                    var j = 0;
                                    while (answer[i][j] != null)
                                        {
                                            var li_temp = document.createElement("li");
                                            li_temp.innerHTML = answer[i][j];
                                            ul_temp.appendChild(li_temp);
                                            j++;
                                        }
                                }
                            div_container.appendChild(div_list);
                        }
                    loader.style.opacity = "";
                    setTimeout( function () {
                        loader.remove();
                        body.appendChild(div_container);
                        for (var i =0; i< document.getElementsByTagName("ul").length; i++)
                                document.getElementsByTagName("ul")[i].onclick = function(e) {
                                            setTimeout(function() {
                                                setCookie("group", e.target.innerHTML);
                                                setTimeout(Dashboard_Load(), 600);
                                            }, 600);
                                };
                    }, 600);
                }
                else
                    {
                        //exception handler
                        CreateEx(data.message);
                    }
            });
        }, 600);
    }, 200);

}

/*** --- Создает объект
Input:
	NAME_TYPE - тип создаваемого объекта
	NAME_ID - ID создаваемого объекта
	NAME_CLASS - класс создаваемого объекта
	NAME_ONCLICK - аттрибут OnClick
	NAME_INNER - innerHTML создаваемого объекта
Output:
	elem - объект типа DOM.ELEMENT
***/
function CreateElem(name_type, name_id, name_class, name_onclick, name_inner)
{
	var elem = document.createElement(name_type);
	if (name_id != null)
		elem.id = name_id;
	if (name_class != null)
		elem.className = name_class;
	if (name_onclick != null)
		elem.setAttribute("onclick", name_onclick);
	if (name_inner != null)
		elem.innerHTML = name_inner;
	return elem;
}

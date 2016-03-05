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
function getVar(name) {
	var item = localStorage.getItem(name);
  	return item!=null ? item : false;
}

/*** --- Устанавливает COOKIE
Input:
	NAME - параметр COOKIE
	VALUE - значение COOKIE
	OPTIONS - свойства -> истечение, путь, домен, secure
Output:
	none
***/
function setVar(name, value) {
	localStorage.setItem(name, value);
}

/*** --- Удаляет COOKIE
Input:
	NAME - паарметр COOKIE
Output:
	none
***/
function delVar(name) {
	delete localStorage[name];
}

/*** --- Создает новый запрос
Input:
	ROUTE - адрес запроса
	BODY - ТЕЛО ЗАПРОСА
	CALLBACK - возвращение значения в функцию
Output:
	CALLBACK - возвращение значения в функцию
***/
function Request(route, body)
{
	this.body = body;
	this.route = route;
	this.noJSON = false;
	this.callback = function () {};
}
Request.prototype = {
	do: function () {
		var callback = this.callback;
		var xhr = new XMLHttpRequest();
		var already_processed = false;
		xhr.open('POST', this.route, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function()
		{
			if (xhr.readyState == 4)
				if (xhr.status == 200)
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
					if (!already_processed)
					   {
							var json_response = document.createElement("html");
							json_response.innerHTML = xhr.responseText;
							var answer = json_response.getElementsByTagName("json")[0];
							if (answer != void(0))
								callback(answer.innerHTML);
							else
								{
									var ans = {};
									ans.state = "fail";
									ans.message = xhr.responseText;
									console.error = xhr.response;
									callback(ans);
								}
							already_processed = true;
					   }
				}
		}
		if (this.noJSON)
			xhr.send(this.body);
		else
			xhr.send("json_input=" + JSON.stringify(this.body));
	}
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
	setTimeout(function () { layer1.style.opacity = 1; }, 10);
}
/*
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
}*/

function PreLoader(block)
{
	this.fullscreen = true;
	this.transparent = false;
	this.loader = null;
	this.block = (block) ? block : document.body;
	this.before = function () {};
	this.inprogress = function () {};
}
PreLoader.prototype = {
	create: function () {
		this.loader = document.createElement("div");
		this.loader.style.opacity = 0;
		this.loader.style.transition = "1s";
		if (this.fullscreen)
			{
				this.loader.style.left = "0";
				this.loader.style.top = "0";			
				this.loader.style.width = "100%";
				this.loader.style.height = "100%";
			}
		else
			{
				this.loader.style.width = this.block.offsetWidth + "px";
				this.loader.style.height = this.block.offsetHeight + "px";
				this.loader.style.left = this.block.getBoundingClientRect().left + "px";
				this.loader.style.top = this.block.getBoundingClientRect().top + "px";
			}
		(this.transparent) && (this.loader.style.backgroundColor = "transparent");
		this.loader.className='loader';
		var span_loader = document.createElement("span");
		span_loader.className = "loader-container";
		for (var i =0; i<4; i++)
			span_loader.appendChild(document.createElement("div"));
		this.loader.appendChild(span_loader);
		this.loader.zIndex = 10;
		this.before();
		var _this = this;
		document.body.appendChild(this.loader);
		setTimeout( function () { _this.loader.style.opacity = 1; }, 10);
		setTimeout( function () { _this.inprogress(); }, 1000);
	},
	purge: function () {
		this.loader.style.opacity = 0;
		var _this = this;
		setTimeout( function () { _this.loader.remove() }, 500);
	}
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
	var loader = new PreLoader();
	loader.before = function () { ClearBody(); }
	loader.inprogress = function () {
		var query = new Request("/Application/Views/Skeletons/Main_Dashboard.html");
		query.callback = function (Response) {
			var temp = document.createElement("div"); // заглушка, поскольку innerHTML += вызывает перезагрузку DOM элементов,
			temp.innerHTML = Response; // что приводит к потере контроля за PreLoader
			document.body.appendChild(temp.children[0]); // TODO: найти более удачный способ решения
			Dashboard_CHECK();
			loader.purge();
    	}
		query.do();
	}
	loader.create();
}
/*** --- Генерирует меню выбора группы
Input:
	none
Output:
	setVar
***/
function GroupChoice()
{
	CHECK_stop = true;
	delVar("group");
    var body = document.body;
	var loader = new PreLoader();
	loader.before = function () { ClearBody(); }
	loader.inprogress = function () {
		var query = new Request("/dashboard/groups/get_list");
		query.callback = function (Response) {
			var answer = JSON.parse(Response);
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
				body.appendChild(div_container);
				for (var i =0; i< document.getElementsByTagName("ul").length; i++)
						document.getElementsByTagName("ul")[i].onclick = function(e) {
										setVar("group", e.target.innerHTML);
										setTimeout(Dashboard_Load(), 10);
						}
				loader.purge();
		}
		query.do();
	}
	loader.create();
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

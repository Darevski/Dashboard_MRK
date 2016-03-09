window.onerror = function (message, url, linenum) {
	CreateEx(message);
	console.error(message,linenum);
}

/** Очищает страницу
*
*/
function ClearBody()
{
    document.body.innerHTML = "";
}

/** Возвращает информацию из хранилища
* @param {string} name Имя ключа в хранилище
* @return {string} item В случае существования возвращает значение, иначе false
*/
function getVar(name) {
	var item = localStorage.getItem(name);
  	return item!=null ? item : false;
}

/** Сохраняет информацию в хранилище
* @param {string} name Имя ключа
* @param {string} value Значение
*
*/
function setVar(name, value) { localStorage.setItem(name, value); }

/** Удаляет информацию из хранилища
* @param {string} name Имя ключа
*/
function delVar(name) { delete localStorage[name]; }

/** Класс запроса - реализует функцию отправки и обработки запросов
* @class
* @param {string} route Адрес запроса
* @param {Object} body Тело запроса
* @this {Request} Экземпляр класса
* @callback
*/
function Request(route, body)
{
	this.body = body;
	this.route = route;
	this.noJSON = false;
	this.callback = function () {};
}
Request.prototype = {
	/** @private */
	do: function () {
		try {
			var callback = this.callback;
			var xhr = new XMLHttpRequest();
			var already_processed = false;

			xhr.open('POST', this.route, true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onreadystatechange = function()
			{
				try {
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
									already_processed = true;

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
							   }
						}
				}
				catch (ex) { console.error(ex); CreateEx(ex.message); }
			}
			this.noJSON ? xhr.send(this.body) : xhr.send("json_input=" + JSON.stringify(this.body));
		}
		catch (ex) { console.error(ex); CreateEx(ex.message); }
	}
}

/** Удаляет информационное окно
* @param {DOM-Element} e Элемент для удаления
*/
function RemoveEx(e)
{
	CHECK_stop = false;
	e.parentNode.style.opacity = "0";
	setTimeout(function () { e.parentNode.remove(); }, 520);
}

/** Создает информационное окно
* @param {string} message Сообщение об ошбике
*/
function CreateEx(message)
{
	try {
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
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Класс загрузчика - реализует PreLoader и управление им
* @class
* @param {DOM-Element} block Желаемый блок для закрытия
* @this {PreLoader} Экземпляр класса
* @callback inprogress
*/
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
		try {
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
		}
		catch (ex) { console.error(ex); CreateEx(ex.message); }
	},
	purge: function () {
		try {
			this.loader.style.opacity = 0;
			var _this = this;
			setTimeout( function () { _this.loader.remove() }, 500);
		}
		catch (ex) { console.error(ex); CreateEx(ex.message); }
	}
}

/** Загружает основное меню пользователя
*
*/
function Dashboard_Load()
{
	try {
		CHECK_stop = false;
		var loader = new PreLoader();

		loader.before = function () { ClearBody(); }
		loader.inprogress = function () {
			try {
				var query = new Request("/Application/Views/Skeletons/Main_Dashboard.html");
				query.callback = function (Response) {
					try {
					var temp = document.createElement("div"); // заглушка, поскольку innerHTML += вызывает перезагрузку DOM элементов,
					temp.innerHTML = Response; // что приводит к потере контроля за PreLoader
					document.body.appendChild(temp.children[0]); // TODO: найти более удачный способ решения

					Dashboard_CHECK();
					loader.purge();
					}
					catch (ex) { loader.purge(); console.error(ex); CreateEx(ex.message); }
				}
				query.do();
			}
			catch (ex) { loader.purge(); console.error(ex); CreateEx(ex.message); }
		}
		loader.create();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Загружает меню выбора группы
*
*/
function GroupChoice()
{
	try {
		CHECK_stop = true;
		delVar("group");
		delVar("alerts");
		delVar("shedule");

		var body = document.body;
		var loader = new PreLoader();

		loader.before = function () { ClearBody(); }
		loader.inprogress = function () {
			try {
				var query = new Request("/dashboard/groups/get_list");
				query.callback = function (Response) {
					try {
						var answer = JSON.parse(Response);
						if (answer.state != "fail") {
							var div_container = document.createElement("div");
							var div_header = document.createElement("div");

							div_container.id = "container-gr";
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
						else { loader.purge(); console.error(answer); CreateEx(answer.message); }
					}
					catch (ex) { loader.purge(); console.error(ex); CreateEx(ex.message); }
				}
				query.do();
			}
			catch (ex) { loader.purge(); console.error(ex); CreateEx(ex.message); }
		}
		loader.create();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Создает элемент
* @param {string} name_type Тип объекта
* @param {string} name_id ID объекта
* @param {string} name_class Класс объекта
* @param {string} name_onclick Событие по клику
* @param {string} name_inner innerHTML объекта
* @return {DOM-Element} Созданный элемент
* @todo Переписать функцию как класс
*/
function CreateElem(name_type, name_id, name_class, name_onclick, name_inner)
{
	try {
	var elem = document.createElement(name_type);
	
	(name_id) && (elem.id = name_id);
	(name_class) && (elem.className = name_class);
	(name_onclick) && (elem.setAttribute("onclick", name_onclick));
	(name_inner) && (elem.innerHTML = name_inner);

	return elem;
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}
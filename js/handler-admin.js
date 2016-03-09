var TIME_difference;
var professorslist, lessonlist;
var notificationlist;
window.onload = DoOnLoad;

/** Загружает скелет, вызывает callback после загрузки
* @param {string} route адрес скелета
* @param {function} callback callback-функция
*/
function LOAD_SkeletonsFullscreen(route, callback)
{
	try {
		var body = document.body;
		var loader = new PreLoader();

		loader.before = function () { ClearBody(); }
		loader.inprogress = function () {
			try {
				var query = new Request(route);
				query.callback = function (Response) {
					try {
						var temp = document.createElement("div"); // заглушка, поскольку innerHTML += вызывает перезагрузку DOM элементов,
						temp.innerHTML = Response; // что приводит к потере контроля за PreLoader
						for (var i = 0; i < temp.childNodes.length; i++)
							document.body.appendChild(temp.childNodes[i]); // TODO: найти более удачный способ решения
						loader.purge();

						(callback != null) && (callback());
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

/** Выполнятеся по загрузке страницы
*
*/
function DoOnLoad()
{
	try {
		LOAD_AdminDash();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Загружает главное меню администратора
*
*/
function LOAD_AdminDash()
{
	try {
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Dashboard.html");
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Загружает модуль множественной отправки уведомлений
* @throws Исключения, вызванные ошибками отправки, обработки; загрузки и изменения контента
*/
function LOAD_Message()
{
	try {
		LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Message.html", function () {
			try {
				var query1 = new Request("/dashboard/units/get_faculty_list");
				query1.callback = function (Response) {
					try {
							var answer = JSON.parse(Response);
							if (answer.state != "fail")
								{
									var block = document.getElementById("message-filter-department").children[1];
									var i =0;
									while (answer[i] != undefined)
										{
											var el1 = CreateElem("li", null, "checkbox_list_item");
											var el2 = CreateElem("label", null, "checkbox_label");
											var el3 = CreateElem("input", null, "checkbox");
											el3.setAttribute("onchange", "LOAD_whom_sent()");							
											el3.setAttribute("type", "checkbox");
											el3.setAttribute("value", answer[i].code);
											el2.appendChild(el3);
											el2.innerHTML += answer[i].name;
											el1.appendChild(el2);
											block.appendChild(el1);
											i++;
										}
								}
							else
								CreateEx(answer.message);
						}
					catch (ex) { console.error(ex); CreateEx(ex.message); }
				}
				query1.do();
				var query2 = new Request("/dashboard/units/get_spec_list");
				query2.callback = function (Response) {
					try {
							var answer = JSON.parse(Response);
						if (answer.state != "fail")
							{
								var block = document.getElementById("message-filter-spec").children[1];
								var i =0;
								while (answer[i] != undefined)
									{
										var el1 = CreateElem("li", null, "checkbox_list_item");
										var el2 = CreateElem("label", null, "checkbox_label");
										var el3 = CreateElem("input", null, "checkbox");
										el3.setAttribute("type", "checkbox");
										el3.setAttribute("value", answer[i].code);
										el3.setAttribute("onchange", "LOAD_whom_sent()");
										el2.appendChild(el3);
										el2.innerHTML += answer[i].name;
										el1.appendChild(el2);
										block.appendChild(el1);
										i++;
									}
							}
							else
								CreateEx(answer.message);
						}
					catch (ex) { console.error(ex); CreateEx(ex.message); }
				}
				query2.do();
				LOAD_whom_sent();
			}
			catch (ex) { console.error(ex); CreateEx(ex.message); }
		});
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Удаляет уведомление
* @param {number} ident ID удаляемого уведомления
* @param {DOM-Element} el Элемент страницы, указывающий на удаляемое уведомление
* @throws Исключения, вызванные ошибками удаления, редактирования контента
*/
function DELETE_message(ident, el)
{
	try {
		var req = {};
		req.id = ident;
		var query = new Request("/admin/notifications/delete", req);
		query.callback = function (Response) {
			try {
					var answer = JSON.parse(Response);
					if (answer.state == "success")
						{
							var i = 0;
							while ((notificationlist[i] != undefined) & (notificationlist[i] != null))
								{
									if (notificationlist[i].id == ident)
										notificationlist.splice(i, 1);
									i++;
								}
							el.parentElement.parentElement.style.height = "0px";
							el.parentElement.parentElement.style.borderBottom = "0px solid transparent";
							setTimeout(function () {
								el.remove();
							}, 800);
						}
					else
						CreateEx(answer.message);
				}
			catch (ex) { console.error(ex); CreateEx(ex.message); }
		}
		query.do();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Создает экземпляр элемента li для модуля удаления уведомлений
* @param {object} input Входные данные уведомления (тип, текст, дата начала/окончания)
* @return {DOM-Element} li Возвращает строку для модуля удаления уведомлений
* @throws Исключения, вызванные чтением входных данных, создания элемента
*/
function create_li_notification(input)
{
	try {
		var li = CreateElem("li");

		li.style.borderLeftStyle = "solid";
		li.style.borderLeftWidth = "5px";
		if (input.state == "note")
			li.style.borderLeftColor = "#FFC107";
		else if (input.state == "alert")
			li.style.borderLeftColor = "#F44336";
		else if (input.state == "info")
			li.style.borderLeftColor = "#03A9F4";

		var p = CreateElem("p");
		p.innerHTML = input.text;
		li.appendChild(p);

		var p = CreateElem("p");
		p.innerHTML = input.group_number;
		if (input.group_number == "0")
		   p.innerHTML = "Всем";
		li.appendChild(p);

		var p = CreateElem("p");
		var d = new Date();
		var temp = {};
		d.setTime(Date.parse(input.starting_date));
		temp.day = d.getDate();
		temp.month = d.getMonth();
		temp.year = d.getFullYear();
		if (temp.day < 10)
			temp.day = "0" + temp.day;
		if (temp.month < 10)
			temp.month = "0" + temp.month;
		p.innerHTML = temp.day + "." + temp.month + "." + temp.year;
		li.appendChild(p);

		var p = CreateElem("p");
		d.setTime(Date.parse(input.ending_date));
		temp.day = d.getDate();
		temp.month = d.getMonth();
		temp.year = d.getFullYear();
		if (temp.day < 10)
			temp.day = "0" + temp.day;
		if (temp.month < 10)
			temp.month = "0" + temp.month;
		p.innerHTML = temp.day + "." + temp.month + "." + temp.year;
		li.appendChild(p);

		var elem = CreateElem("div", null, "delete-message-button", "DELETE_message(" + input.id + ", this);", null);
		var p = CreateElem("p");
		p.appendChild(elem);
		li.appendChild(p);

		return li;
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Сортирует уведомления в модуле удаления
* @param {string} str Метод сортировки
* @throws Исключения, вызванные ошибками сортировки, изменения контента
*/
function message_sort(str)
{
	/** @private */
	function compare_number (a, b)
	{
		return a.group_number - b.group_number;
	}
	/** @private */
	function compare_date_start (a, b)
	{
		var first = new Date(a.starting_date);
		var second = new Date(b.starting_date);
		return first - second;
	}
	/** @private */
	function compare_date_end (a, b)
	{
		var first = new Date(a.ending_date);
		var second = new Date(b.ending_date);
		return first - second;
	}
	try {
		var container = document.getElementById("notification-list-container").children[0];
		var sort_indicator = document.getElementById("notification-list-header");
		for (var i = 0; i < sort_indicator.childElementCount; i++)
			sort_indicator.children[i].style.borderTop = "";
		container.innerHTML = "";
		var i =0;
		switch (str)
		{
			case 'group':
				notificationlist.sort(compare_number);
				sort_indicator.children[1].style.borderTop = "2px solid rgb(3, 169, 244)"
				break;
			case 'start':
				notificationlist.sort(compare_date_start);
				sort_indicator.children[2].style.borderTop = "2px solid rgb(3, 169, 244)"
				break;
			case 'end':
				notificationlist.sort(compare_date_end);
				sort_indicator.children[3].style.borderTop = "2px solid rgb(3, 169, 244)"
				break;
			default:
				throw Error("Ошибка сортировки");
		}
		while((notificationlist[i] != undefined) & (notificationlist[i] != null))
			{
				var li = create_li_notification(notificationlist[i]);
				container.appendChild(li);
				i++;
			}
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Загружает модуль удаления уведомлений
*
*/
function LOAD_Message_manager()
{
	try {
		var query = new Request("/admin/notifications/get_active");
		query.callback = function (Response) {
			try {
				var answer = JSON.parse(Response);
				if (answer.state != "fail")
					{
						LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_notification_manager.html", function () {
							try {
								var container = document.getElementById("notification-list-container").children[0];
								var i =0;
								notificationlist = [];
								while((answer[i] != undefined) & (answer[i] != null))
									{
										notificationlist[notificationlist.length] = answer[i];
										i++;
									}
								message_sort("group");
							}
							catch (ex) { console.error(ex); CreateEx(ex.message); }
						});
					}
				else
					CreateEx(answer.message);
			}
			catch (ex) { console.error(ex); CreateEx(ex.message); }
		}
		query.do();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Отправляет уведомления
* @param {Object} options Параметры уведомления
* @param {string} text Текст уведомления
* @param {number} number Номер уведомления при множественной отправке
* @callback callback
* @throws Исключения, вызванные ошибками отправки, изменения контента
*/
function SEND_message(options, text, number, callback)
{
	try {
		var req = {};
		var answer = {};
		answer.state = "fail";
		
		((text == "") || (text == void(0))) && (answer.message = "Не введен текст уведомления");
		((options.type == "") || (options.type == void(0))) && (answer.message = "Не указан тип уведомления");
		((options.ending_date == "") || (options.ending_date == void(0))) && (answer.message = "Не указана дата окончания");
		((options.target == "") || (options.target == void(0))) && (answer.message = "Нет получателей");
		
		if (answer.message != void(0))
			callback(answer, number);
		else
			{
				req.parameters = options;
				req.text = text;
				var query = new Request("/admin/notifications/add", req);
				query.callback = function (Response) {
					try { callback(JSON.parse(Response), number); }
					catch (ex) { answer.message = ex.message; callback(answer, number);	}
				}
				query.do();
			}
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Загружает список получателей уведомления
*
*/
function LOAD_whom_sent()
{
	function Result_find(elem)
	{
		var option_not_null = false;
		var el_array = [];
		for (var i =0; i<elem.length; i++)
			if (elem[i].checked)
				{
					option_not_null = true;
					el_array[el_array.length] = elem[i].value;
				}
		if (!option_not_null)
			return "null";
		else
			return el_array;
	}
	try {
		var options = {};
		var option_null = true;
		
		options.grade = Result_find(document.getElementById("message-filter-grade").getElementsByTagName("input"));
		options.class = Result_find(document.getElementById("message-filter-after").getElementsByTagName("input"))
		options.faculty = Result_find(document.getElementById("message-filter-department").children[1].getElementsByTagName("input"));
		options.spec = Result_find(document.getElementById("message-filter-spec").children[1].getElementsByTagName("input"));
		
		var query = new Request("/dashboard/groups/filter_apply", options);
		query.callback = function (Response) {
			try {
				var answer = JSON.parse(Response);
				if (answer.state != "fail")
					{
						var whom_list = document.getElementById("whom-sent");
						whom_list.innerHTML = "";
						var i =0;
						if (answer.groups != null)
							while (answer.groups[i] != undefined)
								{
									var elem = CreateElem("li", null, null, null, answer.groups[i]);
									whom_list.appendChild(elem);
									i++;
								}
						(answer.selected_all !== void(0)) ?	document.getElementById("whom-sent").setAttribute("selected-all", "true") : document.getElementById("whom-sent").removeAttribute("selected-all");
					}
				else
					CreateEx(answer.message);
			}
			catch (ex) { console.error(ex); CreateEx(ex.message); }
		}
		query.do();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Подготавливает к отправке уведомления из модуля множественной отправки
*
*/
function SEND_premessage_full()
{
	try {
		var body = document.body;
		var loader = new PreLoader();
		
		loader.inprogress = function () {
			try {
				var elem = document.getElementById("message-whom-status");
				if ((elem != undefined) & (elem != null))
					{
						elem.style.opacity = "";
						elem.remove();
					}
				var preset = {};
				var elem = document.getElementById("message-more-type");
				preset.type = "null";
				
				for(var i =1; i < 4; i++)
					if (elem.children[i].getElementsByTagName("input")[0].checked)
						preset.type = elem.children[i].getElementsByTagName("input")[0].value;
				
				var elem = document.getElementById("whom-sent");
				if ( document.getElementById("message-datepicker").value == "" )
					preset.ending_date = "tomorrow";
				else
					{
						var date = new Date(document.getElementById("message-datepicker").value);
						var temp = date.getUTCMonth() + 1;
						if (temp < 10)
							temp = "0" + temp;
						preset.ending_date = date.getFullYear() + temp;
						temp = date.getUTCDate();
						if (temp < 10)
							temp = "0" + temp;
						preset.ending_date += temp;
					}
				
				if (elem.getAttribute("selected-all") == "true")
					{
						preset.target = "0";
						SEND_message(preset, document.getElementById("message-more-text-input").value, null, function (Response) {
							try {
								loader.purge();
								(Response.state == "success") ? CreateEx("Успешно отправлено") : CreateEx(Response.message);
							}
							catch (ex) { console.error(ex); CreateEx(ex.message); }
						});
					}
				else if (document.getElementById("whom-sent").childElementCount > 0)
					{
						var states = CreateElem("div", "message-whom-status");
						var states_ul = CreateElem("ul");
						var elem = document.getElementById("whom-sent");
						
						for (var i = 0; i < elem.childElementCount; i++)
							{
								var states_li = CreateElem("li");
								var states_p = CreateElem("p", null, null, null, elem.children[i].innerHTML);
								var states_status = CreateElem("state", null, null, null, "Обработка");
								states_li.appendChild(states_p);
								states_li.appendChild(states_status);
								states_ul.appendChild(states_li);
							}
						
						states.appendChild(states_ul);
						var close_button = CreateElem("div", "button-close");
						
						close_button.onclick = function () {
							try {
								document.getElementById('message-whom-status').style.opacity = "";
								setTimeout( function() {
									try {
										document.getElementById('message-whom-status').remove();
									}
									catch (ex) { console.error(ex); CreateEx(ex.message); }
								}, 600);
							}
							catch (ex) { console.error(ex); CreateEx(ex.message); }
						};
						close_button.style.top = "calc( 100% - 25px )";
						states.appendChild(close_button);
						body.appendChild(states);
						
						setTimeout(function () { try { states.style.opacity = "1"; } catch (ex) { console.error(ex); CreateEx(ex.message); } }, 100);
						
						states.style.top = "calc( 50% - " + (15 * (states_ul.childElementCount + 2)) + "px)";
						states.style.height = 30 * (states_ul.childElementCount + 1) + "px";
						
						var counter = 0;
						var counter_ok = 0;
						
						for (var i = 0; i < elem.childElementCount; i++)
							{
								preset.target = elem.children[i].innerHTML;
								states.children[0].children[i].children[1].innerHTML = "Отправка";
								states.children[0].children[i].style.borderLeft = "4px solid #03a9f4";
								
								SEND_message(preset, document.getElementById("message-more-text-input").value, i, function (Response, number){
									if (Response.state == "success")
										{
											counter_ok++;
											states.children[0].children[number].children[1].innerHTML = "Успешно";
											states.children[0].children[number].style.borderLeft = "4px solid #c0ca33";
										}
									else
										{
											var error_more = CreateElem("span", null, "tooltip-left", null, Response.message);
											states.children[0].children[number].insertBefore(error_more, states.children[0].children[number].children[0]);
											error_more.style.display = "inline-block";
											
											for (var k =0; k<4; k++)
												error_more.style.marginLeft = "-" + (error_more.offsetWidth + 20) + "px";
											error_more.style.display = "";
											
											states.children[0].children[number].children[2].innerHTML = "Ошибка";
											if (Response.code !== void(0))
												states.children[0].children[number].children[2].innerHTML = "Ошибка #" + Response.code;
											
											states.children[0].children[number].style.borderLeft = "4px solid #f4511e";
										}
									counter++;
									if (counter == elem.childElementCount)
										{
											loader.purge();
											if (counter == counter_ok)
												{
													CreateEx("Успешно отправлено");
													states.style.opacity = "";
													setTimeout(function () {
														states.remove();
													}, 550);
												}
											else
												CreateEx("При отправке возникли ошибки");
										}
								});
							}
						}
					else
						{
							loader.purge();
							CreateEx("Отсутствуют получатели");
						}
			}
			catch (ex) { console.error(ex); CreateEx(ex.message); }
		}
		loader.create();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Показывает возможные типы уведомлений в малом окне отправки
*
*/
function SHOW_message_types()
{
	try {
		var block = document.getElementById("message-type-input");
		if (block.style.display == "")
			{
				block.style.display = "block";
				setTimeout(function () { block.style.opacity = "1";	}, 10);
			}
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Устанавливает тип отправляемого уведомления в малом окне отправки
* @param {string} value Тип уведомления
*/
function SET_message_type(value)
{
	try {
		var block_inputs = document.getElementById("message-type-input");
		var block_type = document.getElementById("message-type");
		if (value === "alert")
			document.getElementById("message-type-p-type").innerHTML = "Важно";
		else if (value === "note")
			document.getElementById("message-type-p-type").innerHTML = "Внимание";
				else
					document.getElementById("message-type-p-type").innerHTML = "Инфо";
		
		block_inputs.style.opacity = "0";
		block_type.setAttribute("data-messagetype", value);
		
		setTimeout(function () {
			try {
				block_inputs.style.display = "";
			}
			catch (ex) { console.error(ex); CreateEx(ex.message); }
		}, 200);
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/** Подготавливает уведомление к отправке в малом окне
*
*/
function SEND_message_small()
{
	try {
		var loader = new PreLoader();

		loader.inprogress = function() {
			try {
				var options = {};
				options.type = document.getElementById("message-type").dataset.messagetype;
			
				if (document.getElementById("message-to-input").value == "")
					options.target = "0";
				else
					options.target = document.getElementById("message-to-input").value;

				options.ending_date = "tomorrow";

				SEND_message(options, document.getElementById("message-text-input").value, null, function (Response){
					try {
						loader.purge();
						(Response.state == "success") ? CreateEx("Успешно отправлено!") : CreateEx(Response.message);
					}
					catch (ex) { loader.purge(); console.error(ex); CreateEx(ex.message); }
				});
			}
			catch (ex) { loader.purge(); console.error(ex); CreateEx(ex.message); }
		}
		loader.create();
	}
	catch (ex) { console.error(ex); CreateEx(ex.message); }
}

/*
function LOAD_GroupAdd()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Group_Create.html");
}
function CreateGroup()
{
    var body = document.body;
    var block = document.getElementById("group-creator");
    var loader = CreateLoader(body, 1, 1);
    body.appendChild(loader);
    setTimeout(function () {
        loader.style.opacity = "1";    
        setTimeout(function () {
            var group = {};
            group.group_number = document.getElementsByName("number")[0].value;
            group.grade = document.getElementsByName("grade")[0].value;
            try {
				var query = new Request("/Admin/add_group", group);
				query.callback = function (Response) {
                	loader.style.opacity = "";
					try {
						var answer = JSON.parse(ResponseText);
						setTimeout(function () {
							loader.remove();
							if (answer.state == "success")
								CreateEx("Группа успешно создана!");
							else
								CreateEx("Произошла ошибка:" + answer.message);
						}, 500);
					}
					catch (ex)
						{
							setTimeout(function () { loader.remove(); CreateEx("Ошибка: " + ex.message); }, 500);
						}
				}
				query.do();
            }
            catch (ex)
                {
                    loader.style.opacity = "";
                    setTimeout(function () {
                        loader.remove();
                        CreateEx("Произошла ошибка:" + answer.message);
                    }, 500);
                }
        }, 500);        
    }, 10);
}



function LOAD_Swap()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Swap.html", function () {
		LOAD_all_lessons();
		LOAD_grouplist();
	});
}



function LOAD_GroupChange()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Shedule_Edit.html", function (){
		LOAD_shedule_list();
		PRELOAD_shedule_edit(0, 1);
	});
}
function LOAD_ProfessorEdit()
{
    alert();
}
function LOAD_PrintDialog()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Print.html");
}
function LOAD_RoomsEdit()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Classroom_Edit.html");
}
function LOAD_Reports()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Bugs.html");
}



function LOAD_grouplist()
{
	var query = new Request("URL_TO_LOAD_GROUP_LIST");
	query.callback = function (Response) {
		try{
			var answer = JSON.parse(Response);
			if (answer.state != "fail")
				{
					for (var i =0; i < answer.groups.length; i++)
						{
							var el = CreateElem("li", null, null, "SET_group_from_list(this)", answer.groups[i]);
							document.getElementById("swap-group-choice").appendChild(el);
						}
				}
			else
				CreateEx(answer.message);
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	}
	query.do();
}
function LOAD_daylist()
{
	var req = {};
	req.group = document.getElementById("swap-group-choice").dataset.group;
	var query = new Request("URL_TO_LOAD_DAY_LIST");
	query.callback = function (Response) {
		try {
			var answer = JSON.parse(Response);
			if (answer.status != "fail")
				{
					for (var i =0; i < answer.days.length; i++)
						{
							var el = CreateElem("li", null, null, "SET_day_from_list(this)", answer.days[i]);
							document.getElementById("swap-day-choice").appendChild(el);
						}
				}
			else
				CreateEx(answer.message);
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	}
	query.do();
}
function SET_group_from_list(group)
{
	document.getElementById("swap-group-choice").children[0].innerHTML = "Выбрана группа: " + group;
	document.getElementById("swap-group-choice").setAttribute("data-group", group);
}
function SET_day_from_list(day)
{
	document.getElementById("swap-day-choice").children[0].innerHTML = "Выбран день: " + day;
	document.getElementById("swap-day-choice").setAttribute("data-day", day);
}
function LOAD_all_lessons()
{
	var query = new Request("URL_TO_LOAD_ALL_LESSONS");
	query.callback = function (Response) {
		var block = document.getElementById("lesson-list");
		try{
			var answer = JSON.parse(Response);
			if (answer.state != fail)
				{
					block.innerHTML = "";
					for (var i = 0; i < answer.lessons.length; i++)
						{
							var el = document.createElement("option");
							el.setAttribute("value", answer.lessons[i].name);
							el.innerHTML = answer.lessons[i].id;
							block.appendChild(el);
						}
				}
			else
				CreateEx(answer.message);
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	}
	query.do();
}
function LOAD_lessonlist()
{
	var req = {};
	req.group = document.getElementById("swap-group-choice").dataset.group;
	req.day = document.getElementById("swap-day-choice").dataset.day;
	var query = new Request("URL_TO_LOAD_LESSON_LIST", req);
	query.callback = function (Response) {
		try{
			var answer = JSON.parse(Response);
			if (answer.state != "fail")
				{
					for (var i =0; i<answer.lessons.length; i++)
						{
							var el = document.createElement("option");
							el.setAttribute("value", answer.lessons[i].id);
							el.innerHTML = answer.lessons[i].name;
							document.getElementById("lesson-to-swap").appendChild(el);
						}
				}
			else
				CreateEx(answer.message);
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	}
	query.do();
}
function SEND_swap()
{
	var body = document.body;
	var req = {};
	var loader = CreateLoader(body, 1, 1);
	body.appendChild(loader);
	setTimeout(function () {
		loader.style.opacity = "1";
		setTimeout(function () {
			req.group = document.getElementById("swap-group-choice").dataset.group;
			req.day = document.getElementById("swap-day-choice").dataset.day;
			req.id = document.getElementById("lesson-to-swap").value;
			var elem_find = document.getElementById("lesson-new").value;
			for (var i=0; i<document.getElementById("lesson-list").childElementCount; i++)
				if (document.getElementById("lesson-list").children[i].value == elem_find)
					{
						req.new = document.getElementById("lesson-list").children[i].innerHTML;
					}
			var query = new Request("URL_TO_SEND_SWAP", req);
			query.callback = function (Response) {
				loader.style.opacity = "";
				setTimeout(function () {
					loader.remove();
					try{
						var answer = JSON.parse(Response);
						if (answer.success)
							CreateEx("Успешно!");
						else
							CreateEx("Ошибка: " + answer.error);
					}
					catch (ex)
						{
							CreateEx(ex.message);
						}
				}, 500);
			}
			query.do();
		}, 500);
	}, 10);
}
function LOAD_shedule_list()
{
	var query = new Request("URL_TO_LOAD_LISTS_SHEDULE");
	query.callback = function (Response) {
		try {
			var answer = JSON.parse(Response);
			if (answer.state != "fail")
				{
					professorslist = [];
					var dl = CreateElem("datalist", "lesson-list");
					for (var i=0; i < answer.professors.length; i++)
						{
							professorslist[i] = {};
							professorslist[i].name = answer.professors[i].name;
							professorslist[i].id = answer.professors[i].id;
							professorslist[i].lesson = answer.professors[i].lesson;
							professorslist[i].lesson_id = answer.professors[i].lesson_id;
							var el = CreateElem("option");
							el.innerHTML = professorslist[i].lesson_id;
							el.setAttribute("value", professorslist[i].lesson);
							dl.appendChild(el);
						}
					document.body.appendChild(dl);
				}
			else
				CreateEx(answer.message);
		}
		catch (ex)
		{
			CreateEx(ex.message);
		}
	}
	query.do();
}
function LOAD_shedule_edit(day, numerator)
{
	var body = document.body;
	var loader = CreateLoader(body, 1, 1);
	body.appendChild(loader);
	var target = document.getElementById("group-shedule-edit");
	setTimeout(function () {
		loader.style.opacity = "1";
		setTimeout(function () {
			target.innerHTML = "";
			var req = {};
			req.group = getVar("group");
			req.day = {};
			req.day.number = day;
			req.day.numerator = numerator;
			var query = new Request("URL_TO_GET_SHEDULE", req);
			query.callback = function (Response) {
				try{
					var answer = JSON.parse(Response);
					if (answer.state != "fail")
						{
							var tbl = CreateElem("table", null, "admin-table");
							var thead = tbl.createTHead(-1);
							var row = thead.insertRow(-1);
							for (var i = 0; i<8; i++)
								{
									var cell = row.insertCell(-1);
									if (i != 0)
										cell.innerHTML = i + " пара";
								}
							var tbody = tbl.createTBody(-1);
							var row1 = tbody.insertRow(-1);
							var row2 = tbody.insertRow(-1);
							var cell = row1.insertCell(-1);
							cell.innerHTML = "Дисциплина";
							var cell = row2.insertCell(-1);
							cell.innerHTML = "Преподаватель";					
							for (var i =0; i<7; i++)
								{
									var cell = row1.insertCell(-1);
									var el = CreateElem("input");
									el.setAttribute("type", "text");
									el.setAttribute("list", "dl");
									el.setAttribute("placeholder", "Название предмета...");
									if (answer[i] != null)
											el.value = answer[i].lesson;
									cell.appendChild(el);
									var cell = row2.insertCell(-1);
									var el = CreateElem("input");
									el.setAttribute("type", "text");
									el.setAttribute("list", "pl" + i + 1);
									el.setAttribute("placeholder", "Преподаватель...");
									target.appendChild(CreateElem("datalist", "pl" + i + 1));
									el.onkeypress = function () {
										var block = document.getElementById(this.getAttribute("list"));
										block.innerHTML = "";
										for (var i =0; i<professorslist.length; i++)
												if(professorslist[i].name.indexOf(this.value) + 1)
												{
													var elem = CreateElem("option");
													elem.setAttribute("value", professorslist[i].name);
													block.appendChild(elem);
												}
									}
									if (answer[i] != null)
											el.value = answer[i].professor;
									cell.appendChild(el);
								}
							target.appendChild(tbl);
							loader.style.opacity = "";
							setTimeout(function () { loader.remove(); }, 500);
						}
					else
						{
							loader.style.opacity = "";
							setTimeout(function () {
								loader.remove();
								CreateEx(answer.message);
							}, 500);
						}
				}
				catch (ex)
					{
						loader.style.opacity = "";
						setTimeout(function () { loader.remove(); }, 500);						
						CreateEx(ex.message);
					}
			}
			query.do();
		},500);
	}, 10);
}
function PRELOAD_shedule_edit(numerator, day)
{
	if (numerator != null)
		{
			document.getElementById("even-choice").children[0].style.borderBottom = "";
			document.getElementById("even-choice").children[1].style.borderBottom = "";
			document.getElementById("even-choice").children[0].setAttribute("data-selected", "false");
			document.getElementById("even-choice").children[1].setAttribute("data-selected", "false");
			document.getElementById("even-choice").children[numerator].style.borderBottom = "3px solid #03a9f4";
			document.getElementById("even-choice").children[numerator].setAttribute("data-selected", "true");
			for (var i=1; i<7; i++)
				{
					if (document.getElementById("group-shedule-choice").children[i].dataset.selected == "true")
						day = i;
					document.getElementById("group-shedule-choice").children[i].setAttribute("data-selected", "false");
					document.getElementById("group-shedule-choice").children[i].style.borderBottom = "";
				}
			document.getElementById("group-shedule-choice").children[day].setAttribute("data-selected", "true");
			document.getElementById("group-shedule-choice").children[day].style.borderBottom = "3px solid #03a9f4";
			document.getElementById("group-shedule-choice").children[7].setAttribute("onclick","SEND_shedule_edit(" + day + ", " + numerator +")");
			LOAD_shedule_edit(day, numerator);
		}
	else if (day != null)
		{
			for (var i=1; i<7; i++)
				{
					document.getElementById("group-shedule-choice").children[i].setAttribute("data-selected", "false");
					document.getElementById("group-shedule-choice").children[i].style.borderBottom = "";
				}
			document.getElementById("group-shedule-choice").children[day].setAttribute("data-selected", "true");
			document.getElementById("group-shedule-choice").children[day].style.borderBottom = "3px solid #03a9f4";
			numerator = 1;
			if (document.getElementById("even-choice").children[0].dataset.selected == "true")
				numerator = 0;
			document.getElementById("even-choice").children[0].style.borderBottom = "";
			document.getElementById("even-choice").children[1].style.borderBottom = "";
			document.getElementById("even-choice").children[0].setAttribute("data-selected", "false");
			document.getElementById("even-choice").children[1].setAttribute("data-selected", "false");
			document.getElementById("even-choice").children[numerator].style.borderBottom = "3px solid #03a9f4";
			document.getElementById("even-choice").children[numerator].setAttribute("data-selected", "true");
			document.getElementById("group-shedule-choice").children[7].setAttribute("onclick","SEND_shedule_edit(" + day + ", " + numerator +")");
			LOAD_shedule_edit(day, numerator);
		}
}
function FIND_shedule_edit_id(item, where, callback)
{
	var list = document.getElementById(where);
	for (var i =0; i<list.childElementCount; i++)
		{
			if (list.children[i].value == item)
				callback(list.children[i].innerHTML);
		}
}
function SEND_shedule_edit(numerator, day)
{
	var body = document.body;
	var loader = CreateLoader(body, 1, 1);
	body.appendChild(loader);
	setTimeout(function () {
		loader.style.opacity = "1";
		setTimeout(function() {
			try{
				var tbl = document.getElementById("group-shedule-edit").children[0].tBodies[0];
				var req = {};
				req.numerator = numerator;
				req.day = day;
				req.group = getVar("group");
				req.shedule = [];
				for (var j =1; j < tbl.rows[i].cells.length; j++)
					{
						req.shedule[j] = {};
						req.shedule[j].lesson_num = j;
						req.shedule[j].lesson_id = null;
						req.shedule[j].professor_id = null;
						FIND_shedule_edit_id(tbl.rows[0].cells[j].children[0].value, tbl.rows[0].cells[j].children[0].getAttribute("list"), function (Response) {
							req.shedule[j].lesson_id = Response;
						});
						FIND_shedule_edit_id(tbl.rows[1].cells[j].children[0].value, tbl.rows[1].cells[j].children[0].getAttribute("list"), function (Response) {
							req.shedule[j].professor_id = Response;
						});			
					}
				var query = new Request("URL_TO_SEND_SHEDULE_EDIT", req);
				query.callback = function (Response) {
					loader.style.opacity = "";
					setTimeout(function () {
						loader.remove();
						try{
						var ans = Response;
						if (ans.success)
							CreateEx("Успешно");
						else
							CreateEx("Ошибка: " + ans.error);
					}
					catch (ex)
						{
							CreateEx(ex.message);
						}
					}, 500);
				}
				query.do();
			}
			catch (ex)
				{
					loader.style.opacity = "";
					setTimeout(function () {
						loader.remove();
						CreateEx(ex.message);
					}, 500);
				}
		}, 500);
	}, 10);	
}
function LOAD_classrooms()
{
	var body = document.body;
	var block = document.getElementById("table-hide-div");
	var loader = CreateLoader(block, 1, 1);
	body.appendChild(loader);
	setTimeout(function () {
		loader.style.opacity = "1";
		setTimeout(function () {
			NewXHR("URL_TO_GET_CLASSROOMS_NEED_TOMORROW", null, function (Response) {
				loader.style.opacity = "";
				try {
					var answer = JSON.parse(Response);
					for (var i =0; i<answer.roomlist.length; i++)
						{
							
						}
				}
				catch (ex)
					{
						CreateEx(ex.message);
					}
			});
		}, 500);
	}, 10);
}

function LOAD_import_shedule()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Upload.html");
}
function UPDATE_shedule_from_load()
{
    function upload(file) {
		var xhr = new XMLHttpRequest();
		var block = document.getElementById("fileupload");
		var loader = CreateLoader(block, 1);
		var body = document.body;
		block.style.height = "200px";
		body.appendChild(loader);
		loader.style.opacity = "1";
      // обработчик для закачки
      xhr.upload.onprogress = function(event) {
		  document.getElementById("upload-status").style.width = ((event.loaded / event.total) * 400) + "px";
	  }
      // обработчики успеха и ошибки
      // если status == 200, то это успех, иначе ошибка
      xhr.onload = xhr.onerror = function() {
        if (this.status == 200) {
			try {
					var answer = JSON.parse(xhr.responseText);	
					loader.style.opacity = "";
					setTimeout(function () {
						block.style.height = "";
						loader.remove();
						if (answer.success)
							CreateEx("Успешно");
						else
							CreateEx(answer.message);
					}, 500);
				}
			catch (ex)
				{
					loader.style.opacity = "";
					setTimeout(function () {
						block.style.height = "";
						loader.remove();
						CreateEx("Произошла ошибка обработки");
					}, 500);
				}
        } else {
			loader.style.opacity = "";
			setTimeout(function () {
				block.style.height = "";
				loader.remove();
				CreateEx("Произошла ошибка " + this.status);
			}, 500);
        }
      };
      xhr.open("POST", "URL_TO_UPLOAD", true);
      xhr.send(file);
    }
    var csvfile = document.getElementById("filename").files[0];
    upload(csvfile);
}
*/
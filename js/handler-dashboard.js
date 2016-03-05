var CHECK_stop = false; /*	Создает и устанавливает значение необходимости остановки опроса изменения значний у сервера	*/
var TIME_difference;
var base_check_inprogress;
/*** --- Выполняет проверку необходимости обновления блока расписания
Input:
	none
Output:
	none
***/
function CHECK_Shedule()
{
    if (!getVar("inprogress_shedule"))
        if (getVar("group"))
			{
				var req = {};
				req.group_number = getVar("group");
				var query = new Request("/dashboard/timetable/get_actual", req);
				query.callback = function (Response) {
					if (Response.state != "fail") {
						var answer = JSON.parse(Response);
							if (answer.md5 != getVar("shedule"))
								LOAD_Shedule(answer);
					}
					else
						CreateEx("Обнаружена ошибка: " + Response.message);
            	}
				query.do();
			}
	if (!getVar("group"))
		GroupChoice();
}

/*** --- Выполняет обновление блока расписания на основе полученных данных
Input:
	ANSWER - строка JSON, полученная в CHECK_Shedule
Output:
	none
***/
function LOAD_Shedule(answer)
{
	if (!getVar("group"))
		GroupChoice();
	else
		{
			setVar("inprogress_shedule", "1");
			
			document.getElementById("switch1").style.opacity = "0";
			document.getElementById("switch2").style.opacity = "0";
			
			var block = document.getElementById("ActualShedule");
			
			var loader = new PreLoader(block);			
			loader.fullscreen = false;
			loader.inprogress = function () {

			document.getElementById("switch1").style.display = "none";
			document.getElementById("switch2").style.display = "none";

			block.innerHTML = "";
			document.getElementById("ProfessorNow").className += " NothingNow";

			var div_shedule_now = document.createElement("div");
			div_shedule_now.id = "Shedule-now";

			var temp_p = document.createElement("p");
			temp_p.innerHTML = "Расписание на " + answer.today.day_name;
			div_shedule_now.appendChild(temp_p);

			var temp_ul = document.createElement("ul");

			for (var i = 1; i <= 7; i++)
				if (answer.today[i] != null)
					{
						if (answer.today[i].state)
							{
								var temp_li = document.createElement("li");
								temp_li.setAttribute("onclick", "LOAD_Professor_BASIC(" + i + ")");
								temp_li.className = "li-now";

								var temp_span = document.createElement("span");
								temp_span.innerHTML = i + ". " + answer.today[i].lesson_name;

								temp_li.appendChild(temp_span);
								temp_ul.appendChild(temp_li);

								LOAD_Professor_BASIC(i);
							}
						else
							{
								var temp_li = document.createElement("li");
								temp_li.setAttribute("onclick", "LOAD_Professor_BASIC(" + i + ")");

								var temp_span = document.createElement("span");
								temp_span.innerHTML = i + ". " + answer.today[i].lesson_name;

								temp_li.appendChild(temp_span);
								temp_ul.appendChild(temp_li);
							}
					}
			div_shedule_now.appendChild(temp_ul);

			var div_shedule_next = document.createElement("div");
			div_shedule_next.id = "Shedule-next";

			var temp_p = document.createElement("p");
			temp_p.innerHTML = "Расписание на " + answer.tomorrow.day_name;
			div_shedule_next.appendChild(temp_p);

			var temp_ul = document.createElement("ul");
			for (var i = 1; i <= 7; i++)
				if (answer.tomorrow[i] != null)
					{
						var temp_li = document.createElement("li");
						var temp_span = document.createElement("span");
						temp_span.innerHTML = i + ". " + answer.tomorrow[i].lesson_name;

						temp_li.appendChild(temp_span);
						temp_ul.appendChild(temp_li);
					}
			div_shedule_next.appendChild(temp_ul);

			document.getElementById("switch1").style.display = "";
			document.getElementById("switch2").style.display = "";

			setVar("shedule", answer.md5);
			delVar("inprogress_shedule");

			block.appendChild(div_shedule_now);
			block.appendChild(div_shedule_next);

			document.getElementById("switch1").style.opacity = "";
			document.getElementById("switch2").style.opacity = "";

			Open_Shedule("today");

			loader.purge();

			}
			loader.create();
		}
}

/*** --- Открывает/закрывает створку сегодня/завтра
Input:
	ID - указатель направления створки
Output:
	none
***/
function Open_Shedule(id)
{
	if (id == "today")
		{
			document.getElementById("Shedule-now").style.marginTop = "";
			document.getElementById("switch1").style.backgroundColor = "#03a9f4";
			document.getElementById("switch2").style.backgroundColor = "";
		}
	else
		{
			document.getElementById("Shedule-now").style.marginTop = "-500px";
			document.getElementById("switch1").style.backgroundColor = "";
			document.getElementById("switch2").style.backgroundColor = "#03a9f4";
		}
}

/*** --- Загружает информацию о текущей паре
Input:
	LESSON_NUM - указатель пары, информацию о которой необходимо получить
Output:
	none
***/
function LOAD_Professor_BASIC(lesson_num)
{
	if (!getVar("group"))
		GroupChoice();
	else
		{
			if (!getVar("inprogress_basic"))
				{
					setVar("inprogress_basic", "1");
					
					var block = document.getElementById("ProfessorNow");
                    block.className = "inline-block-main";
					
					var loader = new PreLoader(block);
					loader.fullscreen = false;
					loader.inprogress = function () {
						
						block.innerHTML = "";

							var req = {};
							req.group_number = getVar("group");
							req.lesson_number = lesson_num;

							var query = new Request("/dashboard/timetable/get_lesson_info", req);
							query.callback = function (Response) {

								var answer = JSON.parse(Response);
								if (answer.state != "fail")
									{
										var div_info = document.createElement("div");
										div_info.id = "professor-info";

										var professor_list = "";
										if (answer.multiple)
											div_info.style.width = "100%";
										if (answer.multiple)
											for (var i=0; i< answer.professor.length; i++)
												{
													professor_list += '<a onclick="LOAD_Professors(' + answer.professor_id[i] + ')">' + answer.professor[i] + '</a>';
													if (i + 1 != answer.professor.length)
														professor_list += ", ";
												}
										else
											professor_list = answer.professor;

										div_info.appendChild(CreateElem("div", "professor-name", null, null, professor_list));
										div_info.appendChild(CreateElem("div", "professor-department", null, null, answer.department));

										var pdiv = CreateElem("div", "pdiv");
										var classroom_list = "";

										if (answer.multiple)
											for (var i=0; i< answer.classroom.length; i++)
												{
													classroom_list += answer.classroom[i];
													if (i + 1 != answer.classroom.length)
														classroom_list += ", ";
												}
										else
											if (answer.classroom)
												classroom_list = answer.classroom;

										pdiv.appendChild(CreateElem("p", null, null, null, answer.lesson_name));

										div_info.appendChild(CreateElem("div", "time", null, null, answer.time));
										div_info.appendChild(pdiv);
										div_info.appendChild(CreateElem("div", "classroom", null, null, "Аудитории: " +  classroom_list));

										var photo = CreateElem("div", "professor-photo", null, "LOAD_Professors(" + answer.professor_id + ")", null);

										if (!answer.multiple)
											{
												var img = document.createElement("img");
												img.setAttribute("src", answer.photo_url);
												photo.appendChild(img);
											}

										block.appendChild(div_info);

										if (!answer.multiple)
											block.appendChild(photo);
										
										loader.purge();
										delVar("inprogress_basic");
										
										if (base_check_inprogress != null)
											base_check_inprogress = null;
									}
								else
									CreateEx(answer.message);
						}
						query.do();
					}
				   loader.create();
				}
		}
}

/*** --- Выполняет проверку необходимости обновления блока уведомлений
Input:
	none
Output:
	none
***/
function CHECK_alerts()
{
    if (!getVar("inprogress_alerts"))
        if (getVar("group"))
			{
				var req = {};
				req.group_number = getVar("group");
				var query = new Request("/dashboard/notifications/get_for_group", req);
				query.callback = function (Response) {
					var answer = JSON.parse(Response);
					if (answer.state != "fail")
						{
							if (answer.md5 != getVar("alerts"))
								LOAD_alerts(answer);
						}
					else
							CreateEx("Обнаружена ошибка: " + answer.message);
            	}
				query.do();
			}
	if (!getVar("group"))
		GroupChoice();
}

/*** --- Обновляет блок уведомлений на основе полученных данных
Input:
	ANSWER - JSON строка, полученная в CHECK_alerts()
Output:
	none
***/
function LOAD_alerts(answer)
{
    setVar("inprogress_alerts", "1");
	
	var block = document.getElementById("Alerts");
	var loader = new PreLoader(block);
	
	loader.fullscreen = false;
	loader.inprogress = function () {
		
		block.innerHTML = "";
		var p_block = CreateElem("p", null, null, null, "Уведомления");
		var temp_ul = CreateElem("ul");
		
		var data = '<p>Уведомления</p><ul>';
		var i = 0;
		
		while (answer[i])
			{
				var temp_li = CreateElem("li", null, answer[i].state, null, answer[i].text);
				temp_ul.appendChild(temp_li);
				i++;
			}
		
		setVar("alerts", answer.md5);
		delVar("inprogress_alerts");
		
		block.appendChild(p_block);
		block.appendChild(temp_ul);
		
		loader.purge();
	}
	loader.create();
}

/*** --- Выполяется при первоначальной загрузке страницы, выбирает необходимый блок загрузки
Input:
	none
Output:
	none
***/
function DoOnLoad()
{
	ClearBody();
	if (!getVar("group"))
		GroupChoice();
	else
        Dashboard_Load();
}

/*** --- Получает время сервера
Input:
	none
Output:
	none
***/
function GET_time()
{
    var Date_now = new Date();
	var query = new Request("/service/get_time");
	query.callback = function (Response) {
		var answer = JSON.parse(Response);
		if (answer.state != "fail")
				TIME_difference = answer.now_time - ((Date_now.getTime() - Date_now.getMilliseconds()) / 1000);
		else
			CreateEx(answer.message);
		var time_toset = Date_now.getTime() + TIME_difference;
		Date_now.setTime(time_toset);
		var str_time = Date_now.toTimeString();
		document.getElementById("time-now").innerHTML = "";
		for (var i =0; i<5; i++)
			document.getElementById("time-now").innerHTML += str_time[i];
		var DASHBOARD_TIME = setInterval(function () {
		if (CHECK_stop)
				clearInterval(DASHBOARD_TIME);
		else
			{
				var Date_now = new Date();
				var time_toset = Date_now.getTime() + TIME_difference;
				Date_now.setTime(time_toset);
				var str_time = Date_now.toTimeString();
				document.getElementById("time-now").innerHTML = "";
				for (var i =0; i<5; i++)
					document.getElementById("time-now").innerHTML += str_time[i];
			}
		}, 10000);
    }
	query.do();
}

/*** --- Выполняет загрузку информации в скелет главного меню, запускает циклические проверки
Input:
	none
Output:
	none
***/
function Dashboard_CHECK()
{
    base_check_inprogress = true;
	
	delVar("shedule");
	delVar("alerts");
	delVar("inprogress_alerts");
	delVar("inprogress_shedule");
	delVar("inprogress_basic");
	
	CHECK_alerts();
	GET_time();
	CHECK_Shedule();
	
	var DASHBOARD_CHECKER = setInterval(function () {		
		if (CHECK_stop)
			clearInterval(DASHBOARD_CHECKER);
		else
			{
				if (getVar("group") == false)
					{
						CHECK_stop = true;
						GroupChoice();
					}
				else
					{
						CHECK_alerts();
						CHECK_Shedule();
					}
			}
   }, 30000);
}

/*** --- Загружает полное расписание группы на экран
Input:
	none
Output:
	none
***/
function LOAD_fullShedule()
{
	var body = document.body;
	var loader = new PreLoader();
	
	loader.before = function () { ClearBody(); CHECK_stop = true; }
	loader.inprogress = function () {
			var req = {};
			req.group_number = getVar("group");
		
			var query = new Request("/dashboard/timetable/get_week", req);
			query.callback = function (Response) {
				
				var answer = JSON.parse(Response);
                if (answer.state != "fail")
                    {
                        var dashmin = Math.min(answer.even.min,answer.uneven.min);
                        var dashmax = Math.max(answer.even.max,answer.uneven.max);
						
						var div_button = CreateElem("div", "button-close", null, "Dashboard_Load()", null);
						var header = CreateElem("div", "header", null, null, "Расписание группы " + getVar("group"));
						var screen = CreateElem("div", "screen");
						var table1 = CreateElem("table", null, "table-full");
						var caption = CreateElem("caption", null, null, null, "Числитель");
						
						table1.appendChild(caption);
						
						var thead = table1.createTHead(-1);
						var row = thead.insertRow(-1);
						var cell = row.insertCell(-1);
						cell.innerHTML = "День недели";
						
						var day_name = ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"];
						for (var i = dashmin; i<=dashmax; i++)
							{
								var cell = row.insertCell(-1);
								cell.innerHTML = i + " пара";
							}
						
						var tablebody = table1.createTBody(-1);
                        for (var i =1; i<7; i++)
                            {
                                var row = tablebody.insertRow(-1);
								var cell = row.insertCell(-1);
								cell.innerHTML = day_name[i-1];
								
                                for (var j=dashmin; j<=dashmax; j++)
									{
										var cell = row.insertCell(-1);
										if (answer.even[i][j] != null)
											{
												var temp = CreateElem("div", null, "more-info", null, answer.even[i][j].professor);
												cell.appendChild(temp);
												cell.innerHTML += answer.even[i][j].lesson_name;
											}
									}
                            }

						screen.appendChild(table1);
						
						var table1 = CreateElem("table", null, "table-full");
						var caption = CreateElem("caption", null, null, null, "Знаменатель");
						
						table1.appendChild(caption);
						table1.style.marginBottom = "20px";
						
						var thead = table1.createTHead(-1);
						var row = thead.insertRow(-1);
						var cell = row.insertCell(-1);
						cell.innerHTML = "День недели";
						
						var day_name = ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"];
						for (var i = dashmin; i<=dashmax; i++)
							{
								var cell = row.insertCell(-1);
								cell.innerHTML = i + " пара";
							}
						
						var tablebody = table1.createTBody(-1);
						
                        for (var i =1; i<7; i++)
                            {
                                var row = tablebody.insertRow(-1);
								var cell = row.insertCell(-1);
								cell.innerHTML = day_name[i-1];
                                for (var j=dashmin; j<=dashmax; j++)
									{
										var cell = row.insertCell(-1);
										if (answer.uneven[i][j] != null)
											{
												var temp = CreateElem("div", null, "more-info", null, answer.uneven[i][j].professor);
												cell.appendChild(temp);
												cell.innerHTML += answer.uneven[i][j].lesson_name;
											}
									}
                            }
						
						screen.appendChild(table1);
						body.appendChild(div_button);
						body.appendChild(header);
						body.appendChild(screen);
						var blocks = document.getElementsByClassName("more-info");
						for (var i = 0; i<blocks.length; i++)
							{
								blocks[i].style.display = "block";
								blocks[i].style.top = -5 - blocks[i].offsetHeight + "px";
								blocks[i].style.display = "";
							}
						loader.purge();
                    }
			}
		query.do();
	}
	loader.create();
}

/*** --- Загружает скелет преподавателей на экран
Input:
	ID - желаемый ID преподователя для вывода, при его null, выводится первый в списке
Output:
	none
***/
function LOAD_Professors(id)
{
	var body = document.body;
	var loader = new PreLoader();
	
	delVar("inprogress_professor");
	CHECK_stop = true;
    loader.before = function () { ClearBody(); }
	
	loader.inprogress = function () {
		var div_button = CreateElem("div", "button-close", null, "Dashboard_Load()", null);
		var header = CreateElem("div", "header", null, null, "Информация о преподавателе");
		
		var screen = CreateElem("div", "screen");
		var div_left = CreateElem("div", "left-side");
		
		var temp_ul = CreateElem("ul");
		var query = new Request("/dashboard/professors/get_list");
		
		screen.style.overflowY = "hidden";
		
		query.callback = function (Response) {	
			var answer = JSON.parse(Response);
			if (answer.state != "fail") {
				var i = 0;
				while (answer[i] != null)
					{
						var temp_li = CreateElem("li", null, null, null, answer[i].professor);
						temp_li.setAttribute("number", answer[i].id);
						temp_ul.appendChild(temp_li);
						i++;
					}
				var div_right = CreateElem("div", "right-side");
				
				div_left.appendChild(temp_ul);
				
				screen.appendChild(div_left);
				screen.appendChild(div_right);
				
				if ((id == undefined) || (id == null))
					id = answer[0].id;
				body.appendChild(div_button);
				body.appendChild(header);
				body.appendChild(screen);
				LOAD_Professor(id);
				for (i = 0; i< document.getElementsByTagName("ul").length; i++)
					{
						document.getElementsByTagName("ul")[i].onclick = function(e) { if (e.target.getAttribute("number") != null) LOAD_Professor(e.target.getAttribute("number")); };
					}
				loader.purge();
			}
		}
		query.do();
	}
	loader.create();
}

/*** --- Загружает информацию конкретного преподавателя в скелет
Input:
	ID - идентификатор преподавателя
Output:
	none
***/
function LOAD_Professor(id)
{
	if (!getVar("inprogress_professor"))
		{
			var block = document.getElementById("right-side");
            var loader = new PreLoader(block);

			setVar("inprogress_professor", "1");
			block.style.transition = "0.5s";
			loader.transparent = true;
			loader.fullscreen = false;
			
			loader.before = function () { block.style.opacity = 0; }
			loader.inprogress = function () {
				
				setTimeout(function () {

					block.innerHTML = "";

					var req = {};
					req.professor_id = id;

					var query1 = new Request("/dashboard/professors/get_professor_state", req);
					query1.callback = function (Response) {

						var answer = JSON.parse(Response);
						if (answer.state != "fail")
						{
							var teacher_photo = CreateElem("div", "teacher-photo");

							var teacher_img = CreateElem("img");
							teacher_img.setAttribute("src", answer.photo_url);
							teacher_photo.appendChild(teacher_img); //RDY


							var teacher_info = CreateElem("div", "teacher-info");
							teacher_info.appendChild(CreateElem("p", "teacher-name", null, null, answer.name));
							teacher_info.appendChild(CreateElem("p", "teacher-dep", null, null, answer.department));
							teacher_info.appendChild(CreateElem("p", "teacher-now", null, null, "Апанасевич С.А. сегодня не преподает"));

							var req = {};
							req.professor_id = id;
							var query2 = new Request("/dashboard/professors/get_professor_timetable", req);

							query2.callback = function (Response) {
								answer = JSON.parse(Response);
								if (answer.state != "fail")
								{

									var dashmin = Math.min(answer.even.min,answer.uneven.min);
									var dashmax = Math.max(answer.even.max,answer.uneven.max);

									var table1 = CreateElem("table", null, "table-full");
									var caption = CreateElem("caption", null, null, null, "Числитель");
									table1.appendChild(caption);

									var thead = table1.createTHead(-1);
									var row = thead.insertRow(-1);
									var cell = row.insertCell(-1);
									cell.innerHTML = "День недели";
									for (var i = dashmin; i<=dashmax; i++)
										{
											var cell = row.insertCell(-1);
											cell.innerHTML = i + " пара";
										}
									var tbody = table1.createTBody(-1);
									var day_name = ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"];
									for (var i =1; i<7; i++)
										{
											var row = tbody.insertRow(-1);
											var cell = row.insertCell(-1);
											cell.innerHTML = day_name[i-1];
											for (var j=dashmin; j<=dashmax; j++)
												{
													var cell = row.insertCell(-1);
													if (answer.even[i][j] != null)
														{
															cell.appendChild(CreateElem("div", null, "more-info", null, answer.even[i][j].lesson_name));
															cell.innerHTML += answer.even[i][j].group_number;
														}
												}
										}

									var table2 = CreateElem("table", null, "table-full");
									table2.style.marginBottom = "20px";
									var caption = CreateElem("caption", null, null, null, "Знаменатель");
									table2.appendChild(caption);

									var thead = table2.createTHead(-1);
									var row = thead.insertRow(-1);
									var cell = row.insertCell(-1);
									cell.innerHTML = "День недели";
									for (var i = dashmin; i<=dashmax; i++)
										{
											var cell = row.insertCell(-1);
											cell.innerHTML = i + " пара";
										}
									var tbody = table2.createTBody(-1);
									var day_name = ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"];
									for (var i =1; i<7; i++)
										{
											var row = tbody.insertRow(-1);
											var cell = row.insertCell(-1);
											cell.innerHTML = day_name[i-1];
											for (var j=dashmin; j<=dashmax; j++)
												{
													var cell = row.insertCell(-1);
													if (answer.uneven[i][j] != null)
														{
															cell.appendChild(CreateElem("div", null, "more-info", null, answer.uneven[i][j].lesson_name));
															cell.innerHTML += answer.uneven[i][j].group_number;
														}
												}
										}

									block.appendChild(teacher_photo);
									block.appendChild(teacher_info);
									block.appendChild(table1);
									block.appendChild(table2);

									loader.purge();

									var blocks = document.getElementsByClassName("more-info");
									for (var i = 0; i<blocks.length; i++)
										{
											blocks[i].style.display = "block";
											blocks[i].style.top = -5 - blocks[i].offsetHeight + "px";
											blocks[i].style.display = "";
										}

									block.style.opacity = "";
									delVar("inprogress_professor");
								}
							}
							query2.do();
						}
					}
					query1.do();
				}, 500);
			}
			loader.create();
		}
}

/*** --- Загружает окно авторизации
Input:
	none
Output:
	none
***/
function AuthLoad()
{
	CHECK_stop = true;
	delVar("inprogress_Auth");
	
	var loader = new PreLoader();
	
	loader.before = function () { ClearBody(); }
	loader.inprogress = function () {
		var query = new Request("/Application/Views/Skeletons/Main_Auth.html");
		query.callback = function (Response) {

			var temp = document.createElement("div"); // заглушка, поскольку innerHTML += вызывает перезагрузку DOM элементов,
			temp.innerHTML = Response; // что приводит к потере контроля за PreLoader
			document.body.appendChild(temp.children[0]); // TODO: найти более удачный способ решения
			loader.purge();
			
		}
		query.do();
	}
	loader.create();
}

/*** --- Проверяет авторизацию, авторизирует в случае успеха
Input:
	none - получает данные из элементов
Output:
	none
***/
function AuthTry()
{
    if (!getVar("inprogress_Auth"))
        {
            setVar("inprogress_Auth", 1);
			var query = new Request("service/authorization");
			query.body = "login=" + encodeURIComponent(document.getElementById("Auth-login").value) + "&password=" + encodeURIComponent(document.getElementById("Auth-password").value);
			query.noJSON = true;
            query.callback = function (Response) {
                delVar("inprogress_Auth");
				if (Response == "")
						document.getElementsByTagName("form")[0].submit();
					else
						{
							var block = document.getElementById("AuthAnswer");
							block.innerHTML = "Неверные данные";
							block.style.marginTop = "0px";
							setTimeout(function () { block.style.marginTop = ""; setTimeout(function() { block.innerHTML = ""; }, 500); }, 2000);
						}
			}
			query.do();
        }
}
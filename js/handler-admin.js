var TIME_difference;
var professorslist, lessonlist;
function LOAD_SkeletonsFullscreen(route, callback)
{
    var body = document.body;
    var loader = CreateLoader(body, 1, 1);
	body.style.opacity = "0";
	setTimeout(function (){
		ClearBody();
		body.style.opacity = "";
        document.body.appendChild(loader);
		loader.style.opacity = "1";
		setTimeout(function () {
			NewXHR(route, null, function (data){
				if (data.check != false)
					{
						loader.style.opacity = "";
                        body.style.opacity = "0";
						setTimeout(function () {
                            body.style.opacity = "";
							body.innerHTML = data;							
							loader.remove();
							if ((callback != undefined) || (callback != null))
								callback();
						}, 600);
					}
				else
					{
						//exception handler
						CreateEx("Обнаружена ошибка: " + data.status);
					}});
		}, 600);
	}, 600);	
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
function DoOnLoad()
{
	LOAD_AdminDash();
}
function LOAD_AdminDash()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Dashboard.html");
}
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
            NewXHR("/Admin/add_group", "json_input=" + JSON.stringify(group), function(ResponseText){
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
            });
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
function LOAD_Message()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Message.html", function () {
		NewXHR("/Dashboard/get_faculty_list", null, function (Response){
			try {
					var answer = JSON.parse(Response);
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
			catch (ex)
				{
					CreateEx("Ошибка" + ex.message);
				}
		});
		NewXHR("/Dashboard/get_specializations_list", null, function (Response){
			try {
					var answer = JSON.parse(Response);
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
			catch (ex)
				{
					CreateEx("Ошибка" + ex.message);
				}
		});
        LOAD_whom_sent();
	});
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
function SEND_message(options, text, number, callback)
{
    var req = {};
    req.parameters = options;
    req.text = text;
	NewXHR("/Admin/add_notification", "json_input=" + JSON.stringify(req), function(Response) {
		try 
			{
				callback(JSON.parse(Response), number);
			}
		catch (ex)
			{
				var answer = {};
				answer.state = "fail";
				answer.message = ex.message;
				callback(answer, number);
			}
	});  
}
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
	var options = {};
	var option_null = true;
	options.grade = Result_find(document.getElementById("message-filter-grade").getElementsByTagName("input"));
	options.class = Result_find(document.getElementById("message-filter-after").getElementsByTagName("input"))
	options.faculty = Result_find(document.getElementById("message-filter-department").children[1].getElementsByTagName("input"));
	options.spec = Result_find(document.getElementById("message-filter-spec").children[1].getElementsByTagName("input"));
    for (var obj in options)
        if (options[obj] != "null")
            option_null = false;
    if (option_null)
        document.getElementById("whom-sent").setAttribute("selected-all", "true");
    else
        document.getElementById("whom-sent").removeAttribute("selected-all");
  	NewXHR("/Dashboard/get_filtered_groups", "json_input=" + JSON.stringify(options), function (Response) {
		try {
			var answer = JSON.parse(Response);
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
		}
		catch (ex)
			{
				CreateEx(ex.message);
				console.log(ex);
			}
    });

}
function SEND_premessage_full()
{

    var body = document.body;
    var loader = CreateLoader(body, 1, 1);
    body.appendChild(loader);
    setTimeout(function () {
        loader.style.opacity = "1";
		var elem = document.getElementById("message-whom-status");
		if ((elem != undefined) & (elem != null))
			elem.style.opacity = "";
        setTimeout(function () {
			if ((elem != undefined) & (elem != null))
				elem.remove();
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
				preset.ending_date = document.getElementById("message-datepicker").value;
			if (elem.getAttribute("selected-all") == "true")
				{
					preset.target = "0";
					SEND_message(preset, document.getElementById("message-more-text-input").value, null, function (Response) {
						loader.style.opacity = "";
						setTimeout(function () {
							loader.remove();
							setTimeout(function () {
								if (Response.state == "success")
									CreateEx("Успешно отправлено");
								else
									CreateEx(Response.message);
							}, 100);
						}, 550);
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
						document.getElementById('message-whom-status').style.opacity = "";
						setTimeout( function() {
							document.getElementById('message-whom-status').remove();
						}, 600);
					};
					close_button.style.top = "calc( 100% - 25px )";
					states.appendChild(close_button);
					body.appendChild(states);
					setTimeout(function () { states.style.opacity = "1"; }, 100);
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
                                        states.children[0].children[number].children[1].innerHTML = Response.message;
                                        states.children[0].children[number].style.borderLeft = "4px solid #f4511e";
                                    }
								counter++;
                                if (counter == elem.childElementCount)
                                    {
                                        loader.style.opacity = "";
                                        setTimeout(function () {
                                            loader.remove();
                                            setTimeout(function () {
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
                                            }, 100);
                                        }, 550);
                                    }
							});
						}
				}
			else
				{
					loader.style.opacity = "";
					setTimeout(function () {
						loader.remove();
						setTimeout(function () {
							CreateEx("Отсутствуют получатели");
						}, 100);
					}, 550);
				}
        }, 500);
    }, 10);
}
function SHOW_message_types()
{
	var block = document.getElementById("message-type-input");
	if (block.style.display == "")
		{
			block.style.display = "block";
			setTimeout(function () {
				block.style.opacity = "1";
			}, 10);
		}
}
function SET_message_type(value)
{
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
		block_inputs.style.display = "";
	}, 200);
}
function SEND_message_small()
{
    var body = document.body;
    var loader = CreateLoader(body, 1, 1);
    body.appendChild(loader);
    setTimeout(function () {
        loader.style.opacity = "1";
        setTimeout(function () {
			var options = {};
			options.type = document.getElementById("message-type").dataset.messagetype;
			if (document.getElementById("message-to-input").value == "")
				options.target = "0";
			else
				options.target = document.getElementById("message-to-input").value;
			options.ending_date = "tomorrow";
			SEND_message(options, document.getElementById("message-text-input").value, null, function (Response){
				loader.style.opacity = "";
				setTimeout(function () {
					loader.remove();
					setTimeout(function () {
						if (Response.state == "success")
							CreateEx("Успешно отправлено!");
						else
							CreateEx(Response.message);
					}, 100);
				}, 500);
			});
        }, 500);
    }, 10);
}
function LOAD_grouplist()
{
	NewXHR("URL_TO_LOAD_GROUP_LIST", null, function (Response){
		try{
			var answer = JSON.parse(Response);
			for (var i =0; i < answer.groups.length; i++)
				{
					var el = CreateElem("li", null, null, "SET_group_from_list(this)", answer.groups[i]);
					document.getElementById("swap-group-choice").appendChild(el);
				}
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	});
}
function LOAD_daylist()
{
	var req = {};
	req.group = document.getElementById("swap-group-choice").dataset.group;
	NewXHR("URL_TO_LOAD_DAY_LIST", "json_input=" + JSON.stringify(req), function (Response){
		try{
			var answer = JSON.parse(Response);
			for (var i =0; i < answer.days.length; i++)
				{
					var el = CreateElem("li", null, null, "SET_day_from_list(this)", answer.days[i]);
					document.getElementById("swap-day-choice").appendChild(el);
				}
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	});	
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
	NewXHR("URL_TO_LOAD_ALL_LESSONS", null, function (Response) {
		var block = document.getElementById("lesson-list");
		try{
			var answer = JSON.parse(Response);
			block.innerHTML = "";
			for (var i = 0; i < answer.lessons.length; i++)
				{
					var el = document.createElement("option");
					el.setAttribute("value", answer.lessons[i].name);
					el.innerHTML = answer.lessons[i].id;
					block.appendChild(el);
				}
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	});
}
function LOAD_lessonlist()
{
	var body = {};
	body.group = document.getElementById("swap-group-choice").dataset.group;
	body.day = document.getElementById("swap-day-choice").dataset.day;
	NewXHR("URL_TO_LOAD_LESSON_LIST", "json_input=" + JSON.stringify(body), function (Response) {
		try{
			var answer = JSON.parse(Response);
			for (var i =0; i<answer.lessons.length; i++)
				{
					var el = document.createElement("option");
					el.setAttribute("value", answer.lessons[i].id);
					el.innerHTML = answer.lessons[i].name;
					document.getElementById("lesson-to-swap").appendChild(el);
				}
		}
		catch (ex)
			{
				CreateEx(ex.message);
			}
	});
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
			NewXHR("URL_TO_SEND_SWAP", "json_input=" + JSON.stringify(req), function (Response) {
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
			});
		}, 500);
	}, 10);
}
function LOAD_shedule_list()
{
	NewXHR("URL_TO_LOAD_LISTS_SHEDULE", null, function (Response) {
	try {
		var answer = JSON.parse(Response);
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
	catch (ex)
	{
		CreateEx(ex.message);
	}
		   });
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
			req.group = getCookie("group");
			req.day = {};
			req.day.number = day;
			req.day.numerator = numerator;
			NewXHR("URL_TO_GET_SHEDULE", "json_input=" +  JSON.stringify(req), function (Response) {
				try{
					var answer = JSON.parse(Response);
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
				catch (ex)
					{
						loader.style.opacity = "";
						setTimeout(function () { loader.remove(); }, 500);						
						CreateEx(ex.message);
					}
			});
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
				req.group = getCookie("group");
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
				NewXHR("URL_TO_SEND_SHEDULE_EDIT", "json_input=" + JSON.stringify(req), function (ResponseText) {
					loader.style.opacity = "";
					setTimeout(function () {
						loader.remove();
						try{
						var ans = ResponseText;
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
				});
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
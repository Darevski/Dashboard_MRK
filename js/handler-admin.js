var TIME_difference;
function LOAD_SkeletonsFullscreen(route)
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
function LOAD_Message()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Message.html");
}
function LOAD_Swap()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Swap.html");
}
function LOAD_GroupChange()
{
    LOAD_SkeletonsFullscreen("/Application/Views/Skeletons/Admin_Group_Create.html");
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
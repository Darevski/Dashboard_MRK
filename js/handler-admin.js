function LOAD_import_shedule()
{
    NewXHR("/Application/Views/Skeletons/Admin_Upload.html", null, function (answer){
        document.body.innerHTML = answer;
    });
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
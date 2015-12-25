function LOAD_import_shedule()
{
    NewXHR("/Application/Views/Skeletons/Admin_Upload.html", null, function (answer){
        document.body.innerHTML = answer;
    });
}
function UPDATE_shedule_from_load()
{
    function upload(file) {
        document.getElementById("fileupload").style.height = "200px";
      var xhr = new XMLHttpRequest();
      // обработчик для закачки
      xhr.upload.onprogress = function(event) {
          document.getElementById("upload-status").style.width = ((event.loaded / event.total) * 400) + "px";
          console.log(event.loaded + ' / ' + event.total);
      }
      // обработчики успеха и ошибки
      // если status == 200, то это успех, иначе ошибка
      xhr.onload = xhr.onerror = function() {
        if (this.status == 200) {
          console.log("success");
        } else {
          console.log("error " + this.status);
        }
      };
      xhr.open("POST", "URL_TO_UPLOAD", true);
      xhr.send(file);
    }
    var csvfile = document.getElementById("filename").files[0];
    upload(csvfile);
}
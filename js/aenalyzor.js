  /*
    Projektarbete
    Webbprogrammering (DT058G) / Webbptjänster (DT058G)
    HT 2017
    Andreas Edin
    andreas.edin@yahoo.com
*/
//
// Wait for DOM to be loaded.
document.addEventListener("DOMContentLoaded", function () {
    //
    //Gobal variables
    var fileName = "";
    // Add eventlistener to the button with id="submitUpload".
    document.getElementById("submitUpload").addEventListener("click", function () {
        uploadFile();
    });
    //
    //Upload file to localhost.
    function localUpload(){
        var url = "http://localhost/webservices_dt117g/projekt/upload/filemanager.php"
        var fd = new FormData();
        fd.append("fileToUpload", document.getElementById('fileToUpload').files[0]);
        var http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(fd);
        http.onreadystatechange = function() {
            if(http.readyState == 4 && http.status == 200) {
                var myObj = JSON.parse(this.responseText);              
                fileName = myObj['filename'];
            }
        }
    }
    //
    //Upload file to a server where it will be public.
    //This is a requirement from the imagga API.
    function publicUpload(){
        var url = "http://studenter.miun.se/~aned1602/dt117g/projekt/upload.php";
        var fd = new FormData();
        fd.append("fileToUpload", document.getElementById('fileToUpload').files[0]);
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", url, true);
        xhttp.send(fd);
        xhttp.onreadystatechange = function() {
            if(xhttp.readyState == 4) {
            }
        }  
    }
    //
    //Function that executes the work in the right order
    //This function is called by the eventlistener above.
    function uploadFile(){
        localUpload();
        publicUpload();
        alert("File uploaded.\nWaiting to get analyze result.");
        window.location.href = "index.php?upload&filename=" + fileName;
    }
});
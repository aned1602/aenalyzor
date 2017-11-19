  /*
    Projektarbete
    Webbprogrammering (DT058G) / Webbptjänster (DT058G)
    HT 2017
    Andreas Edin
    andreas.edin@yahoo.com
*/

// Wait for DOM to be loaded.
document.addEventListener("DOMContentLoaded", function () {
    //
    //Global variables
    var fileName = "";
    //
    // Add eventlistener to the buttons "Radera/ändra".
    document.getElementById("view_all").addEventListener("click", function () {
        fileName = event.target.id;
        if (confirm("Delete photo?")){
            deleteFile(fileName);
        }else{
            alert("Mabye later then!");
        }
    });
    //
    //Send DELETE request to the API 'filemanager_api.php'   
    function deleteFile(fileToDelete){
        var url = "http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/" + fileToDelete;
        var http = new XMLHttpRequest();
        http.open("DELETE", url, true);
        http.send();           
        http.onreadystatechange = function() {
            if(http.readyState == 4 && this.status == 200) {
                alert("File deleted");
                location.reload();
            }
        }     
    }    
});
<?php
	/*
    Projektarbete
    Webbprogrammering (DT058G) / Webbptjänster (DT058G)
    HT 2017
    Andreas Edin
    andreas.edin@yahoo.com
    */
    //
    //This is a small API with only a POST method. But it actually
    //does the work of a DELETE request. This is a nescessary
    //workaround since the server it's placed on doesn't
    //allow the verb DELETE.
    //
    //Global variables
	$target_dir = "../../writeable/uploads/";
	$uploadOk = 1;
	$method = $_SERVER['REQUEST_METHOD'];
	if ($method == "POST"){
	    $target_filename = $_POST['fileToDelete'];
	    $target_file = $target_dir . $target_filename;
	    //
	    //Check if the file exists before
	    //trying to delete it.
	    if (file_exists($target_file)){
	        unlink($target_file);
	    } else {
	        http_response_code(400);
	    } 
	}
?>
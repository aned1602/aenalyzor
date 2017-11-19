<?php
    /*
    Projektarbete
    Webbprogrammering (DT058G) / Webbptjänster (DT058G)
    HT 2017
    Andreas Edin
    andreas.edin@yahoo.com
    */
    //Global variables
    $target_url = "http://localhost/webservices_dt117g/projekt/upload/photos/";
    $target_dir = "photos/";
    $request =""; 
    $uploadOk = 1;
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "POST"){
        //
        //Add a timestamp to the filename so it will be unique
        $millis = round(microtime(true) * 1000);
        $target_filename = basename($_FILES["fileToUpload"]["name"]);
        $target_filename = $millis . '_' . $target_filename;
        $target_file = $target_dir . $target_filename;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        //
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            http_response_code(415);
            exit();
        }
        //
        // Check if file already exists
        if (file_exists($target_file)) {
            http_response_code(409);
            exit();
        }
        //
        // Check file size, must be <500k
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            http_response_code(413);    
            exit();
        }
        //
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            http_response_code(415);
            exit();
        }
        //
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            http_response_code(400);
            //
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $result_array['url'] = $target_url;
                $result_array['filename'] = $target_filename;
                echo json_encode($result_array, JSON_UNESCAPED_SLASHES);
            } else {
                http_response_code(400);
            }
        }   
    } else {
        http_response_code(400); 
    }  
?>
<?php
    /*
    Projektarbete
    Webbprogrammering (DT058G) / Webbptjänster (DT058G)
    HT 2017
    Andreas Edin
    andreas.edin@yahoo.com
    */
    //Global variables
    $target_url = "http://studenter.miun.se/~aned1602/writeable/uploads/";
    $target_dir = "../../writeable/uploads/";
    $uploadOk = 1;
    $method = $_SERVER['REQUEST_METHOD'];
    $request = "";
    if ($method == "POST"){
        //
        //Set filename to 'ae_photo.jpg'. This is the 
        //filename that will be used when sending a request to
        //imagga's API, to get an analyze of the photo. 
        //If it already exists an old file with the same name
        //it will be overwritten. 
        $target_filename = "ae_photo.jpg";
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
        // Check file size
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
                $result_array['url'] = $target_url . $target_filename;
                echo json_encode($result_array, JSON_UNESCAPED_SLASHES);
            } else {
                http_response_code(400);
            }
        }   
    }
?>
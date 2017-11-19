<?php
    /*
    Projektarbete
    Webbprogrammering (DT058G) / Webbptjänster (DT058G)
    HT 2017
    Andreas Edin
    andreas.edin@yahoo.com

    This is an API for managing data in a database. The data in the database comes from analyzes done
    on photos.

    THE API
    *******
    url to api: http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/  (this is
    later on referred to as url)

    Available methods: GET, POST, DELETE
    GET
        Ex1: GET url will return a Json string containing the filenames of all the photos 
        in the database and a url to where the files are stored. Ex:
        {"url":"http://localhost/webservices_dt117g/projekt/upload/photos/","files":["bild2.jpg","picture12.jpg","ae1.jpg"]}

        Ex2: GET url + filename (Ex:http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/picture2.jpg) will return all stored data related tho the picture with the specifyed filename, ex: {"url":"http://localhost/webservices_dt117g/projekt/upload/photos/","photo":{"filename":"picture2.jpg","date":"2017-11-08","time":"10:44:05","totalvalue":"86","tags":{"tag1":{"name":"happy","value":"29"},"tag2":{"name":"happiness","value":"25"},"tag3":{"name":"smiling","value":"25"},"tag4":{"name":"smile","value":"22"},"tag5":{"name":"fun","value":"18"}}}}

    POST
        This metod will need json indata for example: {"url":"http://localhost/webservices_dt117g/projekt/upload/photos/","photo":{"filename":"picture2.jpg","date":"2017-11-08","time":"15:59:20","totalvalue":86,"tags":{"tag1":{"name":"happy","value":29},"tag2":{"name":"happiness","value":25},"tag3":{"name":"smiling","value":25},"tag4":{"name":"smile","value":22},"tag5":{"name":"fun","value":18}}}}
        Th indata is stored in the database.

    DELETE
        EX: Delete url + filename will delete all data in the database where the filename is as given. Ex: DELETE http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/picture2.jpg

    THE DATABASE
    ************

    Hostname = "localhost";
    Databasename = "photoanalyze";
    Username = "ae";
    Password = "dt117g";
    Tablename = "photos";

    The table photos has the following struture:
    ----------------------------------------------------------------------------------------------------------------------------------------------
    |filename | date | time |tag1_name|tag2_name|tag3_name|tag4_name|tag5_name|totalvalue|tag1_value| tag2_value|tag3_value|tag4_value|tag5_value|
    ----------------------------------------------------------------------------------------------------------------------------------------------
    |string   |string|string| string  | string  | string  | string  | string  | int      | int      | int       | int      | int      | int      |
    ----------------------------------------------------------------------------------------------------------------------------------------------

    */
    //
    //No cache, always reload
    header ("Cache-Control: no-cache, must_revalidate");
    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    //
    //Gloabal variables
    $target_url = "http://localhost/webservices_dt117g/projekt/upload/photos/";
    $target_dir = "photos/";
    //
    //Variables for DB connection.
    $hostName = "localhost";
    $dataBaseName = "photoanalyze";
    $userName = "ae";
    $password = "dt117g";
    $tableName = "photos";
    //
    //Check method of the requst
    $method = $_SERVER['REQUEST_METHOD'];
    //
    //Check parameters
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    if (!$request[0] == "photos"){
        http_response_code(404);
        exit();
    }
    //
    // GET
    if ($method == "GET"){
        $returnObj = new \stdClass();
        //
        // If the request has no filename e.g: http://localhost...filemanager_api.php/photos
        if (!isset($request[1])){
            $sql = "SELECT filename FROM " . $tableName;
            $result = fetchResult($sql);
            $fileNameArr = [];
            $nrOfRows = 0;
            while($row = mysqli_fetch_assoc($result)){
                $fileNameArr[$nrOfRows] = $row['filename'];
                $nrOfRows++;
            }
            if($nrOfRows ==0 ) {
                http_response_code(204);
                exit();
            }else{
                $returnObj->url = $target_url;
                $returnObj->files = $fileNameArr;  
                echo json_encode($returnObj, JSON_UNESCAPED_SLASHES);
            }
            //
            // If the request has a filename e.g: http://localhost...filemanager_api.php/photos/556767657_myphoto.jpg
        }else{
            $sql = "SELECT * FROM " . $tableName . " WHERE filename = '" . $request[1] . "'";//'" . $request[1] . "'";
            $result = fetchResult($sql);
            $returnPhotoObj = new \stdClass(); 
            $nrOfRows = 0;
            //
            //Setup and return the result in JSON.
            while($row = mysqli_fetch_assoc($result)){
                $returnPhotoObj->filename = ($row['filename']);
                $returnPhotoObj->date = ($row['date']);
                $returnPhotoObj->time = ($row['time']);
                $returnPhotoObj->totalvalue = ($row['totalvalue']);
                $returnTagsObj = new \stdClass();
                for ($i=1; $i<6; $i++){
                    $tag_nr = "tag" . (string)$i;
                    $tag_name =  $tag_nr . "_name";
                    $tag_value = $tag_nr . "_value";
                    $returnTagsObj->$tag_nr = array("name"=>$row[$tag_name], "value"=>$row[$tag_value]);
                }
                $returnPhotoObj->tags = $returnTagsObj;
                $nrOfRows++;
            }
            if($nrOfRows ==0 ) {
                http_response_code(204);
                exit();
            }else{
                $returnObj->url = $target_url;
                $returnObj->photo = $returnPhotoObj; 
                echo json_encode($returnObj, JSON_UNESCAPED_SLASHES);
            }
        }
    }
    //
    // POST
    if ($method == "POST"){
        //
        //Get th input data
        $requestData = file_get_contents(('php://input'),true);
        $input = json_decode($requestData, true);
        //
        //Check that filename exists
        if ($input['photo']['filename'] == "") {
            http_response_code(400);
            exit(); 
        }
        //
        // Check that 'filename' not already exist in DB
        $sql = "SELECT filename FROM " . $tableName . " WHERE filename = '" . $input['photo']['filename'] . "';";
        $result = fetchResult($sql);
        $row = mysqli_fetch_assoc($result);
        //
        //If 'filename' not already exist in DB then the new resource will be added to DB.
        if(is_null($row)){
            $sql = "INSERT INTO " . $tableName . " (filename, date, time, tag1_name, tag1_value, tag2_name, tag2_value, tag3_name, tag3_value, tag4_name, tag4_value, tag5_name, tag5_value, totalvalue) VALUES ('" . $input['photo']['filename'] . "', '" . $input['photo']['date'] . "', '" . $input['photo']['time'] . "', '" . $input['photo']['tags']['tag1']['name'] . "', '" . $input['photo']['tags']['tag1']['value'] . "', '" . $input['photo']['tags']['tag2']['name'] . "', '" . $input['photo']['tags']['tag2']['value'] . "', '" . $input['photo']['tags']['tag3']['name'] . "', '" . $input['photo']['tags']['tag3']['value'] . "', '" . $input['photo']['tags']['tag4']['name'] . "', '" .$input['photo']['tags']['tag4']['value'] . "', '" . $input['photo']['tags']['tag5']['name'] . "', '" . $input['photo']['tags']['tag5']['value'] . "', '" . $input['photo']['totalvalue'] . "');";
            $result = fetchResult($sql);
        }else{
            http_response_code(409);
            exit();         
        }
    }
    //
    // DELETE
    if ($method == "DELETE"){
        //
        //Check that the filename in $request[1] exists in the database. If it exists it is deleted.
        if (isset($request[1])) {
            $sql = "SELECT ID FROM " . $tableName . " WHERE filename = '" . $request[1] . "';";
            $result = fetchResult($sql);
            $row = mysqli_fetch_assoc($result);
            if(!is_null($row)){
                $idToDelete = $row['ID'];
                $sql = "DELETE FROM " . $tableName . " WHERE ID = " . $idToDelete . ";";
                $result = fetchResult($sql);
                //
                //Delete file from uploads/photos
                $target_dir = "upload/photos/";
                $target_file = $target_dir . $request[1];
                if (file_exists($target_file)){
                    unlink($target_file);
                } 
            }else{
                http_response_code(404);
                exit();
            }
        }else{
            http_response_code(404);
            exit();
        }
    }
    //
    // This function sets up a connection to DB, puts a sql query ($sql_arg) and returns the result
    function fetchResult($sql_arg){
        //
        //Use global variables
        global $hostName, $userName, $password, $dataBaseName;
        //
        //Establish connection to database 
        $conn = mysqli_connect($hostName, $userName, $password, $dataBaseName) or die("Error connecting to database.");
        $db_connected = mysqli_select_db($conn, $dataBaseName);
        //
        // Put SQL-qurey to database and store result in $result
        $queryResult = mysqli_query($conn,$sql_arg) or die(mysqli_error($conn));
        $myRefresh = mysqli_refresh($conn, MYSQLI_REFRESH_TABLES);
        //
        // Close connection to database
        mysqli_close($conn);
        //
        //Return result
        return $queryResult;
    }    
?>
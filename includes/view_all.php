<?php
  /*
  Projektarbete
  Webbprogrammering (DT058G) / Webbptjänster (DT058G)
  HT 2017
  Andreas Edin
  andreas.edin@yahoo.com
  */
  //
  //Here all the photos stored in DB are wiewed.

  //
  //Prevent page caching.
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-cache");
  header("Pragma: no-cache");
  //
  //Look up all the filenames of the photos from DB.
  //Create an array '$fileNameArray' with all the filenames
  $filesInDB = getFilenamesInDB();
  $filesInDB_Array = json_decode($filesInDB, true);
  $fileNameArray = $filesInDB_Array['files'];
  //
  //Loop through array with filenames ('$fileNameArray') and
  //create result for each file.
  echo "<h2>Uploaded photos</h2>";
  echo "<div id='view_all' class='flex-container'>";
  $nr_of_photos = sizeof($fileNameArray);
  for($n=0; $n<sizeof($fileNameArray); $n++){
    echo "<div class='photo_div'>";
    $filename = $fileNameArray[$n];
    $photo_url = "upload/photos/" . $filename;
    //
    //Adjust the size of the uploaded image
    $data = getimagesize($photo_url);
    $width = $data[0];
    $height = $data[1];
    $new_height = 180;
    $new_width = round($width/$height*$new_height);
    $alt_text = "Photo nr " . $n+1 . " of total " . $nr_of_photos . " photos that has been analyzed and stored in database.";
    echo "<img src='" . $photo_url . "' alt='" . $alt_text . "' id='photo_" . $filename . "' height='" . $new_height . "' width='" . $new_width . "' class='analyzedphoto'>";
    //
    //Get all the analyze data for each photo.
    $analyzeJson = getAnalyzeFromDB($filename);
    //
    //Putting out the analyze data at the client for each photo.
    createResult ($analyzeJson);
    echo "<br>";
    //
    //Add a delete button below every photo
    echo "<button type='button' class='delete_button' id='" . $filename . "'>Delete photo</button>";
    echo "</div>";//class='photo_div'
  }
  echo "<br><br><br>";
  echo "</div>"; //id='view_all'
  //
  //Add Javascript, containing listener to the delete button.
?>
<script type="text/javascript" src="js/delete.js"></script>
<?php
  //
  //Send a request to the API 'filemanager_api.php' to
  //get all filenames of the photos stored in database.
  function getFilenamesInDB(){
    $curl = curl_init();
    $url = "http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/";
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_URL, $url);
      $result = curl_exec($curl);
      curl_close($curl);
      return $result;
  }
  //
  //Put a GET request to DB with '$filename_arg' as parameter
  function getAnalyzeFromDB($filename_arg){
    $curl = curl_init();
    $url = "http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/" . $filename_arg;
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_URL, $url);
      $result = curl_exec($curl);
      curl_close($curl);
      return $result;
  }
  //
  //Takes all analyze data from a photo
  //and putting it out the at the client
  function createResult($JSON_arg){
    $jsonArray = json_decode($JSON_arg, true);
    $totalvalue = $jsonArray['photo']['totalvalue'];
    echo "<br>";
    echo "<b>Total fun value: " . $totalvalue . "%</b>";
    for ($i=1; $i<6; $i++){
      $tag_name = $jsonArray['photo']['tags']['tag' . $i]['name'];
      $tag_value = $jsonArray['photo']['tags']['tag' . $i]['value'];
      if ($tag_name <> "" && $tag_value <> 0){
        echo "<br>";
        echo $tag_name . ": " . $tag_value . "%";
      }
    }
  }
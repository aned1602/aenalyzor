<?php
/*
Projektarbete
Webbprogrammering (DT058G) / Webbptjänster (DT058G)
HT 2017
Andreas Edin
andreas.edin@yahoo.com
*/
//
//Here is the result shown from the latest uploaded and analyzed photo.
//
//Check if the parameter 'filename' is not set.
if (!isset($_REQUEST['filename'])){
	echo("File not found!");
	exit();
}
?>
	<h1>....This fun is it!</h1>	
<?php
$filename = $_REQUEST['filename'];
$photo_url = "upload/photos/" . $filename;
//
//Adjust the size of the uploaded image
$data = getimagesize($photo_url);
$width = $data[0];
$height = $data[1];
$new_width = 150;
$new_height = $height/$width*$new_width;
//
//Wiew the photo on the client
echo "<img src=" . $photo_url . " alt='This is the most recent uploaded photo with analyze results from imagga.' id='bild1' height='" . $new_height . "' width='" . $new_width . "' class='analyzedphoto'>";
//
//Get the analyze data belonging to the photo '$filename'.
//and wiewing it at the client
$analyzeJson = getAnalyzeFromDB($filename);
createResult ($analyzeJson);
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
//Putting out the tag's 'name' and 'value'
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
?>
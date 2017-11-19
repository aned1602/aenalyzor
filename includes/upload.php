<?php
	/*
	Projektarbete
	Webbprogrammering (DT058G) / Webbptjänster (DT058G)
	HT 2017
	Andreas Edin
	andreas.edin@yahoo.com
	*/
	//This file can be called with a parameter set or without. The parameter is a filename of the file that will be processed
	// Ex, with parameter set: upload.php$filename=picture2.php
	//
	//
	//Prevent page caching.
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	//
	//Global variables
	$target_url = "http://localhost/webservices_dt117g/projekt/upload/photos/";
	$filename="";
	//
	//Check if the parameter 'filename' is not set.
	if (!isset($_REQUEST['filename'])){
		echo '<h2>Upload photos</h2>';
		echo '<div id="fileUpload">';
		echo "Select an image file to upload.<br>";
		echo '<input type="file" name="fileToUpload" id="fileToUpload"><br><br>';
		echo '<input type="submit" value="Upload Image" name="submit" id="submitUpload">';
		echo '</div>';
	}else{
		//
		//'filename' is set
		$filename = $_REQUEST['filename'];
		//
		//check that filename not is empty
		if ($filename<>""){
			//
			//process the file
			$analyzeData = analyzePhoto();
			addToDB($analyzeData);
			//
			//jump to page 'uploaded' that will show the uploaded photo and its analyze result.
			header("Location: index.php?uploaded&filename=" . $filename);	
		}else{
			//
			//Jump back to this page, with no filename set as parameter.
			header("Location: index.php?upload");
		}
		

	}
	//
	//A photo with a public url is analyzed with imagga's API.
	//The tags from imagga is matched to a given array wit tags, here
	//named '$matchTags'. The five top matching tags and their matching
	//value (in %) is used to create a JSON response. A 'totalvalue' of
	//the match is calculeted as well, this is most for fun.
	function analyzePhoto(){
		Global $target_url, $filename, $exampleResult;
		//
		//Variables to set up connection to the API at www.imagga.com
		$authorization = "Basic YWNjXzBmYjM1ZmNjMWUzYTZiZToxY2JiMDhhYjg2Mjc4MTAxNzM2MzgzZjE5ODE0MTMzMw==";
		$api_http = "https://api.imagga.com/";
		//
		//The photo that will be analyzed must have been placed at this url and with this name.
		$photo_url = "http://studenter.miun.se/~aned1602/writeable/uploads/ae_photo.jpg";
		//
		//These are the tags that will be matched to the tags from imagga's API
		$matchTags = ["smile", "smiling", "happy", "happiness", "fun", "party", "celebration", "music", "dancing", "celebration"];
		//
		//Setup all according to instructions from immaga.com's API documentation
		$curl = curl_init();
	    curl_setopt_array($curl, array(
	      CURLOPT_URL => "http://api.imagga.com/v1/tagging?url=" . $photo_url . "&version=2",
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => "json",
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 30,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	      CURLOPT_CUSTOMREQUEST => "GET",
	      CURLOPT_HTTPHEADER => array(
	        "accept: application/json",
	        "authorization: " . $authorization
	        ),
	    ));
	    //
	    //Send request to API
	    $response = curl_exec($curl);
	    $jsonArray = json_decode($response, true);
	    $err = curl_error($curl);
	    curl_close($curl);
	    if ($err) {
	      return;
	    } else {
		    //
		    //Variables for creating the return result in JSON.
		    $returnObj = new \stdClass();
		    $returnPhotoObj = new \stdClass();
		    $returnTagsObj = new \stdClass();
		    //
		    //Get time and date from filename
		    $timeStamp = substr($filename, 0, 10);
			$date = date("Y-m-d",$timeStamp);
			$time = date("H:i:s",$timeStamp);
			//
			//Continnuing creating the return result in JSON.
		    $returnObj->url = $target_url;
		    $returnPhotoObj->filename = $filename;
		    $returnPhotoObj->date = $date;
		    $returnPhotoObj->time = $time;
		    //
		    //If the result from Imagga's API is unsuccessful.
		    //Could happen if the picture is to large (>300px in some dimension),
		    //if the api is down or the connection is slow/down
		    if (!isset($jsonArray['results'][0]['tags'])){
		    	echo '<h2>Upload photos</h2>';
				echo '<div id="fileUpload">';
				echo "Select image to upload:";
				echo '<input type="file" name="fileToUpload" id="fileToUpload">';
				echo '<input type="submit" value="Upload Image" name="submit" id="submitUpload">';
				echo '</div>';
				echo "<br>";
				echo '<div id="error">';
				echo '<h3>Trouble with photoanalyze at api.imagga.com</h3>';
				echo "<p>Try again!</p>";
				echo "<p><b>A tip from imaga about images Size</b><br>If your network connection or the network connection of the server where your images are being stored is slow, it is preferable that you downscale the images before sending them to Imagga API. The API doesn't need more that 300px on the shortest side to provide you with the same great results.</p>";
				echo '</div>';
		    }else{
			    //
			    //Variables for setting up the tag's names and values.
			    $allTagsArray = $jsonArray['results'][0]['tags'];
			    $i = 1;
			    $maxNrOfTags = 5;
			    $totalFunfactor = 0;
			    $nrOfTags = 0;
			    $nrOfMatches = 0;
			    //
			    //Building the json result.
			    foreach ($allTagsArray as $tagarray) {
			    	if ($i<=$maxNrOfTags){
				        $tag = $tagarray['tag'];
				        $confidence = $tagarray['confidence'];
				        $tag_nr = "tag" . (string)$i;
				        //
				        //Matching tags from imagga to the array '$matchTags'.
			            if (in_array($tag, $matchTags)){
				        	$returnTagsObj->$tag_nr = array("name"=>$tag, "value"=>round($confidence));
				        	$i++;
				          	$totalFunfactor += $confidence;
				          	$nrOfTags ++;
				          	$nrOfMatches = $nrOfTags;
				        }
			      	}
			    }
			    //
			    //Fill up eventually unset (empty) tags with values
			   	while($nrOfTags<5){
			    	$tag_nr = "tag" . (string)$i;
			    	$returnTagsObj->$tag_nr = array("name"=>"", "value"=>0);
			    	$i++;
			    	$nrOfTags++;
			    }
			    //
			    //Calculation of the 'Total Fun Factor' (most for fun)
			    $totalFunfactor = $totalFunfactor/5 + $nrOfMatches*12.5;
			    $returnPhotoObj->totalvalue = round($totalFunfactor);
			    $returnPhotoObj->tags = $returnTagsObj;
			    $returnObj->photo = $returnPhotoObj;
			    //
			    //return result in json 
			    return json_encode($returnObj, JSON_UNESCAPED_SLASHES);
		    }
	    }
	}
	//
	//Load up a result from an analyze of a photo to the DB by
	//putting a POST request to the API ('filemanager_api.php').
	function addToDB($inDataJson){
		$curl = curl_init();
		$url = "http://localhost/webservices_dt117g/projekt/filemanager_api.php/photos/";
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $inDataJson);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    $result = curl_exec($curl);
	    curl_close($curl);
	}
?>
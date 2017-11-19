<?php
	/*
	Projektarbete
	Webbprogrammering (DT058G) / Webbptjänster (DT058G)
	HT 2017
	Andreas Edin
	andreas.edin@yahoo.com
	*/
	session_start();
	$page_title = "AEnalyzor Startsida";

	include("includes/header.php");

	if(isset($_REQUEST["about"])) {
		include("includes/about.php");
	}else if(isset($_REQUEST["view_all"])) {
		include("includes/view_all.php");
	}else if(isset($_REQUEST["upload"])) {
		include("includes/upload.php");
	}else if(isset($_REQUEST["home"])) {
		include("includes/home.php");
	}else if(isset($_REQUEST["uploaded"])) {
		include ("includes/uploaded.php");
	}else {
		?>
			<h2>How fun is your photos?</h2>
			<p>Here you can find out <i>exactly</i> how fun your photos are, and
			thereby mabye how fun you had at the moment a certain photo was taken. Or it's possible to see how fun a person on a photo really is.</p>
			<p>It's simle. Load up a photo (or other image file) by clicking on "Upload photo" in the left side menu.
			After that is done you will see the result of the photoanalyze. By clicking on "View all photos" in the left side menu you can watch all your uploaded photos. Mabye it's time to compare your friends to find out who's fun enough to be invited to the next party?</p>
		<?php
	}
	include("includes/footer.php");
	exit();
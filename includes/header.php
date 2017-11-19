<?php
/*
Projektarbete
Webbprogrammering (DT058G) / Webbptjänster (DT058G)
HT 2017
Andreas Edin
andreas.edin@yahoo.com
*/
?>
<!DOCTYPE html>
<?php include("includes/config.php"); ?>
<html lang="sv">
<head>
    <title><?= $site_title . $divider . $page_title; ?></title>
    <meta charset="utf-8">
	
	<!-- add css stylesheet -->
    <link rel="stylesheet" href="css/stilmall.css" type="text/css">
	
	<!-- add javascript -->
	<script type="text/javascript" src="js/aenalyzor.js"></script>
	
	<!-- add jquery -->
		<script type="text/javascript" src="lib/jquery-1.10.1.min.js"></script>
	
	
</head>
<body>

	<header id="mainheader">
	    <h1>AEnalyzor - how fun is it?</h1>
	</header>
	<?php include("includes/mainmenu.php"); ?>
    <section id="leftcontent">
<?php        	
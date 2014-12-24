<?php

include 'functions.php';
tryLogin();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html data-ng-app="AforoKontrola">
	<head>
		<meta charset="UTF-8" http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Aforo Kontrola</title>
		<meta http-equiv="refresh" content="10" >

		<link href="css/aforo.css" rel="stylesheet" type="text/css" />
	</head>

	<body>
		<h1>Aforo Kontrola</h1>
		<div class="data">
<?php
	$datuak = lortuSarrerakEtaIrteerak();
	$sarrerak = $datuak[0];
	$irteerak = $datuak[1];
	echo("<p>Sarrerak: $sarrerak</p>");
	echo("<p>Irteerak: $irteerak</p>");
	echo("<h3>BARRUAN: " . ($sarrerak - $irteerak) . " pertsona</h3>");
	echo ("<p><a href=\"stats.php\">Grafika ikusi</a></p>");
?>
		</div>
	</body>
</html>

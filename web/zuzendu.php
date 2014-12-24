<?php

include 'functions.php';
tryLogin();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html data-ng-app="AforoKontrola">
	<head>
		<meta charset="UTF-8" http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Aforo Kontrola</title>

		<link href="css/sarrerak.css" rel="stylesheet" type="text/css" />
	</head>

	<body>
		<h1>Zuzendu</h1>
<?php

$kopurua = $_REQUEST["kop"];
$taula = $_REQUEST["taula"];
$gehitu = $_REQUEST["gehitu"];

if ($db = openDB()) {
	$query = "INSERT INTO " . $taula . " (user, time, gehitu, num) values " .
			"('" . $_SESSION['user'] . "'," . date("U") . ",'" . $gehitu . "'," . $kopurua . ")";
	if ($db -> query($query)) {
		echo "<p class=\"center\">" . $kopurua . (($taula == "sarrerak") ? " sarrera " : " irteera ") .
		(($gehitu == "TRUE") ? "gehitu" : "kendu") . " dira.</p>";
	}
	else
		showError(ERROR_QUERYING);
	
	$db -> close();
}
else
	showError(ERROR_CONNECTING_DB);

?>

		<p class="center"><a href="aforo.php">Itzuli</a></p>
	</body>
</html>

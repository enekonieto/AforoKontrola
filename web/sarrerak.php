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
		<h1>Sarrerak</h1>
		<p class="center"><a href="aforo.php">Itzuli</a></p>
		<div class="table">
			<div class="row header">
				<div class="cell header">ID</div>
				<div class="cell header">Erabiltzailea</div>
				<div class="cell header">Ordua</div>
				<div class="cell header">Konpurua</div>
				<div class="cell header">Guztira</div>
			</div>
<?php

/**
 * Taulan lerro bat gehitu.
 * @param lerroa Array-a lerroaren datuekin.
 * @param guztira Guztira daudeank lerro berria kontuan hartu aurretik.
 * @return Guztira daudenak lerro berria kontuan hartuta.
 */
function lerroaErakutsi($lerroa, $guztira) {
	$num = intval(($lerroa['gehitu'] == 'TRUE') ? $lerroa['num'] : - $lerroa['num']);
	$guztira += $num;
	echo "<div class=\"row\">" .
			"<div class=\"cell\">" . $lerroa['id'] . "</div>" .
			"<div class=\"cell\">" . $lerroa['user'] . "</div>" .
			"<div class=\"cell\">" . date("G:i:s", $lerroa['time']) . "</div>" .
			"<div class=\"cell\">" . $num . "</div>" .
			"<div class=\"cell\">" . $guztira . "</div>" .
	"</div>\n";
	return $guztira;
}

if ($db = openDB()) {
	$guztira = 0;
	$result = $db -> query("SELECT id, user, time, gehitu, num FROM sarrerak WHERE deleted = 'FALSE'");
	if ($result) {
		while ($row = $result -> fetchArray())
			$guztira = lerroaErakutsi($row, $guztira);
	}
	else
		showError(ERROR_QUERYING);
	
	$db -> close();
}

?>
		</div>
		<p class="center"><a href="aforo.php">Itzuli</a></p>
	</body>
</html>

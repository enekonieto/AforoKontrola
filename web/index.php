<?php

include 'functions.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html data-ng-app="AforoKontrola">
	<head>
		<meta charset="UTF-8" http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Aforo Kontrola</title>

		<link href="css/index.css" rel="stylesheet" type="text/css" />
	</head>

	<body>
		<h1>Aforo Kontrola</h1>
		<form action="aforo.php" method="post">
			<div class="login">
<?php
	if (isset($_REQUEST['error'])) {
		session_start();
		unset($_SESSION['user']);
		unset($_SESSION['pass']);
		echo "<div class=\"error\">Erabiltzaile edo pasahitz okerra</div>";
	}
?>
				<div class="table">
					<div class="row">
						<div class="cell">Erabiltzailea:</div>
						<div class="cell"><input type="text" name="user" size="20" /></div>
					</div>
					<div class="row">
						<div class="cell">Pasahitza:</div>
						<div class="cell"><input type="password" name="pass" size="20" /></div>
					</div>
				</div>
				<div class="bidali"><input type="submit" value="Sartu"></div>
			</div>
		</form>
	</body>
</html>

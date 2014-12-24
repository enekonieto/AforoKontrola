<?php

define(ERROR_NO_OP, 0);
define(ERROR_INVALID_OP, 1);
define(ERROR_CONNECTING_DB, 2);
define(ERROR_INVALID_USER_OR_PASS, 3);
define(ERROR_INVALID_NUMBER, 4);
define(ERROR_INSERTING_ROW, 5);
define(ERROR_INVALID_ID, 6);
define(ERROR_QUERYING, 7);

/**
 * Sarrera eta irteera kopuru totala lortu.
 * @return Sarrera eta irteera kopurua.
 */
function lortuSarrerakEtaIrteerak() {
	if ($db = openDB()) {
		$result = $db -> query("SELECT (SELECT SUM(num) FROM sarrerak WHERE deleted = 'FALSE'), (SELECT SUM(num) FROM irteerak WHERE deleted = 'FALSE')");
		if ($result)
			return $result -> fetchArray();
		else
			showError(ERROR_QUERYING);
	}

	$db -> close();
	return false;
}

/**
 * Sarrera/irteera progresioa array batean itzuli
 * @param Taula: sarrerak edo irteerak. 
 * @return Hasiera ordua eta sarrera/irteera progresioa.
 */
function lortuProgresioa($taula) {
	$progresioa = array();
	$guztira = 0;
	
	if ($db = openDB()) {
		$result = $db -> query("SELECT time, num, gehitu FROM " . $taula . " WHERE deleted = 'FALSE'");
		while ($row = $result -> fetchArray()) {
			if (! isset($hasiera))
				$hasiera = $row['time'];
			if ($row['gehitu'] == 'TRUE')
				$guztira += $row['num'];
			else
				$guztira -= $row['num'];
			$progresioa[] = array($row['time'] - $hasiera, $guztira);
		}
	}
	
	return json_encode(array($hasiera, $progresioa));
}

/**
 * Sarrera/irteera progresioa array batean itzuli
 * @param Taula: sarrerak edo irteerak. 
 * @return Hasiera ordua eta sarrera/irteera progresioa.
 */
function lortuAforoProgresioa() {
	$progresioa = array();
	$guztira = 0;
	
	if ($db = openDB()) {
		$sarrerak = $db -> query("SELECT time, num, gehitu FROM sarrerak WHERE deleted = 'FALSE'");
		$irteerak = $db -> query("SELECT time, num, gehitu FROM irteerak WHERE deleted = 'FALSE'");
		
		$sarreraGehio = ($azkenSarrera = $sarrerak -> fetchArray());
		$irteeraGehio = ($azkenIrteera = $irteerak -> fetchArray());
		$hasiera = ($azkenSarrera['time'] < $azkenIrteera['time']) ? $azkenSarrera['time'] : $azkenIrteera['time'];
		while ($sarreraGehio || $irteeraGehio) {
			if (! $sarreraGehio)
				$sarreraTxanda = false;
			else if (! $irteeraGehio)
				$sarreraTxanda = true;
			else
				$sarreraTxanda = ($azkenSarrera['time'] < $azkenIrteera['time']);
			
			if ($sarreraTxanda) {
				if ($azkenSarrera['gehitu'] == 'TRUE')
					$guztira += $azkenSarrera['num'];
				else
					$guztira -= $azkenIrteera['num'];
				$progresioa[] = array($azkenSarrera[time] - $hasiera, $guztira);
				$sarreraGehio = ($azkenSarrera = $sarrerak -> fetchArray());
			}
			else {
				if ($azkenIrteera['gehitu'] == 'TRUE')
					$guztira -= $azkenIrteera['num'];
				else
					$guztira += $azkenIrteera['num'];
				$progresioa[] = array($azkenIrteera[time] - $hasiera, $guztira);
				$irteeraGehio = ($azkenIrteera = $irteerak -> fetchArray());
			}
		}
	}
	
	return json_encode(array($hasiera, $progresioa));
}

/**
 * Autentifikazioa egiaztatu. Errorea egotekotan login orrialdera bidaltzen du.
 */
function tryLogin() {
	session_start();
	if (isset($_SESSION["user"]) && isset($_SESSION["pass"])) {
		$user = $_SESSION["user"];
		$pass = $_SESSION["pass"];
	}
	else {
		$user = $_REQUEST["user"];
		$_SESSION["user"] = $user;
		$pass = $_REQUEST["pass"];
		$_SESSION["pass"] = $pass;
	}
	
	if (! authentificate($user, $pass))
		header('Location: index.php?error=1');
}

/**
 * Erabiltzailea eta pasahitza egiaztatu.
 * @param user Erabiltzailea
 * @param pass Pasahitza
 * @return Ondo egiaztatu den (true) ala ez (false)
 */
function authentificate($user, $pass) {
	if ($db = openDB()) {
		$result = $db -> query("SELECT * from users where user = '" . $user . "' and pass='" . $pass . "' and admin = 'true'");
		if ($result) {
			if ($result -> fetchArray())
				return true;
			else
				showError(ERROR_INVALID_USER_OR_PASS);
		}
		else
			showError(ERROR_QUERYING);
	}

	$db -> close();
	return false;
}

/**
 * Datu basea ireki.
 */
function openDB() {
	try {
		$db = new SQLite3("../aforo_kontrola.db", SQLITE3_OPEN_READWRITE);
		return $db;
	} catch (Exception $e) {
		showError(ERROR_CONNECTING_DB);
		return false;
	}
}

/**
 * Errorea JSON moduan kodifikatu eta bidali.
 * @param error_cod Errore zenbakia
 * @param extra_msg Errore mezuari gehitu nahi zaion testua. Errore mezuak $error array-an daude.
 */
function showError($error_cod = NULL, $extra_msg = false) {
	echo "<p>ERROR </p>" . $error_cod;
}

?>

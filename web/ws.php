<?php

/**
 * Mugikorrak zerbitzariakin komunikatzeko erabiltzen duen web service-a.
 */

define(ERROR_NO_OP, 0);
define(ERROR_INVALID_OP, 1);
define(ERROR_CONNECTING_DB, 2);
define(ERROR_INVALID_USER_OR_PASS, 3);
define(ERROR_INVALID_NUMBER, 4);

$error = array(ERROR_NO_OP => "Ez da operaziorik aukeratu", ERROR_INVALID_OP => "Operazio izen okerra", ERROR_CONNECTING_DB => "Errorea datu basera konektatzen", ERROR_INVALID_USER_OR_PASS => "Erabiltzaile edo pasahitz okerra", ERROR_INVALID_NUMBER => "Errorea kopuruarekin", );

if (isset($_REQUEST['op']))
	process($_REQUEST['op']);
else
	response(false, ERROR_NO_OP);

/**
 * Eskatutako operazioa aztertu eta funtzioa egokia deitu.
 * @param op Operazioa
 */
function process($op) {
	switch ($op) {
		case "login" :
			loginFunc($_REQUEST['user'], $_REQUEST['pass']);
			break;
		case "sarrera" :
			sarreraFunc($_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['num']);
			break;
		case "irteera" :
			irteeraFunc($_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['num']);
			break;
		default :
			response(false, ERROR_INVALID_OP, " \"" . $op . "\"");
			break;
	}
}

/**
 * Erabiltzailea eta pasahitza egiaztatu.
 * @param user Erabiltzailea
 * @param pass Pasahitza
 */
function loginFunc($user, $pass) {
	if (authentificate($user, $pass))
		response(TRUE);
}

/**
 * Sarrerak gehitu.
 * @param user Erabiltzailea
 * @param pass Pasahitza
 * @param num Sarrera kopurua
 */
function sarreraFunc($user, $pass, $num) {
	if (authentificate($user, $pass)) {
		if (intval($num) != 0) {
			if ($db = openDB()) {
				$db -> exec("INSERT INTO sarrerak (user, time, num) values( '" . $user . "', " . time() . ", " . $num . ")");
				response(TRUE);
			}
		} else
			response(FALSE, ERROR_INVALID_NUMBER);
	}
}

/**
 * Irteerak gehitu.
 * @param user Erabiltzailea
 * @param pass Pasahitza
 * @param num Irteera kopurua
 */
function irteeraFunc($user, $pass, $num) {
	if (authentificate($user, $pass)) {
		if (intval($num) != 0) {
			if ($db = openDB()) {
				$db -> exec("INSERT INTO irteerak (user, time, num) values( '" . $user . "', " . time() . ", " . $num . ")");
				response(TRUE);
			}
		} else
			response(FALSE, ERROR_INVALID_NUMBER);
	}
}

/*
 * FUNTZIO LAGUNTZAILEAK
 */

/**
 * Erabiltzailea eta pasahitza egiaztatu.
 * @param user Erabiltzailea
 * @param pass Pasahitza
 * @return Ondo egiaztatu den (true) ala ez (false)
 */
function authentificate($user, $pass) {
	if ($db = openDB()) {
		$result = $db -> query("SELECT * from users where user = '" . $user . "' and pass='" . $pass . "'");
		if ($result -> fetchArray())
			return true;
		else
			response(FALSE, ERROR_INVALID_USER_OR_PASS);
	}

	$db -> close();
	return false;
}

/**
 * Datu basea ireki.
 */
function openDB() {
	try {
		$db = new SQLite3("aforo_kontrola.db", SQLITE3_OPEN_READWRITE);
		return $db;
	} catch (Exception $e) {
		response(FALSE, ERROR_CONNECTING_DB);
		return false;
	}
}

/**
 * Erantzuna JSON moduan kodifikatu eta bidali.
 * @param success Operazioa ondo (true) atera den ala ez (false)
 * @param error_cod Errore zenbakia
 * @param extra_msg Errore mezuari gehitu nahi zaion testua. Errore mezuak $error array-an daude.
 */
function response($success, $error_cod = NULL, $extra_msg = false) {
	global $error;
	$response["success"] = $success;
	if (!$success) {
		$response["error_code"] = $error_cod;
		$response["error_msg"] = $error[$error_cod];
		if ($extra_msg)
			$response["error_msg"] .= $extra_msg;
	}
	echo json_encode($response);
}
?>

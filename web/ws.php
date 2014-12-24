<?php

/**
 * Mugikorrak zerbitzariakin komunikatzeko erabiltzen duen web service-a.
 */

define(ERROR_NO_OP, 0);
define(ERROR_INVALID_OP, 1);
define(ERROR_CONNECTING_DB, 2);
define(ERROR_INVALID_USER_OR_PASS, 3);
define(ERROR_INVALID_NUMBER, 4);
define(ERROR_INSERTING_ROW, 5);
define(ERROR_INVALID_ID, 6);
define(ERROR_QUERYING, 7);

$error = array(
	ERROR_NO_OP => "Ez da operaziorik aukeratu",
	ERROR_INVALID_OP => "Operazio izen okerra",
	ERROR_CONNECTING_DB => "Errorea datu basera konektatzen",
	ERROR_INVALID_USER_OR_PASS => "Erabiltzaile edo pasahitz okerra",
	ERROR_INVALID_NUMBER => "Errorea kopuruarekin",
	ERROR_INSERTING_ROW => "Errorea hilara sortzen",
	ERROR_INVALID_ID => "Errorea id-arekin",
	ERROR_QUERYING => "Errorea kontsultan"
);

if (isset($_REQUEST['op']))
	process($_REQUEST['op']);
else
	responseError(ERROR_NO_OP);

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
			$gehi = (isset($_REQUEST['gehi'])) ? $_REQUEST['gehi'] : 'TRUE';
			gehituFunc("sarrerak", $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['num'], $gehi);
			break;
		case "irteera" :
			$gehi = (isset($_REQUEST['gehi'])) ? $_REQUEST['gehi'] : 'TRUE';
			gehituFunc("irteerak", $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['num'], $gehi);
			break;
		case "sarrera_urratu" :
			urratuFunc("sarrerak", $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['id']);
			break;
		case "irteera_urratu" :
			urratuFunc("irteerak", $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['id']);
			break;
		default :
			responseError(ERROR_INVALID_OP, " \"" . $op . "\"");
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
		responseSuccess();
}

/**
 * Sarrerak edo irteerak gehitu.
 * @param table Datu basearen tabla: sarrerak edo iteerak
 * @param user Erabiltzailea
 * @param pass Pasahitza
 * @param num Sarrera/irteera kopurua
 * @param gehi Kopurua gehitu (true) edo kendu (false)
 */
function gehituFunc($table, $user, $pass, $num, $gehi) {
	if (authentificate($user, $pass)) {
		if (intval($num) != 0) {
			if ($db = openDB()) {
				if ($db -> exec("INSERT INTO " . $table . " (user, time, num, gehitu) values( '" . $user . "', " . time() . ", " . $num . ", '" . $gehi . "')")) {
					$id = $db -> lastInsertRowid();
					responseSuccess($id);
				}
				else
					responseError(ERROR_INSERTING_ROW);
				$db -> close();
			}
		} else
			responseError(ERROR_INVALID_NUMBER);
	}
}

/**
 * Sarrera edo irteera lerro bat urratu.
 * @param table Datu basearen tabla: sarrerak edo iteerak
 * @param user Erabiltzailea
 * @param pass Pasahitza
 * @param id Sarrera kopurua
 */
function urratuFunc($table, $user, $pass, $id) {
	if (authentificate($user, $pass)) {
		if (intval($id) > 0) {
			if ($db = openDB()) {
				if ($db -> exec("DELETE FROM " . $table . " WHERE id = " . $id))
					responseSuccess();
				else
					responseError(ERROR_INSERTING_ROW);
				$db -> close();
			}
		} else
			responseError(ERROR_INVALID_ID);
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
		$result = $db -> query("SELECT * from users where user = '" . $user . "' and pass='" . sha1($pass) . "'");
		if ($result) {
			if ($result -> fetchArray())
				return true;
			else
				responseError(ERROR_INVALID_USER_OR_PASS);
		}
		else
			responseError(ERROR_QUERYING);
		
		$db -> close();
	}

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
		responseError(ERROR_CONNECTING_DB);
		return false;
	}
}

/**
 * Erantzuna JSON moduan kodifikatu eta bidali.
 * @param id Sarrera edo irteeraren id-a (sormen operazioetan soilik).
 */
function responseSuccess($id = NULL) {
	global $error;
	$response["success"] = TRUE;
	if ($id)
		$response["id"] = $id;
	echo json_encode($response);
}

/**
 * Errorea JSON moduan kodifikatu eta bidali.
 * @param error_cod Errore zenbakia
 * @param extra_msg Errore mezuari gehitu nahi zaion testua. Errore mezuak $error array-an daude.
 */
function responseError($error_cod = NULL, $extra_msg = false) {
	global $error;
	$response["success"] = FALSE;
	$response["error_code"] = $error_cod;
	$response["error_msg"] = $error[$error_cod];
	if ($extra_msg)
		$response["error_msg"] .= $extra_msg;
	echo json_encode($response);
}

?>

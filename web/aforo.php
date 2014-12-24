<?php

include 'functions.php';
tryLogin();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html data-ng-app="AforoKontrola">
<head>
<meta charset="UTF-8" http-equiv="Content-Type"
	content="text/html; charset=utf-8" />

<title>Aforo Kontrola</title>
<meta http-equiv="refresh" content="100">

<link href="css/aforo.css" rel="stylesheet" type="text/css" />
<script language="javascript">
	function createHiddenInput(izena, balioa) {
		var node = document.createElement("input");
		node.type = "hidden";
		node.name = izena;
		node.value = balioa;
		return node;
	}

	function zuzendu(gehitu, taula) {
		var form = document.getElementById("form");

		form.appendChild(createHiddenInput("taula", taula));
		form.appendChild(createHiddenInput("gehitu", gehitu));
		form.appendChild(createHiddenInput("kop", document.getElementById("kop_" + taula).value));
		
		form.submit();
	}
</script>
</head>

<body>
	<h1>Aforo Kontrola</h1>
	<div class="data">
		<?php
		$datuak = lortuSarrerakEtaIrteerakGuztira();
		$sarrerak = $datuak[0];
		$irteerak = $datuak[1];
		echo("<p>Sarrerak: $sarrerak</p>");
		echo("<p>Irteerak: $irteerak</p>");
		echo("<h3>BARRUAN: " . ($sarrerak - $irteerak) . " pertsona</h3>");
		echo ("");
		?>
		<div class="table">
			<form id="form" action="zuzendu.php" method="post">
				<div class="row">
					<div class="cell">Sarrerak aldatu:</div>
					<div class="cell">
						<input type="text" id="kop_sarrerak" size="4" />
					</div>
					<div class="cell">
						<input type="button" value="Gehitu"
							onclick="javascript:zuzendu('TRUE', 'sarrerak')" />
					</div>
					<div class="cell">
						<input type="button" value="Kendu"
							onclick="javascript:zuzendu('FALSE', 'sarrerak')" />
					</div>
				</div>
				<div class="row">
					<div class="cell">Irteerak aldatu:</div>
					<div class="cell">
						<input type="text" id="kop_irteerak" size="4" />
					</div>
					<div class="cell">
						<input type="button" value="Gehitu"
							onclick="javascript:zuzendu('TRUE', 'irteerak')" />
					</div>
					<div class="cell">
						<input type="button" value="Kendu"
							onclick="javascript:zuzendu('FALSE', 'irteerak')" />
					</div>
				</div>
			</form>
		</div>
		<p>
			<a href="sarrerak.php">Sarrerak</a> | <a href="irteerak.php">Irteerak</a>
			| <a href="stats.php">Grafika</a>
		</p>
	</div>
</body>
</html>

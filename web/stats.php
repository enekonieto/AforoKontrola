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

<link href="css/stats.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript"
	src="js/prototype.js"></script>
<script language="javascript" type="text/javascript"
	src="js/flotr-0.2.0-alpha.js"></script>
</head>

<body>
	<div class="data">
		<div id="container">
		</div>
		<p><a href="aforo.php">Itzuli</a></p>
	</div>
	<script language="javascript">
<?php
	echo "sarrerak = " . lortuProgresioa("sarrerak") . ";\n";
	echo "irteerak = " . lortuProgresioa("irteerak") . ";\n";
	echo "aforoa = " . lortuAforoProgresioa() . ";\n";
?>

		function formatDate(lehena, secs) {
			var date = new Date((lehena + parseInt(secs, 10)) * 1000);
			var minutuak = new String(date.getMinutes());
			if (minutuak.length == 1)
				minutuak = "0" + minutuak;
			var segunduak = new String(date.getSeconds());
			if (segunduak.length == 1)
				segunduak = "0" + segunduak;
			return date.getHours() + ":" + minutuak + ":" + segunduak;
		}

		var lehenSarrera = parseInt(sarrerak[0], 10);
		var lehenIrteera = parseInt(irteerak[0], 10);
		var lehenAforoa = parseInt(aforoa[0], 10);
		Flotr.draw(
			$('container'),
			[
				{
					data: sarrerak[1],
					label: "Sarrerak",
					mouse: { trackFormatter: function(obj){ return formatDate(lehenSarrera, obj.x) + ' ' + obj.y +' sarrera'; } }
				},
				{
					data: irteerak[1],
					label: "Irteerak",
					mouse: { trackFormatter: function(obj){ return formatDate(lehenIrteera, obj.x) + ' ' + obj.y +' irteera'; } }
				},
				{
					data: aforoa[1],
					label: "Barruan",
					mouse: { trackFormatter: function(obj){ return formatDate(lehenAforoa, obj.x) + ' ' + obj.y +' barruan'; } }
				}
			],
			{
				xaxis: { tickFormatter: function(obj){ return formatDate(lehenAforoa, obj) } },
				mouse: {
					track: true,
					sensibility: 25,
					trackDecimals: 0
				}
			}
		);
	</script>
</body>
</html>

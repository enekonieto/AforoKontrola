/**
 * Orrialdeen javascript funtzioak. AngularJS controller-ak hemen daude.
 */

var app = angular.module('AforoControllers', [ 'ngAnimate' ]);

/**
 * Konstanteak
 */
app.urlWebServices = 'https://192.168.42.221/AforoKontrola/web/ws.php';
app.loginErrorCode = 3;
app.resendInterval = 15000; // In milliseconds

// Login datuak berreskuratu
app.user = window.localStorage.getItem('user');
app.pass = window.localStorage.getItem('pass');

/**
 * Login
 */
app.controller('LoginController', function($scope, $http, $location) {
	// Gordetako login datuak erakutsi
	if ((app.user != null) && (app.pass != null)) {
		$scope.user = app.user;
		$scope.pass = app.pass;
		$scope.gogoratuLogin = true;
	}
	
	/**
	 * Erabiltzailea eta pasahitza bidali autentifikatzeko.
	 * @param user Erabiltzailea
	 * @param pass Pasahitza
	 */
	$scope.funtzioaLogin = function(user, pass) {
		$http({
			method : 'GET',
			url : app.urlWebServices + '?op=login&user=' + $scope.user + '&pass=' + $scope.pass
		}).success(function(data) {
			if (data.success) {
				app.user = $scope.user;
				app.pass = $scope.pass;
				if ($scope.gogoratuLogin) {
					window.localStorage.setItem('user', $scope.user);
					window.localStorage.setItem('pass', $scope.pass);
				}
				else {
					window.localStorage.removeItem('user');
					window.localStorage.removeItem('pass');
				}
				$location.path('/aforo');
			}
			else {
				$scope.error = data.error_msg;
			}
		}).error(function() {
			$scope.error = "Errorea zerbitzarira konektatzen.";
		});
	};
	
});

/**
 * Zerrendaren scrollbar-a behean mantentzeko direktiba.
 */
app.directive('scrollbardown', function () {
    return function (scope, element, attrs) {
        scope.$watch("azkenSarrerak", function (value) {
        	scope.divScroll.activeScroll();
        });
    };
});

/**
 * Aforo kontrola
 */
app.controller('AforoController', function($scope, $http, $location, $timeout, $interval, $animate) {
	var funtzioa = 0;
	// 0 = Sarrerak, 1 = Irteerak
	var ikurrak = ['+', '-'];
	var testuak = ['SARRERAK', 'IRTEERAK'];
	var funtzioaAldatuTestuak = ['Irteeretara aldatu', 'Sarreretara aldatu'];

	var sarrerak = 0;
	var irteerak = 0;
	$scope.azkenSarrerak = [];
	var bidaliGabekoak = [];
	$scope.bidaliGabeSarrerak = 0;
	$scope.bidaliGabeIrteerak = 0;
	
	// Zerrendako azkeneko lerroak ikustea.
	$scope.divScroll = new chatscroll.Pane('list');


	/**
	 * Kontrako funtzioa aukeratu (irteeretatik sarreretara pasa eta alderantziz).
	 */
	$scope.funtzioaAldatu = function() {
		funtzioaAldatu(++funtzioa % 2);
	};

	/**
	 * Sarrerak edo irteerak aukeratu.
	 * @param funtzioBerria 0=Sarrerak 1=Irteerak
	 */
	function funtzioaAldatu(funtzioBerria) {
		funtzioa = funtzioBerria;
		$scope.funtzioIkurra = ikurrak[funtzioa];
		$scope.funtzioTestua = testuak[funtzioa];
		$scope.funtzioaAldatuTestua = funtzioaAldatuTestuak[funtzioa];
		$scope.kopurua = (funtzioa == 0) ? sarrerak : irteerak;
	};

	// Orrialdea kargatzerakoan sarrerak aukeratu.
	funtzioaAldatu(0);

	/**
	 * Kopuru handia gehitu, textbox-ean adierazitakoa. $scope.gehitu funtzioa erabiltzen du.
	 */
	$scope.gehituAsko = function() {
		var kopurua = parseInt($scope.gehituTextbox);
		if (! isNaN(kopurua))
			$scope.gehitu(kopurua);
		$scope.gehituTextbox = '';
	};

	/**
	 * Kopuru txikia gehitu, botoiak erabiltzen dute.
 	 * @param kopurua Gehitu behar den kopurua.
	 */
	$scope.gehitu = function(kopurua) {
		var wsOp;
		
		if (funtzioa == 0) {
			sarrerak += kopurua;
			$scope.kopurua = sarrerak;
			wsOp = "sarrera";
		} else {
			irteerak += kopurua;
			$scope.kopurua = irteerak;
			wsOp = "irteera";
		}
		
		// Sarrera/irteera goiko zerrenda gehitu.
		var sartzeOrdua = (new Date()).toLocaleTimeString();
		var sarrera = {
			testua: sartzeOrdua + " " + kopurua + " " + wsOp + " (bidaltzen...)",
			ordua: sartzeOrdua,
			mota: wsOp,
			kopurua: kopurua,
			bidalita: false,
			bidaltzen: true,
			urratua: false
		};
		$scope.azkenSarrerak.push(sarrera);
		
		// Zerbitzarira bidali.
		bidaliSarrera(sarrera);
	};
	
	/**
	 * Sarrera edo irteera zerbitzarira bidali.
	 * @param sarrera Sarrera/irteera objetua.
	 */
	function bidaliSarrera(sarrera) {
		$http({
			method : 'GET',
			url : app.urlWebServices + '?op=' + sarrera.mota + '&user=' + app.user + '&pass=' + app.pass + '&num=' + sarrera.kopurua
		}).success(function(data) {
			sarrera.bidaltzen = false;
			if (data.success) {
				sarrera.bidalita = true;
				sarrera.id = data.id;
				if (sarrera.urratua)
					bidaliSarreraUrratzeko(sarrera);
				else
					sarrera.testua = sarrera.ordua + " " + sarrera.kopurua + " " + sarrera.mota + " (BIDALITA)";
			}
			else if (data.error_code == app.loginErrorCode)
				$location.path('/');
			else {
				if (! sarrera.urratua) {
					sarrera.testua = sarrera.ordua + " " + sarrera.kopurua + " " + sarrera.mota + " (ERROREA: " + data.error_msg + ")";
					bidaliGabekoak.push(sarrera);
					if (funtzioa == 0)
						$scope.bidaliGabeSarrerak += sarrera.kopurua;
					else
						$scope.bidaliGabeIrteerak += sarrera.kopurua;
				}
			}
		}).error(function() {
			sarrera.bidaltzen = false;
			if (! sarrera.urratua) {
				sarrera.testua = sarrera.ordua + " " + sarrera.kopurua + " " + sarrera.mota + " (ERROREA: ezin izan da konektatu)";
				bidaliGabekoak.push(sarrera);
				if (funtzioa == 0)
					$scope.bidaliGabeSarrerak += sarrera.kopurua;
				else
					$scope.bidaliGabeIrteerak += sarrera.kopurua;
			}
		});
	}
	
	/**
	 * Sarrera edo irteera urratzeko zerbitzarira bidali.
	 * @param sarrera Sarrera/irteera objetua.
	 */
	function bidaliSarreraUrratzeko(sarrera) {
		sarrera.bidalita = false;
		sarrera.bidaltzen = true;
		$http({
			method : 'GET',
			url : app.urlWebServices + '?op=' + sarrera.mota + '_urratu' + '&user=' + app.user + '&pass=' + app.pass + '&id=' + sarrera.id
		}).success(function(data) {
			sarrera.bidaltzen = false;
			if (data.success) {
				sarrera.bidalita = true;
				sarrera.testua = "URRATUA";
			}
			else if (data.error_code == app.loginErrorCode)
				$location.path('/');
			else
				bidaliGabekoak.push(sarrera);
		}).error(function() {
			sarrera.bidaltzen = false;
			bidaliGabekoak.push(sarrera);
		});
	}
	
	/**
	 * Errore batengatik bidali gabe geratu diren operazioak birbidali.
	 */
	function bidaliGabekoakBirbidali() {
		var bidaliGabekoakZahar = bidaliGabekoak;
		bidaliGabekoak = [];
console.log("BIRBIDALTZEN");
console.log("bidaliGabekoakZahar:" + bidaliGabekoakZahar);
		
		for (var i = 0; i < bidaliGabekoakZahar.length; i++) {
			var aux = bidaliGabekoakZahar[i];
console.log("sarrera:" + aux.kopurua + " bidaltzen=" + aux.bidaltzen + " bidalita=" + aux.bidalita);
			if (! aux.bidaltzen && ! aux.bidalita) {
				aux.bidaltzen = true;
				var url = app.urlWebServices + '?op=' + aux.mota + ((aux.urratua) ? '_urratu' : '')
					+ '&user=' + app.user + '&pass=' + app.pass
					+ ((aux.urratua) ? '&id=' + aux.id : '&num=' + aux.kopurua)
				$http({
					method : 'GET',
					url : url
				}).success(function(data) {
					aux.bidaltzen = false;
					if (data.success) {
						aux.bidalita = true;
						if (aux.urratua)
							aux.testua = "URRATUA";
						else
							aux.testua = aux.ordua + " " + aux.kopurua + " " + aux.mota + " (BIDALITA)";
					}
					else
						bidaliGabekoak.push(aux);
				}).error(function() {
					aux.bidaltzen = false;
					bidaliGabekoak.push(aux);
				});
			}
		}
	}
	// Minuturo saiatu bidali gabekoak birbidaltzen.
	$interval(bidaliGabekoakBirbidali, app.resendInterval);
	
	
	/*
	 * Azkenetako sarrera/irteera bat luzez sakatu dela detektatzeko funtzioak.
	 */
	var azkenSarreraTimer = null;
	var azkenSarreraIndex;
	
	$scope.azkenSarreraMouseDown = function(index) {
		azkenSarreraIndex = index;
		azkenSarreraTimer = $timeout(azkenSarreraEzabatu, 2000);
	};
	
	function azkenSarreraEzabatu() {
		azkenSarreraTimer = null;
		var aux = $scope.azkenSarrerak[azkenSarreraIndex];
		aux.testua = "Urratzen...";
		aux.urratua = true;
		if (! aux.bidaltzen && aux.bidalita)
			bidaliSarreraUrratzeko(aux);
		if (aux.mota == "sarrera") {
			if (! aux.bidaltzen && ! aux.bidalita) // Sarrerak bidalketa errorea eman du
				$scope.bidaliGabeSarrerak -= aux.kopurua;
			sarrerak -= aux.kopurua;
		}
		else if (aux.mota == "irteera") {
			if (! aux.bidaltzen && ! aux.bidalita) // Sarrerak bidalketa errorea eman du
				$scope.bidaliGabeIrteerak -= aux.kopurua;
			irteerak -= aux.kopurua;
		}
		$scope.kopurua = (funtzioa == 0) ? sarrerak : irteerak;
	}
	
	$scope.azkenSarreraMouseLeave = function() {
		if (azkenSarreraTimer != null) {
			$timeout.cancel(azkenSarreraTimer);
			azkenSarreraTimer = null;
		}
	};
});

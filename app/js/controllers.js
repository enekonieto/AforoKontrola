/**
 * Orrialdeen javascript funtzioak. AngularJS controller-ak hemen daude.
 */

var app = angular.module('AforoControllers', []);

/**
 * Login
 */
app.controller('LoginController', function($scope, $http, $location) {
	
	/**
	 * Erabiltzailea eta pasahitza bidali autentifikatzeko.
	 * @param user Erabiltzailea
	 * @param pass Pasahitza
	 */
	$scope.funtzioaLogin = function(user, pass) {
		$http({
			method : 'GET',
			url : '../web/ws.php?op=login&user=' + $scope.user + '&pass=' + $scope.pass
		}).success(function(data) {
			if (data.success) {
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
 * Aforo kontrola
 */
app.controller('AforoController', function($scope) {
	var funtzioa = 0;
	// 0 = Sarrerak, 1 = Irteerak
	var ikurrak = ['+', '-'];
	var testuak = ['Sarrerak', 'Irteerak'];
	var funtzioaAldatuTestuak = ['Irteeretara aldatu', 'Sarreretara aldatu'];

	var sarrerak = 0;
	var irteerak = 0;
	
	function sarreraBidali(kopurua) {
		$http({
			method : 'GET',
			url : '../web/ws.php?op=sarrera&user=' + $scope.user + '&pass=' + $scope.pass + '&num=' + kopurua
		}).success(function(data) {
			if (data.success) {
				$location.path('/aforo');
			}
			else {
				$scope.error = data.error_msg;
			}
		}).error(function() {
			$scope.error = "Errorea zerbitzarira konektatzen.";
		});
	}

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
	funtzioaAldatu = function(funtzioBerria) {
		funtzioa = funtzioBerria;
		$scope.funtzioIkurra = ikurrak[funtzioa];
		$scope.funtzioTestua = testuak[funtzioa];
		$scope.funtzioaAldatuTestua = funtzioaAldatuTestuak[funtzioa];
		$scope.kopurua = (funtzioa == 0) ? sarrerak : irteerak;
	};

	/**
	 * Kopuru handia gehitu, textbox-ean adierazitakoa.
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
		if (funtzioa == 0) {
			sarrerak += kopurua;
			$scope.kopurua = sarrerak;
		} else {
			irteerak += kopurua;
			$scope.kopurua = irteerak;
		}
	};

	// Orrialdea kargatzerakoan sarrerak aukeratu.
	funtzioaAldatu(0);
});

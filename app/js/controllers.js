/**
 * Orrialdeen javascript funtzioak. AngularJS controller-ak hemen daude.
 */

var app = angular.module('AforoControllers', []);

/**
 * Konstanteak
 */
app.urlWebServices = 'https://localhost/AforoKontrola/web/ws.php';
app.loginErrorCode = 3;

/**
 * Login
 */
app.controller('LoginController', function($scope, $http, $location) {
	
	var user = window.localStorage.getItem('user');
	var pass = window.localStorage.getItem('pass');
	if ((user != null) && (pass != null)) {
		$scope.user = user;
		$scope.pass = pass;
		$scope.gogoratuLogin = true;
	}
	
	/**
	 * Erabiltzailea eta pasahitza bidali autentifikatzeko.
	 * @param user Erabiltzailea
	 * @param pass Pasahitza
	 */
	$scope.funtzioaLogin = function(user, pass) {
// var secret = window.localStorage.getItem('myApp.secret');
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
app.controller('AforoController', function($scope, $http, $location) {
	var funtzioa = 0;
	// 0 = Sarrerak, 1 = Irteerak
	var ikurrak = ['+', '-'];
	var testuak = ['Sarrerak', 'Irteerak'];
	var funtzioaAldatuTestuak = ['Irteeretara aldatu', 'Sarreretara aldatu'];

	var sarrerak = 0;
	var irteerak = 0;

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
		
		$http({
			method : 'GET',
			url : app.urlWebServices + '?op=' + wsOp + '&user=' + app.user + '&pass=' + app.pass + '&num=' + kopurua
		}).success(function(data) {
			if (data.success) {
				$scope.bidaliGabe = "ONDO";
			}
			else if (data.error_code == app.loginErrorCode)
				$location.path('/');
			else {
				$scope.bidaliGabe = data.error_msg;
			}
		}).error(function() {
			$scope.bidaliGabe = "Errorea zerbitzarira konektatzen.";
		});
		
	};

	// Orrialdea kargatzerakoan sarrerak aukeratu.
	funtzioaAldatu(0);
});

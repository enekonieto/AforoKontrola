/**
 * 
 */

var app = angular.module('AforoControllers', []);

app.controller('AforoController', function($scope) {
	var funtzioa = 0; // 0 = Sarrerak, 1 = Irteerak
	var ikurrak = ['+', '-'];
	var testuak = ['Sarrerak', 'Irteerak'];
	var funtzioaAldatuTestuak = ['Irteeretara aldatu', 'Sarreretara aldatu'];
	
	var sarrerak = 0;
	var irteerak = 0;
	
	$scope.funtzioaAldatu = function() {
		funtzioaAldatu(++funtzioa % 2);
	};
	
	funtzioaAldatu = function(funtzioBerria) {
		funtzioa = funtzioBerria;
		$scope.funtzioIkurra = ikurrak[funtzioa];
		$scope.funtzioTestua = testuak[funtzioa];
		$scope.funtzioaAldatuTestua = funtzioaAldatuTestuak[funtzioa];
		$scope.kopurua = (funtzioa == 0) ? sarrerak : irteerak;
	};
	
	$scope.gehituAsko = function() {
		var kopurua = parseInt($scope.gehituTextbox);
		if (! isNaN(kopurua))
			$scope.gehitu(kopurua);
		$scope.gehituTextbox = '';
	};
	
	$scope.gehitu = function(kopurua) {
		if (funtzioa == 0) {
			sarrerak += kopurua;
			$scope.kopurua = sarrerak;
		}
		else {
			irteerak += kopurua;
			$scope.kopurua = irteerak;
		}
	};
	
	funtzioaAldatu(0);
});

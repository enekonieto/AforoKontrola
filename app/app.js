/**
 * Hatortxu Rock-erako aforo kontrol aplikazioa.
 */

var app = angular.module('AforoKontrola', ['ngRoute', 'AforoControllers']);

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider
		.when('/',
			{
				controller: 'AforoController',
				templateUrl: 'partials/aforo.html'
			})
		.otherwise( { redirectTo: '/' } );
}]);

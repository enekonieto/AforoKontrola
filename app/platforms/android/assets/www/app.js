/**
 * Hatortxu Rock-erako aforo kontrol aplikazioa.
 * AngularJS programaren sustraia:
 *  - js/controllers: Orrialdeen javascript funtzioak.
 *  - partials/: Orrialdeen HTMLa.
 */

var app = angular.module('AforoKontrola', ['ngRoute', 'AforoControllers']);

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider
		.when('/',
			{
				controller: 'LoginController',
				templateUrl: 'partials/login.html'
			})
		.when('/aforo',
			{
				controller: 'AforoController',
				templateUrl: 'partials/aforo.html'
			})
		.otherwise( { redirectTo: '/' } );
}]);

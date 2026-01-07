'use strict';

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize', 'ngRoute', 'thatisuday.dropzone']);


app.config(function($mdThemingProvider) {

	$mdThemingProvider.theme('default').dark()
	.primaryPalette('teal')
	.accentPalette('green')
	.warnPalette('amber');
 }); 
 
app.config(function ($routeProvider, $locationProvider) {

	$routeProvider
	 

	.when("/dashboard", {
		templateUrl: "views/admin/page_dashboard.html"
	})

	.when("/clinics", {
		templateUrl: "views/admin/page_clinics.html"
	})

	.when("/advisory", {
		templateUrl: "views/admin/page_advisory.html"
	})

	.when("/users", {
		templateUrl: "views/admin/page_users.html"
	})


	.when("/error404", {
		templateUrl: "views/content_error.html",
		controller: function(global){
			global.page.title = 'Error 404';
   		global.page.pageBackUrl = '';
		}
	})
	.otherwise("/error404");

});






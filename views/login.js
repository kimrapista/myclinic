"use strict";

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages','ngSanitize']);

app.config(function($mdThemingProvider) {
	$mdThemingProvider.theme('default').dark()
	.primaryPalette('teal')
	.accentPalette('green')
	.warnPalette('amber');
 }); 

app.controller('Login', function($scope, $http, $timeout, global) {



    $scope.Init = function(baseUrl,TOKEN) {

        global.baseUrl = baseUrl;

        $scope.successMsg = '';
        $scope.errorMsg = '';
        $scope.isSubmit = false;

        $scope.help = false;

        $scope.form = { TOKEN: TOKEN, URL: baseUrl+'submit-login', USERNAME: '', PASSWORD: '' };

        $scope.homeUrl = baseUrl;
    }


    $scope.Submit_Form = function() {


        $scope.isSubmit = true;
        $scope.successMsg = '';
        $scope.errorMsg = '';

        localStorage.setItem('USERNAME', $scope.form.USERNAME);

        $http.post($scope.form.URL, $scope.form, global.ajaxConfig) .then( function(response) {


            if (response.data.error != '') {
                global.Toast(response.data.error);    
            }
            else{ 
                // location.replace(response.data.redirect +'#!/dashboard');
                location.replace(response.data.redirect);
            }

            $scope.isSubmit = false;

        },
        function(response) {
            global.Toast(response.data);
            $scope.isSubmit = false;
        });
    }

});

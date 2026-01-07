"use strict";

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages','ngSanitize']);


app.controller('Login', function($scope, $http, $window, global) {


    $scope.init = function(baseUrl,TOKEN) {

        $scope.successMsg = '';
        $scope.errorMsg = '';
        $scope.isSubmit = false;

        $scope.help = false;

        $scope.form = { TOKEN: TOKEN, URL: baseUrl+'submit-login', USERNAME: '', PASSWORD: '' };

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

                window.location = response.data.redirect +'#!/dashboard';
                location.reload(); 
            }

            $scope.isSubmit = false;

        },
        function(response) {
            global.Toast(response.data);
            $scope.isSubmit = false;
        });
    }

});
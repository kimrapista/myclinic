"use strict";


app.controller('pageRelogin',function ($scope,$http,$timeout,$filter, $rootScope,$mdDialog, DiaglogData, global){


	$scope.opt = {
		isSubmit : false,
		isSignIn : false,
		submitRelogin : global.baseUrl + 'submit-relogin',
		ACCOUNTBASE : global.ACCOUNTBASE,
		LEVEL : global.LEVEL
	}


	$scope.form = {
		USERNAME : (localStorage.getItem('USERNAME') != undefined ? localStorage.getItem('USERNAME') : ''),
		PASSWORD : '',
		ACTION : DiaglogData.ACTION,
		AUTOLOGIN : (localStorage.getItem('AUTOLOGIN') != undefined ? (localStorage.getItem('AUTOLOGIN') == 'true' ? true : false) : false),
	}


	$scope.Submit_Form = function(){

		$scope.opt.isSubmit = true;

		$http.post($scope.opt.submitRelogin, $scope.form, global.ajaxConfig).then( function(response){

			if( response.data.error != '' ){
				global.Toast(response.data.error);
			}
			else {

				$scope.opt.isSignIn = true;

				if( $scope.form.ACTION == 'LOAD' ){
					$rootScope.$emit('RELOGIN_LOAD');
				}
				else if( $scope.form.ACTION == 'FORM' ){
					$rootScope.$emit('RELOGIN_FORM');
				}
				else if( $scope.form.ACTION == 'FORM1' ){
					$rootScope.$emit('RELOGIN_FORM1');
				}
				
				$scope.Dialog_Close();
				
			}

			$scope.opt.isSubmit = false;

			localStorage.setItem('USERNAME', $scope.form.USERNAME);
			localStorage.setItem('AUTOLOGIN', $scope.form.AUTOLOGIN );

		},
		function(response){
			global.Toast(response.data);
			$scope.opt.isSubmit = false;
		});
	}


	$scope.Dialog_Close = function() {
		$mdDialog.cancel();
	};


	if( $scope.form.AUTOLOGIN && $scope.form.USERNAME != '' ){

		//$scope.opt.isSubmit = true;
		
		// setTimeout(function(){
		//	$scope.Submit_Form();
		// },2000);
	}

});

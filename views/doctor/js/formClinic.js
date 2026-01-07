"use strict";
// doctor

app.controller('FormClinic',function ($scope,$http,$timeout,$filter, $location, $rootScope, $mdDialog, global){
	
	$rootScope.$emit("Title_Global", { TITLE : 'EDIT CLINIC', BACKURL:'#!/clinic' });

	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'clinics/form-data',
		submitUrl : global.baseUrl + 'clinics/submit-form',
		cancelUrl : '#!/clinic',
		ACCOUNTBASE : global.ACCOUNTBASE
	}



	$scope.Load_Form = function(){

		$http.get( $scope.opt.formUrl, global.ajaxConfig ) .then( function(response) {	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.f = [];
				$scope.f = response.data;

				$scope.f.CANCELLED = $scope.f.CANCELLED == 'Y' ? true : false;
				$scope.f.MEDICALHISTORY = $scope.f.MEDICALHISTORY == 'Y' ? true : false;
				$scope.f.REFRACTION = $scope.f.REFRACTION == 'Y' ? true : false;
			}

			$scope.opt.isLoaded = true;
		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isLoaded = true;
		});
	}


	$scope.Submit_Form = function(){

		if( $scope.opt.isSubmit ) return;
		
		$scope.opt.isSubmit = true;

		$http.post( $scope.opt.submitUrl, $scope.f, global.ajaxConfig).then( function(response){

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM');
			}
			else{
				
				if ( response.data.err != '' ){

					global.Toast(response.data.err);
				}
				else {
					global.Toast('SAVED');
					// $location.url('/clinic');
				}
			}

			$scope.opt.isSubmit = false;
		},
		function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
		});
	}



	$scope.Referral_Report = function(ev) { 

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_referral_letter.html',
			locals:{
				REFERRALURL: global.baseUrl + 'medicals/report/0/referral-letter'
			},
			controller: function($scope, $mdDialog, REFERRALURL, global){

				$scope.URL = REFERRALURL;

				$scope.Dialog_Close = function() {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};


	$scope.Clearance_Report = function(ev) {

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_clearance_letter.html',
			locals:{
				CLEARANCEURL: global.baseUrl + 'medicals/report/0/clearance-letter'
			},
			controller: function($scope, $mdDialog, CLEARANCEURL, global){

				$scope.URL = CLEARANCEURL;

				$scope.Dialog_Close = function() {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};


	$scope.Load_Form();

	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.Load_Form();
	});

	var dList1 = $rootScope.$on('RELOGIN_FORM', function (event, data) {
		$scope.Submit_Form();
	});


	$scope.$on('$destroy', function() {
		dList();
		dList1();
	});


	

});

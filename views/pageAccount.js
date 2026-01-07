"use strict";


app.controller('PageAccount',function ($scope,$http,$timeout,$filter, $rootScope, global){


	$rootScope.$emit("Title_Global", { TITLE : 'ACCOUNT', BACKURL:'' });


	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		isSubmit : false,
		isSubmitPass : false,
		url : global.baseUrl + 'account/account-data',
		submitAccountUrl : global.baseUrl + 'account/submit-account',
		submitPasswordUrl : global.baseUrl + 'account/submit-account-password',
		USER: global.USER
	}



	$scope.User_Data = function(){

		$scope.opt.isSearch = true;


		$http.get($scope.opt.url, global.ajaxConfig) .then( function(response) {	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.f = [];
				$scope.f = response.data;

				$scope.f.SPECIALISTID = $scope.Check_Select_Value($scope.f.SPECIALISTID);

				if( $scope.opt.USER.LEVEL == 'ADMINISTRATOR' || $scope.opt.USER.LEVEL == 'BRANCH ASSISTANT' ){
					$scope.f.DoctorFields = false;
				} else{
					$scope.f.DoctorFields = true;
				}
				
			}

			$scope.opt.isLoaded = true;
			$scope.opt.isSearch = false;
		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isSearch = false;
			$scope.opt.isLoaded = true;
		});
	}


	$scope.Submit_Form = function(){

		$scope.opt.isSubmit = true;
		

		$http.post($scope.opt.submitAccountUrl,$scope.f, global.ajaxConfig).then( function(response){

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM');
			}
			else{

				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {

					global.Toast('SAVED');	
					global.Set_USER({
						NAME: $scope.f.NAME, 
						JOBTITLE: $scope.f.JOBTITLE, 
						SPECIALISTID: $scope.f.SPECIALISTID, 
						SUBCLINICID: $scope.f.SUBCLINICID,
						LICENSENO : $scope.f.LICENSENO,
						PTR : $scope.f.PTR
					});

					$rootScope.$emit("Update_User_Info", { });
				}
			}

			$scope.opt.isSubmit = false;
		},
		function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
		});

	}


	$scope.Submit_Form_Password = function(){

		$scope.opt.isSubmitPass = true;
		
		if( $scope.f.newPassword  != $scope.f.retypePassword ){

			global.Toast('New and Re-type Password missmatch');
			$scope.opt.isSubmitPass = false;
			return;
		}

		
		$http.post($scope.opt.submitPasswordUrl,$scope.f, global.ajaxConfig).then( function(response){

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM1');
			}
			else{

				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {
					global.Toast('CHANGED PASSWORD.');	
					$scope.f.currentPassword = '';
					$scope.f.newPassword = '';
					$scope.f.retypePassword = '';
				}
			}

			$scope.opt.isSubmitPass = false;
		},
		function(response){

			global.Relogin('FORM1');
			$scope.opt.isSubmitPass = false;
		});
	}


	$scope.Check_Select_Value = function(value){

		if( value != undefined ){
			if( isNaN(value) ){
				if( value == '' ){
					return null;
				}	
			} else {
				return parseInt(value);
			}
		} 
		return value;
	}



	$scope.User_Data();

	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.User_Data();
	});

	var dList1 = $rootScope.$on('RELOGIN_FORM', function (event, data) {
		$scope.Submit_Form();
	});

	var dList2 = $rootScope.$on('RELOGIN_FORM1', function (event, data) {
		$scope.Submit_Form_Password();
	});


	$scope.$on('$destroy', function() {
		dList();
		dList1();
		dList2();
	});


});

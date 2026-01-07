"use strict";
// admin

app.controller('FormSpecialist',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, global){


	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'settings/specialist/form-data/',
		submitUrl : global.baseUrl +'settings/specialist/submit-form',
		cancelUrl : '#!/settings/specialist'
	}
	

	$scope.Load_Form = function(){

		$http.get($scope.opt.formUrl, global.ajaxConfig) .then( function(response){	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.form = [];
				$scope.form = response.data;

				$scope.form.PRICE = parseFloat($scope.form.PRICE);
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

		$http.post($scope.opt.submitUrl, $scope.form, global.ajaxConfig).then( function(response){

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM');
			}
			else{
				
				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {
					global.Toast('SAVED');
					$location.url('/settings/specialist');
				}
			}

			$scope.opt.isSubmit = false;
		},
		function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
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




	if( $routeParams.P1 == 'new' ){

		$scope.opt.formUrl = global.baseUrl + 'settings/specialist/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW SPECIALIST', BACKURL: '#!/settings/specialist' });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'settings/specialist/form-data/' + $routeParams.P1;
		$rootScope.$emit("Title_Global", { TITLE : 'EDIT SPECIALIST', BACKURL: '#!/settings/specialist' });
	}

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

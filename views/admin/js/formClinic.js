"use strict";
// ADMIN

app.controller('FormClinic',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, global){
	
	$rootScope.$emit("Title_Global", { TITLE : 'EDIT CLINIC', BACKURL:'#!/clinics' });

	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'clinics/form-data',
		submitUrl : global.baseUrl + 'clinics/submit-form',
		cancelUrl : '#!/clinics',
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

				$scope.f.SALES = $scope.f.SALES == 'Y' ? true : false;
				$scope.f.IS_BLAST = $scope.f.IS_BLAST == 'Y' ? true : false;
				$scope.f.ACCOUNTBASE = $scope.f.ACCOUNTBASE == 'Y' ? true : false;
				$scope.f.ASSISTANTRECORD = $scope.f.ASSISTANTRECORD == 'Y' ? true : false;
			}

			$scope.opt.isLoaded = true;
		}, 
		function(response){ 
			global.Toast(response.data); 
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
					$location.url('/clinics');
				}
			}

			$scope.opt.isSubmit = false;
		},
		function(response){
			global.Toast(response.data); 
			$scope.opt.isSubmit = false;
		});
	}



	if( $routeParams.P1 == 'new' ){

		$scope.opt.formUrl = global.baseUrl + 'clinics/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW CLINIC', BACKURL: '#!/clinics' });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'clinics/form-data/' + $routeParams.P1;
		$rootScope.$emit("Title_Global", { TITLE : 'EDIT CLINIC', BACKURL: '#!/clinics' });
	}


	$scope.Load_Form();

});

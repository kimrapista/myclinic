"use strict";
// ASSISTANT


app.controller('FormPatient',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, global){

	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'patients/form-data/',
		submitUrl : global.baseUrl +'patients/submit-patient/',
		cancelUrl : '#!/patients/',
		recordUrl : '#!/patients/record/'
	}
	

	$scope.Load_Form = function(){

		$http.get($scope.opt.formUrl, global.ajaxConfig) .then( function(response) {	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.f = [];
				$scope.f = response.data;
				$scope.f.DOB = global.Date($scope.f.DOB);
				$scope.f.DATEREG = global.Date($scope.f.DATEREG);
				$scope.f.CIVILSTATUS = $scope.f.CIVILSTATUS == '' ? null : $scope.f.CIVILSTATUS;
				$scope.f.REVISIT = $scope.f.REVISIT == 'Y' ? true : false;	
				$scope.f.CANCELLED = $scope.f.CANCELLED == 'Y' ? true : false;
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

		$http.post($scope.opt.submitUrl, $scope.f, global.ajaxConfig).then( function(response){

			$scope.opt.isSubmit = false;

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM');
			}
			else if( response.data.err != undefined ) {
				
				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {
					global.Toast('SAVED');
					$location.url('/patients/record/' + response.data.suc.PATIENTID);
				}
			}
			else{
				global.Relogin('FORM');
			}

		}, function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
		});

	}



	if( $routeParams.P1 != undefined ){
		$scope.opt.formUrl = global.baseUrl + 'patients/form-data/' + $routeParams.P1;
		$scope.opt.cancelUrl = '#!/patients/record/' + $routeParams.P1;

		$rootScope.$emit("Title_Global", { TITLE : 'EDIT PATIENT', BACKURL: '#!/patients/record/' + $routeParams.P1 });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'patients/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW PATIENT', BACKURL: '#!/patients/' });
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

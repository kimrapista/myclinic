"use strict";
// ADMIN

app.controller('FormHospital',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, global){
	
	$rootScope.$emit("Title_Global", { TITLE : 'EDIT HOSPITAL', BACKURL:'#!/hospitals' });

	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'hospitals/form-data',
		submitUrl : global.baseUrl + 'hospitals/submit-form',
		cancelUrl : '#!/hospitals'
	}



	$scope.Load_Form = function(){

		$http.get( $scope.opt.formUrl, global.ajaxConfig ) .then( function(response) {	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.f = [];
				$scope.f = response.data;
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
					$location.url('/hospitals');
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

		$scope.opt.formUrl = global.baseUrl + 'hospitals/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW HOSPITAL', BACKURL: '#!/hospitals' });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'hospitals/form-data/' + $routeParams.P1;
		$rootScope.$emit("Title_Global", { TITLE : 'EDIT HOSPITAL', BACKURL: '#!/hospitals' });
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

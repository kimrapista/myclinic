"use strict";
// doctor

app.controller('FormSubClinic',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, global){


	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'clinics/subclinic/form-data/',
		submitUrl : global.baseUrl +'clinics/subclinic/submit-form',
		cancelUrl : '#!/clinic'
	}
	

	$scope.Load_Form = function(){

		$http.get($scope.opt.formUrl, global.ajaxConfig) .then( function(response){	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.form = [];
				$scope.form = response.data;
			}

			$scope.opt.isLoaded = true;

		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isLoaded = true;
		});
	}


	$scope.Lookup_Acronym = function(HOSPITALID){

		if( $scope.form != undefined){
			angular.forEach($scope.form.HOSPITALS , function(v,k){
				if( HOSPITALID == v.ID ){
					$scope.form.NAME = v.CODE;
				}
			});
		}
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
					localStorage.removeItem('SUBCLINICS');
					$rootScope.$emit("Load_Masterlist"); 
					$location.url('/clinic');
				}
			}

			$scope.opt.isSubmit = false;
		},
		function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
		});

	}





	if( $routeParams.P1 == 'new' ){

		$scope.opt.formUrl = global.baseUrl + 'clinics/subclinic/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW SUBCLINIC', BACKURL: '#!/clinic' });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'clinics/subclinic/form-data/' + $routeParams.P1;
		$rootScope.$emit("Title_Global", { TITLE : 'EDIT SUBCLINIC', BACKURL: '#!/clinic' });
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

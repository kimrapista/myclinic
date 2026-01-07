"use strict";
// doctor

app.controller('FormInstruction',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, global){


	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'settings/instructions/form-data/',
		submitUrl : global.baseUrl +'settings/instructions/submit-form',
		cancelUrl : '#!/settings/instructions'
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
				$scope.form.ACTIVE = $scope.form.ACTIVE == 'Y' ? true : false;
				
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
					localStorage.removeItem('INSTRUCTIONS');
					$rootScope.$emit("Load_Masterlist"); 
					$location.url('/settings/instructions');
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

		$scope.opt.formUrl = global.baseUrl + 'settings/instructions/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW INSTRUCTION', BACKURL: '#!/settings/instructions' });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'settings/instructions/form-data/' + $routeParams.P1;
		$rootScope.$emit("Title_Global", { TITLE : 'EDIT INSTRUCTION', BACKURL: '#!/settings/instructions' });
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

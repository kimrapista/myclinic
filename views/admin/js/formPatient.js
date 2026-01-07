"use strict";


 app.controller('FormPatient',function ($scope,$http,$timeout,$filter){

 	
 	$scope.init = function(url)
 	{

 		$scope.url = url;
		$scope.isSubmit = false;
		$scope.response = '';
		$scope.responseErr = '';

		readyShow();

 		$scope.loadForm();
 	}

 	$scope.loadForm = function(){

 		$http.get($scope.url)
		.then(
			function(response) 
			{	
				$scope.f = [];
				$scope.f = response.data;
				$scope.remove = false;
			}, 
	        function(response){ 
	        	$scope.loadForm(); 
	        }
	    );
 	}


	$scope.submitForm = function(){

		if( $scope.isSubmit ) return;


		$scope.isSubmit = true;
		$scope.response = '';
		$scope.responseErr = '';

		$http.post($scope.f.url,$scope.f).then(
			function(response){

				
				$scope.responseErr = response.data.err;

				if( !$scope.responseErr ){
					$scope.response = 'SAVED';

					$timeout(function(){ window.location = response.data.suc.redirect; },800);
				}

				$scope.isSubmit = false;
			},
			function(response){
				$scope.responseErr = response.data;
				$scope.isSubmit = false;
			}
		);

	}

	$scope.boolChar = function(v){ 
		if( v == 'N' ){ return 'Y'; }
		else if ( v == 'Y' ){ return 'N'; }
		else{ return 'N'; }   
	}


 });

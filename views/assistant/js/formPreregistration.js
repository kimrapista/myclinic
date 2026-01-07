"use strict";
app.controller('FormPreregistration',function ($scope,$http,$timeout,$filter){


	$scope.init 		= function(baseUrl,id)
	{

		$scope.url 		= baseUrl+'preregistration/edit_preregistration_data/'+id;
		$scope.isSubmit = false;
		$scope.title 	= '';


		$scope.loadForm();
	}

	$scope.loadForm 	= function(){

		$http.get($scope.url) .then( function(response) {	
			$scope.f 							= [];
			$scope.f 							= response.data;
			$scope.f.DOB 						= $filter('date')( new Date($scope.f.DOB) , 'MM/dd/y' );

			//$scope.AdmittedPatient 				= response.data.AdmittedPatient;
			$scope.isSubmit = false;

			if ($scope.f.POST 				= 'N') {$scope.f.POST = false;}
			else{ $scope.f.POST 			= true; }
			
			if($scope.f.ID > 0 ){ $scope.title 	= 'Edit Patient'; }
			
			else{ $scope.title 					= 'New Patient'; }

			hide_loadingpage();
		}, 
		function(response){ 
			Form_Error(response.data);
		} );
	}


	$scope.submitForm = function(){

		if( $scope.isSubmit ) return;

		Loading(true);
		$scope.isSubmit = true;

		$http.post($scope.f.URL,$scope.f).then( function(response){

			if( response.data.err 				!= '' ){
				Form_Error(response.data.err);
			}
			else {
				Form_Success('SAVED');

				if( response.data.suc.redirect != undefined )
					window.location 			= response.data.suc.redirect;
			}
			$scope.isSubmit 					= false;
		},
		function(response){
			Form_Error(response.data);
			$scope.isSubmit 					= false;
		} );
	}
});


'use strict';

app.controller('Preregistration',function ($scope,$http,$timeout,$filter){


	$scope.init = function(baseUrl)
	{
		$scope.url 		= baseUrl+'preregistration/index';
		$scope.formUrl	= baseUrl+'preregistration/form-data/';

		$scope.Load_List();
	}



	$scope.Load_List = function(){

		$http.get($scope.url) .then( function(response) {
			$scope.LIST = [];
			$scope.LIST = response.data;

			hide_loadingpage();					
		},
		function(response){ 
			Form_Error(response.data);
		});
	}
	


	$scope.Load_Form = function(detail){

		$('#form').modal('show');

		$scope.title 			= 'PREREGISTRATION';
		$scope.isSubmit 		= true;
		$scope.response 		= '';
		

		$http.get($scope.formUrl + ( detail != undefined ? detail.ID : 0) ).then(function(response){

			$scope.form = [];
			$scope.form = response.data;
			$scope.form.PRICE = parseFloat($scope.form.PRICE);

			$scope.form.source = detail;

			if( $scope.form.ID == 0 ){ $scope.title = 'New Patient'; }
			else{ $scope.title = 'NO. '+ $scope.form.ID; }


			$scope.isSubmit = false;

		},
		function(response){ });
	}



	$scope.Submit_Form = function(){

		if( $scope.isSubmit ) return;

		$scope.isSubmit = true;
		
		Loading(true);

		$http.post($scope.form.URL,$scope.form).then(function(response){

			if( response.data.err != '' ){

				Form_Error(response.data.err);
			}
			else{

				if( response.data.suc.new ){

					$scope.Load_List();
				}
				else{
					
					$scope.form.source.NAME = $scope.form.NAME;
					$scope.form.source.PRICE = $scope.form.PRICE;			
				}

				Form_Success('SAVED');
			}

			$('#form').modal('hide');
			$scope.isSubmit = false;

		},
		function(response){

			Form_Error(response.data);
			$scope.isSubmit = false;
		});

	}


});

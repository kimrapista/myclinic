
'use strict';

app.controller('MedicalHistory',function ($scope,$http,$timeout,$filter){


	$scope.init = function(baseUrl)
	{
		$scope.url 		= baseUrl+'medical-history/index';
		$scope.formUrl	= baseUrl+'medical-history/form-data/';

		$scope.Load_List();
	}



	$scope.Load_List = function(){

		$http.get($scope.url, global.ajaxConfig) .then( function(response) {
			$scope.LIST = [];
			$scope.LIST = response.data;

			angular.forEach($scope.LIST,function(v,k){
				v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
			})

			hide_loadingpage();					
		},
		function(response){ 
			Form_Error(response.data);
		});
	}
	


	$scope.Load_Form = function(detail){

		$('#form').modal('show');

		$scope.title 			= 'Medical History';
		$scope.isSubmit 		= true;
		$scope.response 		= '';
		

		$http.get($scope.formUrl + ( detail != undefined ? detail.ID : 0) , global.ajaxConfig).then(function(response){

			$scope.form = [];
			$scope.form = response.data;
			$scope.form.CANCELLED = $scope.form.CANCELLED == 'Y' ? true : false;

			$scope.form.source = detail;

			if( $scope.form.ID == 0 ){ $scope.title = 'New Disease'; }
			else{ $scope.title = 'NO. '+ $scope.form.ID; }


			$scope.isSubmit = false;

		},
		function(response){ });
	}



	$scope.Submit_Form = function(){

		if( $scope.isSubmit ) return;

		$scope.isSubmit = true;
		
		Loading(true);

		$http.post($scope.form.URL,$scope.form, global.ajaxConfig).then(function(response){

			if( response.data.err != '' ){

				Form_Error(response.data.err);
			}
			else{

				if( response.data.suc.new ){

					$scope.Load_List();
				}
				else{
					
					$scope.form.source.NAME = $scope.form.NAME;
					$scope.form.source.CANCELLED = $scope.form.CANCELLED;			
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

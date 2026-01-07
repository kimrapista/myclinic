"use strict";
 

 app.controller('PagePatient',function ($scope,$http,$timeout,$filter){

 	
 	$scope.init = function(url)
 	{
 		
 		$scope.url = url;
 	 	readyShow();
 	 	$scope.loadPatient();	
 	}



	$scope.loadPatient = function(){
		
		$http.get($scope.url)
		.then(
			function(response) 
			{	
				$scope.patient = [];
				$scope.patient = response.data;
				$scope.patient.DATEREG = new Date($scope.patient.DATEREG);
				$scope.patient.DOB = new Date($scope.patient.DOB);

				angular.forEach($scope.patient.MEDICALS,function(val,key){
					val.CHECKUPDATE = new Date(val.CHECKUPDATE);
				});

			}, 
	        function(response){ 
	        	$scope.loadPatient(); 
	        }
	    );

	}


	$scope.emptyVariable = function(v){ if( v == null || v == '' ){ return '--' }; return v; }


	$scope.subMenus = function(v){

		$scope.contactPanel = {active:false, class:''};
		$scope.emergencyPanel = {active:false, class:''};
		$scope.medicalPanel = {active:false, class:''};

		
		if(v == 'contact'){ $scope.contactPanel.active = true; $scope.contactPanel.class = 'active';}
		else if(v == 'emergency'){ $scope.emergencyPanel.active = true; $scope.emergencyPanel.class = 'active';}
		else if(v == 'medical'){ $scope.medicalPanel.active = true; $scope.medicalPanel.class = 'active';}
	}



 });

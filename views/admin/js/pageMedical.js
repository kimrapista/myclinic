"use strict";
 

 app.controller('PageMedical',function ($scope,$http,$timeout,$filter){

 	
 	$scope.init = function(url)
 	{
 		$scope.url = url;
 		
 		$scope.search = [];
 		$scope.search.url = url;
 		$scope.search.text = '';
		$scope.search.dateFrom = $filter('date')( new Date() , 'MM/dd/y' );
		$scope.search.dateTo = $filter('date')( new Date() , 'MM/dd/y' );
 		$scope.search.isSubmit = false; 


		readyShow();
 		
		$scope.loadSearchMedicals();
 		
	}


	$scope.loadSearchMedicals = function(){

		if( $scope.search.isSubmit ) return false;

		$scope.search.isSubmit = true;

		$http.post($scope.search.url,{text: $scope.search.text,dateFrom: $scope.search.dateFrom,dateTo: $scope.search.dateTo }).then(
			function(response) 
			{	
				$scope.records = [];
				$scope.records = response.data;
				$scope.patientsRecorrectData();	
				$scope.search.isSubmit = false;

				$timeout(function() { $('#searchText').focus(); });
			}, 
	        function(response){ 
	        	alert(response.data);
	        	$scope.search.isSubmit = false;
	        }
	    );
		
	} 


	$scope.patientsRecorrectData = function(){

		angular.forEach($scope.records,function(val,key){ 
			val.NAME = val.LASTNAME+', '+val.FIRSTNAME+' '+val.MIDDLENAME;
			val.NAME1 = val.LASTNAME+' '+val.FIRSTNAME+' '+val.MIDDLENAME;
			val.NAME2 = val.FIRSTNAME+' '+val.MIDDLENAME+' '+val.LASTNAME;
			val.NAME3 = val.FIRSTNAME+' '+val.LASTNAME;
		});	

	}


	$scope.cacheSearch = function(){
		localStorage.setItem('searchName',$scope.search.text);
		localStorage.setItem('searchDateFrom',$scope.search.dateFrom);
		localStorage.setItem('searchDateTo',$scope.search.dateTo);
	}




 });

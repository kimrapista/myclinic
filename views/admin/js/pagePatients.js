"use strict";
 

 app.controller('PagePatients',function ($scope,$http,$timeout,$filter){

 	
 	$scope.init = function(url,searchUrl)
 	{
 		
 		$scope.url = url;
 		$scope.searchUrl = searchUrl;
 		
 		$scope.search = '';
 		$scope.searchOn = false;

 		$scope.currentPage = 0;
 		$scope.totalPage = 0;
 		$scope.limitPage = 10;
 		$scope.pages = [];

 		if( localStorage.getItem("searchPatient") ) $scope.search = localStorage.getItem("searchPatient");
 		if( localStorage.getItem("currentPage") ) $scope.currentPage = localStorage.getItem("currentPage");
		
 		readyShow();
 		
		if( $scope.search != '' ){$scope.loadSearchPatients();}
		else{ $scope.loadPatients(); }
 		

	}



	$scope.loadPatients = function(){
		
		$scope.searchOn = true;

		$http.get($scope.url)
		.then(
			function(response) 
			{	
				$scope.patients = [];
				$scope.patients = response.data;

				$scope.patientsRecorrectData();
				$scope.initializePaging();

				$scope.searchOn = false;

				$timeout(function() { $('#searchText').focus(); });
			}, 
	        function(response){ 
	        	$scope.loadPatients(); 
	        }
	    );

	}


	$scope.loadSearchPatients = function(){
		$scope.searchOn = true;

		$http.post($scope.searchUrl,{search:$scope.search})
		.then(
			function(response) 
			{	
				$scope.patients = [];
				$scope.patients = response.data;

				$scope.patientsRecorrectData();

				$scope.initializePaging();

				$scope.searchOn = false;

				$timeout(function() { $('#searchText').focus(); });
			}, 
	        function(response){ 
	        	$scope.loadSearchPatients(); 
	        }
	    );

	}

	$scope.patientsRecorrectData = function(){

		angular.forEach($scope.patients,function(val,key){ 
			val.DATEREG = new Date(val.DATEREG); 
		});	

	}





	$scope.cacheSearch = function(){
		localStorage.setItem('searchPatient',$scope.search);
		$scope.currentPage = 0;
		localStorage.setItem('currentPage',$scope.currentPage);

	}


	$scope.searchLoad = function(){
		if( $scope.search != '' ){$scope.loadSearchPatients();}
		else{ $scope.loadPatients(); }
	}


	$scope.initializePaging = function(){
		
		var totalData = $scope.patients.length;

		if( $scope.search != ''){
			
			var searchData = $filter('filter')($scope.patients,$scope.search);
			totalData = searchData.length;
		}

		$scope.totalPage = Math.ceil( totalData / $scope.limitPage);

		$scope.pages = [];
		for (var i = 0; i < $scope.totalPage; i++) { 
			$scope.pages.push({page:i, label:(i+1), class: 'btn-dark', visible: false }); 
		}

		$scope.changePage($scope.currentPage);
	}


	$scope.checkDataPageLimit = function(page){
		var startWith = ($scope.currentPage * $scope.limitPage);
		
		if( page >= startWith  && page < startWith+$scope.limitPage ) { return true; }
		else{ return false; }
	}

	
	$scope.changePage = function(v){
		
		v = parseInt(v);
		
		if( v < 0) v =0;
		$scope.currentPage = v;

		localStorage.setItem('currentPage',$scope.currentPage);

		angular.forEach($scope.pages,function(val,key){

			if( val.page >= $scope.currentPage-1 &&  val.page <= $scope.currentPage+2 ){ 
				val.visible = true; 

				if( val.page == $scope.currentPage ){
					val.class= 'btn-light'; 
				}
				else{
					val.class= 'btn-dark';
				}
			}	
			else{ 
				val.visible = false; 
				val.class = 'btn-dark';
			}

		});
	}


	$scope.submitOldData = function(){
		var saveurl = $scope.url+'_submit';
		$scope.dummy = true;
		$http.get(saveurl)
		.then(
			function(response) 
			{	
				location.reload();
				$scope.dummy = false;
			}, 
	        function(response){}
	    );

	}




 });

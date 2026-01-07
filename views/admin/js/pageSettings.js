"use strict";
 


 app.controller('PageSettings',function ($scope,$http,$timeout,$filter){

 	
 	$scope.init = function(baseUrl)
 	{

 		$scope.services = [];
 		$scope.services.url = baseUrl+'list-of-services';
 		$scope.services.token = '';
 		$scope.services.formUrl = '';
 		$scope.services.list = null;
 		$scope.services.isSubmit = false;
 		$scope.services.response = '';

 		$scope.discounts = [];
 		$scope.discounts.url = baseUrl+'list-of-discounts';
 		$scope.discounts.token = '';
 		$scope.discounts.formUrl = '';
 		$scope.discounts.list = null;
 		$scope.discounts.isSubmit = false;
 		$scope.discounts.response = '';

 		$scope.HMO = [];
 		$scope.HMO.url = baseUrl+'list-of-hmo';
 		$scope.HMO.token = '';
 		$scope.HMO.formUrl = '';
 		$scope.HMO.list = null;
 		$scope.HMO.isSubmit = false;
 		$scope.HMO.response = '';

 		$scope.medicines = [];
 		$scope.medicines.url = baseUrl+'list-of-medicines';
 		$scope.medicines.token = '';
 		$scope.medicines.formUrl = '';
 		$scope.medicines.list = null;
 		$scope.medicines.isSubmit = false;
 		$scope.medicines.response = '';
 		
 		$scope.users = [];
 		$scope.users.url = baseUrl+'list-of-users';
 		$scope.users.list = null;

 		readyShow();
 		
 		$scope.loadServices();
 		$scope.loadDiscounts();
 		$scope.loadHMO();
 		$scope.loadMedicines();
 		$scope.loadUsers();
	}



	$scope.loadServices = function(){

		$scope.services.isSubmit = true;

		$http.get($scope.services.url)
		.then(
			function(response) 
			{	
				$scope.services.list = [];
				$scope.services.token = response.data[0];
				$scope.services.formUrl = response.data[1];
				$scope.services.list = response.data[2];

				angular.forEach($scope.services.list,function(val,key){
					val.PRICE = parseFloat(val.PRICE);
				});

				$scope.services.isSubmit = false;
			}, 
	        function(response){ 
	        	alert(response.data);
	        }
	    );

	}



	$scope.loadDiscounts = function(){

		$scope.discounts.isSubmit = true;

		$http.get($scope.discounts.url)
		.then(
			function(response) 
			{	
				$scope.discounts.list = [];
				$scope.discounts.token = response.data[0];
				$scope.discounts.formUrl = response.data[1];
				$scope.discounts.list = response.data[2];

				angular.forEach($scope.discounts.list,function(val,key){
					val.AMOUNT = parseFloat(val.AMOUNT);
				});

				$scope.discounts.isSubmit = false;
			}, 
	        function(response){ 
	        	alert(response.data);
	        }
	    );
	}


	$scope.loadHMO = function(){

		$scope.HMO.isSubmit = true;

		$http.get($scope.HMO.url)
		.then(
			function(response) 
			{	
				$scope.HMO.list = [];
				$scope.HMO.token = response.data[0];
				$scope.HMO.formUrl = response.data[1];
				$scope.HMO.list = response.data[2];

				$scope.HMO.isSubmit = false;
			}, 
	        function(response){ 
	        	alert(response.data);
	        }
	    );

	}


	$scope.loadMedicines = function(){

		$scope.medicines.isSubmit = true;

		$http.get($scope.medicines.url)
		.then(
			function(response) 
			{	
				$scope.medicines.list = [];
				$scope.medicines.token = response.data[0];
				$scope.medicines.formUrl = response.data[1];
				$scope.medicines.list = response.data[2];

				$scope.medicines.isSubmit = false;
			}, 
	        function(response){ 
	        	alert(response.data);
	        }
	    );

	}


	$scope.loadUsers = function(){

		$http.get($scope.users.url)
		.then(
			function(response) 
			{	
				$scope.users.list = [];
				$scope.users.list = response.data;

			}, 
	        function(response){ 
	        	alert(response.data);
	        }
	    );

	}


	$scope.submitServices = function(){

		if( $scope.services.isSubmit ) return;


		$scope.services.isSubmit = true;
		$scope.services.response = '';
		

		$http.post($scope.services.formUrl,{token:$scope.services.token,list:$scope.services.list}).then(
			function(response){

				$scope.services.response = response.data.err;

				if( $scope.services.response == ''){
					$scope.services.response = 'SAVED';
					// if( response.data['suc'].newID ) $scope.loadServices();
				}
				$scope.loadServices();
				$scope.services.isSubmit = false;
			},
			function(response){
				$scope.services.response = response.data;
				$scope.services.isSubmit = false;
			}
		);

	}


	$scope.addServices = function(){

		$scope.services.list.push({ID:0,NAME:'',PRICE:0});
	}

	$scope.removeService = function(key){

		$scope.services.list.splice(key,1);
	}



	$scope.submitDiscounts = function(){

		if( $scope.discounts.isSubmit ) return;


		$scope.discounts.isSubmit = true;
		$scope.discounts.response = '';
		

		$http.post($scope.discounts.formUrl,{token:$scope.discounts.token,list:$scope.discounts.list}).then(
			function(response){

				$scope.discounts.response = response.data.err;

				if( $scope.discounts.response == ''){
					$scope.discounts.response = 'SAVED';
					// if( response.data['suc'].newID ) $scope.loadDiscounts();
				}
				$scope.loadDiscounts();	
					
				$scope.discounts.isSubmit = false;
			},
			function(response){
				$scope.discounts.response = response.data;
				$scope.discounts.isSubmit = false;
			}
		);

	}

	$scope.addDiscounts = function(){

		$scope.discounts.list.push({ID:0, NAME:'', PERCENTAGE:'N', AMOUNT:0});
	}

	$scope.removeDiscount = function(key){

		$scope.discounts.list.splice(key,1);
	}


	$scope.submitHMO = function(){

		if( $scope.HMO.isSubmit ) return;


		$scope.HMO.isSubmit = true;
		$scope.HMO.response = '';
		

		$http.post($scope.HMO.formUrl,{token:$scope.HMO.token,list:$scope.HMO.list}).then(
			function(response){

				$scope.HMO.response = response.data.err;

				if( $scope.HMO.response == ''){
					$scope.HMO.response = 'SAVED';
					// if( response.data['suc'].newID ) $scope.loadHMO();
				}
				$scope.loadHMO();
				
				$scope.HMO.isSubmit = false;
			},
			function(response){
				$scope.HMO.response = response.data;
				$scope.HMO.isSubmit = false;
			}
		);

	}

	$scope.addHMO = function(){

		$scope.HMO.list.push({ID:0, NAME:''});
	}

	$scope.removeHMO = function(key){

		$scope.HMO.list.splice(key,1);
	}


	$scope.submitMedicines = function(){

		if( $scope.medicines.isSubmit ) return;


		$scope.medicines.isSubmit = true;
		$scope.medicines.response = '';
		

		$http.post($scope.medicines.formUrl,{token:$scope.medicines.token,list:$scope.medicines.list}).then(
			function(response){

				$scope.medicines.response = response.data.err;

				if( $scope.medicines.response == ''){
					$scope.medicines.response = 'SAVED';
					// if( response.data['suc'].newID ) $scope.loadHMO();
				}
				$scope.loadMedicines();
				
				$scope.medicines.isSubmit = false;
			},
			function(response){
				$scope.medicines.response = response.data;
				$scope.medicines.isSubmit = false;
			}
		);

	}

	$scope.addMedicine = function(){

		$scope.medicines.list.push({ID:0, NAME:''});
	}

	$scope.removeMedicine = function(key){

		$scope.medicines.list.splice(key,1);
	}


	$scope.boolChar = function(v){ 
		if( v == 'N' ){ return 'Y'; }
		else if ( v == 'Y' ){ return 'N'; }
		else{ return 'N'; }   
	}


 });

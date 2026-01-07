"use strict";


 app.controller('FormMedical',function ($scope,$http,$timeout,$filter){

 	
 	$scope.init = function(url)
 	{

 		$scope.url = url;
		$scope.isSubmit = false;
		$scope.response = '';
		$scope.responseErr = '';

		$scope.galleryModal = false;
		$scope.carouselActive = false;
		$scope.carousel = [];



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

				
				$scope.f.AMOUNT = parseFloat($scope.f.AMOUNT);
				$scope.f.AMOUNTCHANGE = parseFloat($scope.f.AMOUNTCHANGE);

				angular.forEach($scope.f.SERVICES,function(val,key){ 
					val.REMOVE = 0; 
					val.PRICE = parseFloat(val.PRICE);
					val.QUANTITY = parseInt(val.QUANTITY);
				});

				angular.forEach($scope.f.DISCOUNTS,function(val,key){ 
					val.REMOVE = 0; 
					val.AMOUNT = parseFloat(val.AMOUNT);
				});

				angular.forEach($scope.f.MEDICINES,function(val,key){ 
					val.REMOVE = 0; 
				});
				// angular.forEach($scope.f.INSTRUCTION,function(val,key){ 
				// 	val.REMOVE = 0; 
				// });

				$scope.remove = false;
				$scope.initImages();	

			}, 
	        function(response){ 
	        	alert(response.data); 
	        }
	    );
 	}


 	$scope.initImages = function(){
 		angular.forEach($scope.f.IMAGES,function(val,key){
			val.class = val.CANCELLED == 'Y' ? 'disabled':'';
		});
 	}


	$scope.submitForm = function(backToProfile){

		if( $scope.isSubmit ) return;


		$scope.isSubmit = true;
		$scope.response = '';
		$scope.responseErr = '';

		$http.post($scope.f.URL,$scope.f).then(
			function(response){

				$scope.responseErr = response.data.err;

				if( !$scope.responseErr ){
					$scope.response = 'SAVED';

					if( backToProfile && response.data.suc.redirect){
						$timeout(function(){ window.location = response.data.suc.redirect; },800);
					}
					else{	
						$scope.url = response.data.suc.getUrl;
						// $scope.f = [];
						$scope.loadForm();
						$timeout(function(){ $scope.response = ''; },2000);
					}
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


	$scope.addServices = function(){

		$scope.f.SERVICES.push({ID:0, SERVICEID:0, NAME: '', QUANTITY: 1, CANCELLED: '', PRICE:0, REMOVE: 0, EDIT: true});
	}

	$scope.removeServices = function(index){

		if( $scope.f.SERVICES[index].ID > 0) {
			$scope.f.SERVICES[index].REMOVE = 2;
			$scope.f.SERVICES[index].CANCELLED = 'Y';
			$scope.f.SERVICES[index].EDIT = true;
		}
		else{
			$scope.f.SERVICES.splice(index,1);
		}
	}	

	$scope.getServicesDetail = function(index){
		angular.forEach($scope.f.list_services,function(val,key){
			if( $scope.f.SERVICES[index].SERVICEID == val.ID){
				$scope.f.SERVICES[index].NAME = val.NAME;
				$scope.f.SERVICES[index].PRICE = parseFloat(val.PRICE);
				$scope.f.SERVICES[index].CANCELLED = 'N';
				$scope.f.SERVICES[index].EDIT = true;
			}
		});
	}


	$scope.totalServices = function(){
		var total = 0;
		if( $scope.f != null )
			angular.forEach($scope.f.SERVICES,function(val,key){ 
				if( val.REMOVE < 2 )
					total+= parseFloat(val.PRICE) * val.QUANTITY; 
			});
		return total;
	}




	$scope.addDiscounts = function(){

		$scope.f.DISCOUNTS.push({ID:0, DISCOUNTID:0, NAME: '', PERCENTAGE: '', AMOUNT: 0, CANCELLED: '', REMOVE: 0, EDIT:false});
	}

	$scope.removeDiscounts = function(index){

		if( $scope.f.DISCOUNTS[index].ID > 0) {
			$scope.f.DISCOUNTS[index].REMOVE = 2;
			$scope.f.DISCOUNTS[index].CANCELLED = 'Y';
			$scope.f.DISCOUNTS[index].EDIT = true;
		}
		else{
			$scope.f.DISCOUNTS.splice(index,1);
		}
	}

	$scope.getDiscountsDetail = function(index){
		angular.forEach($scope.f.list_discounts,function(val,key){
			if( $scope.f.DISCOUNTS[index].DISCOUNTID == val.ID){
				$scope.f.DISCOUNTS[index].NAME = val.NAME;
				$scope.f.DISCOUNTS[index].PERCENTAGE = val.PERCENTAGE;
				$scope.f.DISCOUNTS[index].AMOUNT = parseFloat(val.AMOUNT);
				$scope.f.DISCOUNTS[index].CANCELLED = 'N';
				$scope.f.DISCOUNTS[index].EDIT = true;
			}
		});
	}


	$scope.totalDiscounts = function(){

		var totalPayment = $scope.totalServices();
		var totalDeduction = 0;
		var deductionPercent = 0;
		var deduction = 0;

		if( $scope.f != null )
			angular.forEach($scope.f.DISCOUNTS,function(val,key){ 
				if( val.REMOVE < 2 ){
					if(val.PERCENTAGE == 'Y'){ deductionPercent += parseFloat(val.AMOUNT); }
					else{ deduction += parseFloat(val.AMOUNT); }
				}
			});

		deductionPercent = totalPayment * ( (deductionPercent/100) );

		totalDeduction = deductionPercent + deduction;
		
		if( $scope.f != null )
			$scope.f.AMOUNTCHANGE = $scope.f.AMOUNT - (totalPayment - totalDeduction);

		return totalDeduction;
	}



	$scope.carouselPanel = function(){

		$scope.carouselActive = !$scope.carouselActive;
		if( $scope.carouselActive ){
			 $('body').css('overflow', 'hidden');
		}
		else{
			$('body').css('overflow', 'auto');
		}
	}


	$scope.imageCarousel = function(v){

		if( $scope.f.IMAGES[v] )
			$scope.carousel = {IMAGEPATH: $scope.f.IMAGES[v].IMAGEPATH, key:v};
	}

	$scope.imageExist = function(v){

		if( $scope.f ){
			if( $scope.f.IMAGES[v] != null ){ console.log('a'); return true; }
			return false;
		}
	}



	$scope.addMedicine = function(){

		$scope.f.MEDICINES.push({ID:0, MEDICINEID:0, FREQUENCY:'', INSTRUCTION:'', CANCELLED:'N', EDIT:true, REMOVE: 0});
	}


	$scope.removeMedicine = function(key){
		
		if( $scope.f.MEDICINES[key].ID == 0){
			$scope.f.MEDICINES.splice(key,1);
		}
		else{
			$scope.f.MEDICINES[key].CANCELLED = 'Y';
			$scope.f.MEDICINES[key].EDIT = true;
			$scope.f.MEDICINES[key].REMOVE = 2;
		}
	}

	$scope.editted = function(v){ v.EDIT = true; }

	$scope.reportPanel = function(){
		$scope.reportActive = !$scope.reportActive;
	}

 });



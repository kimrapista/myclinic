"use strict";
// doctor

app.controller('PageAppointment',function ($scope,$http,$timeout,$filter, $rootScope, global){ 


	$rootScope.$emit("Title_Global", { TITLE : 'APPOINTMENTS', BACKURL:'' });


	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		searchText : '',
		dateFrom : new Date(),
		dateTo : new Date(),
		searchUrl : global.baseUrl + 'appointments/index',
		viewPatientUrl : '#!/patients/record/',
		viewMRUrl : '#!/patients/',
	}

	
	$scope.Search_MR = function(){

		$scope.opt.isSearch = true;

		$http.post($scope.opt.searchUrl, {text: $scope.opt.searchText, dateFrom: $scope.opt.dateFrom, dateTo: $scope.opt.dateTo }, global.ajaxConfig)
		.then( function(response) {	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.records = [];
				$scope.records = response.data;
			}

			$scope.opt.isLoaded = true;
			$scope.opt.isSearch = false;

			$rootScope.$emit("Search_Global_Done");
		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isSearch = false;
			$scope.opt.isLoaded = true;
		});

	} 


	$scope.Search_MR();


	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.Search_MR();
	});


	var dList1 = $rootScope.$on('Search_Global_Control', function (event, data) {

		$scope.opt.searchText = data.text;
		$scope.opt.dateFrom = data.dateFrom;
		$scope.opt.dateTo = data.dateTo;

		$scope.Search_MR();
	});
	

	$scope.$on('$destroy', function() {
		dList();
		dList1();
	});


});

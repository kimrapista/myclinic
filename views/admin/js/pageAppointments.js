"use strict";
// admin

app.controller('PageAppointments',function ($scope,$http,$timeout,$filter, $rootScope,$q, global){ 


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
		reTextUrl : global.baseUrl +'appointments/retext/',
		isRetext : false
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

		}, 
		function(response){ 
			global.Toast(response.data);
			$scope.opt.isSearch = false;
			$scope.opt.isLoaded = true;
		});

	} 



	$scope.Retext = function(key){

		if( $scope.records[key] != undefined ){

			$scope.opt.isRetext = true;

			$http.get($scope.opt.reTextUrl + $scope.records[key].ID, global.ajaxConfig)
			.then( function(response) {	

				if( response.data == 'RELOGIN'){
					global.Relogin();
				}
				else{
					$scope.Retext(key+1);
				}
			}, 
			function(response){ 
				global.Toast(response.data);
			});
		}
		else{
			$scope.opt.isRetext = false;
		}
	}


	$scope.Retext_All = function(){

		global.Toast('Refreshing messages for appointment.');

		$scope.Retext(0);
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

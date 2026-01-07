"use strict";
// doctor

app.controller('PageMedical',function ($scope,$http,$timeout,$filter, $rootScope, $mdDialog, global){

	$rootScope.$emit("Title_Global", { TITLE : 'MEDICAL RECORDS', BACKURL:''});


	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		searchText : '',
		dateFrom : new Date(),
		dateTo : new Date(),
		searchUrl : global.baseUrl + 'medicals/index',
		viewPatientUrl : '#!/patients/record/',
		viewMRUrl : '#!/patients/'
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

	$scope.MR_Preview = function(ev, PATIENTID, ID) {

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_preview_mr.html',
			locals:{
				PATIENTID: PATIENTID,
				MRID : ID
			},
			controller: function($scope,$mdDialog, $http, $location, PATIENTID, MRID, global){

				$scope.previewLoading = true;
				$scope.MR = [];

				$http.get( global.baseUrl +'medicals/mr-preview/' + MRID, global.ajaxConfig)
				.then(function(response){

					$scope.MR = response.data;

					$scope.MR.CHECKUPDATE =  global.Date($scope.MR.CHECKUPDATE);

					if( $scope.MR.LMP != null )
						$scope.MR.LMP = global.Date($scope.MR.LMP);

					$scope.previewLoading = false;
				},
				function(response){

				});

				$scope.Edit_MR = function(){
					$location.url('patients/'+ PATIENTID +'/medical-record/'+ MRID);
					$scope.close();
				}

				$scope.close = function () {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose: true,
			fullscreen: true
		})
		.then(function(answer) {
			
		}, function() {
			
		});
	};


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

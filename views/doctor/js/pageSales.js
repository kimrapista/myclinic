"use strict";
// doctor

app.controller('PageSales',function ($scope,$http,$timeout,$filter, $rootScope,$mdDialog, global){

	$rootScope.$emit("Title_Global", { TITLE : 'SALES', BACKURL:'', dateFrom: new Date(), dateTo: new Date() });


	$scope.opt = {
		isLoaded : false,
		isSearch : false,
		searchText : '',
		dateFrom : new Date(),
		dateTo : new Date(),
		searchUrl : global.baseUrl + 'sales/index',
		detailReportUrl : global.baseUrl + 'sales/detail-report',
		summaryReportUrl : global.baseUrl + 'sales/summary-report', 
		editMRUrl : '#!/patients/'
	}


	
	$scope.Search_Sales = function(){

		$scope.opt.isSearch = true;

		$http.post($scope.opt.searchUrl, {text: $scope.opt.searchText, dateFrom: $scope.opt.dateFrom, dateTo: $scope.opt.dateTo }, global.ajaxConfig)
		.then(function(response){	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.SUMMARY = [];
				$scope.SUMMARY = response.data.SUMMARY;

				$scope.TOTALSUMMARY = 0;
				$scope.TOTALRECORD = 0;

				angular.forEach($scope.SUMMARY, function(v,k){
					v.MRCOUNT = parseFloat(v.MRCOUNT);
					v.NETPAYABLES = parseFloat(v.NETPAYABLES);
					$scope.TOTALSUMMARY += v.NETPAYABLES;
					$scope.TOTALRECORD += v.MRCOUNT;
				});

				$scope.DETAIL = [];
				$scope.DETAIL = response.data.DETAIL;

				$scope.TOTALDETAIL = 0;

				angular.forEach($scope.DETAIL, function(v,k){

					v.NETPAYABLES = parseFloat(v.NETPAYABLES);
					$scope.TOTALDETAIL += v.NETPAYABLES;
				});

				
				$scope.SERVICES = [];
				$scope.SERVICES = response.data.SERVICES;

				$scope.TOTALSERVICES = 0;
				$scope.TOTALUNSERVICES = 0;

				angular.forEach($scope.SERVICES, function(v,k){
					v.QUANTITY = parseFloat(v.QUANTITY);
					v.AMOUNT = parseFloat(v.AMOUNT);

					$scope.TOTALSERVICES += v.AMOUNT;
				});

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


	$scope.Summary_Report = function(ev) {

		var url = $scope.opt.summaryReportUrl +'/'+ $filter('date')(new Date($scope.opt.dateFrom) , 'y-MM-dd' ) +'/'+ $filter('date')(new Date($scope.opt.dateTo),'y-MM-dd');
		
		$mdDialog.show({
			template: `
			<md-dialog aria-label="Sales Summary Report" class="dialog-view-image">
			<md-toolbar>
			<div class="md-toolbar-tools">
			<h2 class="md-title">Sales Summary Report</h2>
			<span flex></span>
			<md-button class="md-icon-button" ng-click="Dialog_Close()">
			<md-icon class="material-icons" aria-label="Close dialog">close</md-icon>
			</md-button>
			</div>
			</md-toolbar>
			<md-dialog-content>
			<iframe src="`+ url +`" scrolling="auto" ></iframe>
			</md-dialog-content>
			</md-dialog>
			`,
			controller: 'PageSales',
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose: true
		})
		.then(function(answer) {
			
		}, function() {
			
		});
	};


	$scope.Detail_Report = function(ev) {

		var url = $scope.opt.detailReportUrl +'/'+ $filter('date')(new Date($scope.opt.dateFrom) , 'y-MM-dd' ) +'/'+ $filter('date')(new Date($scope.opt.dateTo),'y-MM-dd');
		
		$mdDialog.show({
			template: `
			<md-dialog aria-label="Sales Detail Report" class="dialog-view-image">
			<md-toolbar>
			<div class="md-toolbar-tools">
			<h2 class="md-title">Sales Detail Report</h2>
			<span flex></span>
			<md-button class="md-icon-button" ng-click="Dialog_Close()">
			<md-icon class="material-icons" aria-label="Close dialog">close</md-icon>
			</md-button>
			</div>
			</md-toolbar>
			<md-dialog-content>
			<iframe src="`+ url +`" scrolling="auto" ></iframe>
			</md-dialog-content>
			</md-dialog>
			`,
			controller: 'PageSales',
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose: true
		})
		.then(function(answer) {
			
		}, function() {
			
		});
	};


	$scope.Dialog_Close = function() {
		$mdDialog.cancel();
	};


	$scope.Search_Sales();


	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.Search_Sales();
	});

	var dList1 = $rootScope.$on('Search_Global_Control', function (event, data) {

		$scope.opt.searchText = data.text;
		$scope.opt.dateFrom = data.dateFrom;
		$scope.opt.dateTo = data.dateTo;

		$scope.Search_Sales();
	});
	

	$scope.$on('$destroy', function() {
		dList();
		dList1();
	});
});
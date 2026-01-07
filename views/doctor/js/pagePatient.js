"use strict";
// doctor

app.controller('PagePatient',function ($scope, $http, $timeout, $filter, $rootScope, $routeParams, $mdDialog, $mdBottomSheet, $location, global){

	$rootScope.$emit("Title_Global", { TITLE : 'PATIENT RECORDS', BACKURL:'#!/patients/' });


	$scope.opt = {
		isLoaded : false,
		recordUrl : global.baseUrl + 'patients/patient-information/' + $routeParams.P1,
		submitRemovePatientUrl : global.baseUrl + 'patients/submit-remove-patient',
		editPatientUrl : '#!/patients/edit/' + $routeParams.P1,
		patientsUrl : '#!/patients/',
		newMRUrl : '#!/patients/'+ $routeParams.P1 +'/medical-record/new',
		editMRUrl : '#!/patients/'+ $routeParams.P1 +'/medical-record/',
		submitRemoveMRUrl : global.baseUrl + 'medicals/submit-remove-record/',
		avatarUrl : global.baseUrl + 'assets/css/images/patient_default.png',
		medicalRecordReportUrl : global.baseUrl +'medicals/report/'
	}


	$scope.Load_Patient = function(){
		
		$http.get($scope.opt.recordUrl, global.ajaxConfig).then( function(response) {	

			if ( response.data == 'RELOGIN' ){
				global.Relogin();
			}
			else if( response.data.TOKEN != undefined ){

				$scope.PATIENT = [];
				$scope.PATIENT = response.data;
				$scope.PATIENT.DATEREG = global.Date($scope.PATIENT.DATEREG);
				$scope.PATIENT.DOB = global.Date($scope.PATIENT.DOB);


				angular.forEach($scope.PATIENT.MEDICALS,function(val,key){
					val.CHECKUPDATE = global.Date(val.CHECKUPDATE);
					val.APPOINTMENT = val.APPOINTMENT == 'Y' ? true : false;
				});

				angular.forEach($scope.PATIENT.OTHERS,function(val,key){
					val.CHECKUPDATE = global.Date(val.CHECKUPDATE);
				});
			}
			else{

				global.Toast(response.data.RESPONSE);
				$location.url('patients');
			}

			$scope.opt.isLoaded = true;

		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isLoaded = true;
		});
	}


	$scope.Remove_Patient = function(event,TOKEN,ID){

		$scope.Sheet_Close();

		if ( TOKEN == undefined) {
			TOKEN = $scope.PATIENT.TOKEN;
			ID = $scope.PATIENT.ID;
		}

		var confirm = $mdDialog.confirm()
		.title('Patient Info')
		.textContent('Are you sure to delete this patient?')
		.ariaLabel('delete patient')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			$http.post($scope.opt.submitRemovePatientUrl, { 
				TOKEN : TOKEN,
				ID : ID
			}, global.ajaxConfig).then( function(response){

				if ( response.data == 'RELOGIN' ){
					global.Relogin('POSSIVE');
					$scope.Sheet_Close();
				}
				else {

					if( response.data.err != '' ){
						global.Toast(response.data.err);
					}
					else{

						global.Toast(response.data.suc);
						$location.url('patients');
					}
				}
			},
			function(response){
				global.Relogin('POSSIVE');
				$scope.Sheet_Close();
			});

			
		}, function() {
			
		});
	}

	$scope.Sheet_Close = function(){
		$mdBottomSheet.hide();
	}




	$scope.Remove_Record = function(event,TOKEN,ID,key){

		var confirm = $mdDialog.confirm()
		.title('Delete Record')
		.textContent('Are you sure to delete this record?')
		.ariaLabel('delete record')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			$http.post($scope.opt.submitRemoveMRUrl, { 
				TOKEN : TOKEN,
				ID : ID
			}, global.ajaxConfig).then( function(response){

				if ( response.data == 'RELOGIN' ){
					global.Relogin('POSSIVE');
					$scope.Sheet_Close();
				}
				else {

					if( response.data.err != '' ){
						global.Toast(response.data.err);
					}
					else{

						global.Toast(response.data.suc);
						$scope.PATIENT.MEDICALS.splice(key,1);
					}
				}
			},
			function(response){
				global.Relogin('POSSIVE');
				$scope.Sheet_Close();
			});

			
		}, function() {
			
		});
	}



	$scope.Medical_Record_Report = function(ev,ID) {
 
		$mdDialog.show({
			template: `
			<md-dialog aria-label="Medical Record Report" class="dialog-view-image">
			<md-toolbar>
			<div class="md-toolbar-tools">
			<h2 class="md-title">`+ ID +` Medical Record Report</h2>
			<span flex></span>
			<md-button class="md-icon-button" ng-click="Dialog_Close()">
			<md-icon class="material-icons" aria-label="Close dialog">close</md-icon>
			</md-button>
			</div>
			</md-toolbar>
			<md-dialog-content>
			<iframe src="`+ ($scope.opt.medicalRecordReportUrl+ ID + '/medical-record') +`" scrolling="auto" ></iframe>
			</md-dialog-content>
			</md-dialog>
			`,
			controller: 'Extra',
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose: true
		})
		.then(function(answer) {
			
		}, function() {
			
		});
	};

	$scope.MR_Preview = function(ev,ID) {

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_preview_mr.html',
			locals:{
				PATIENTID: $routeParams.P1,
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



	$scope.Load_Patient();


	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.Load_Patient();
	});

	$scope.$on('$destroy', function() {
		dList();
	});



});

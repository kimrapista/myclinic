"use strict";
// doctor

app.controller('FormSMS',function ($scope,$http,$timeout,$filter, $location, $rootScope, $routeParams, $mdDialog, global){


	$scope.opt = {
		isLoaded : false,
		isSubmit : false,
		formUrl : global.baseUrl + 'sms/form-data/',
		submitUrl : global.baseUrl +'sms/submit-form',
		patientUrl : global.baseUrl + 'sms/patient-mobile-no',
		cancelUrl : '#!/sms',
		isSending: false
	} 
	

	$scope.Load_Form = function(){

		$http.get($scope.opt.formUrl, global.ajaxConfig) .then( function(response){	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else{

				$scope.form = [];
				$scope.form = response.data;
				$scope.form.SENDDATE =  global.Date($scope.form.SENDDATE);
				$scope.form.NOPATIENT = parseInt($scope.form.NOPATIENT);
				$scope.form.APPOINTMENT = $scope.form.APPOINTMENT == 'Y' ? true : false;
				$scope.form.NEWPATIENT = $scope.form.NEWPATIENT == 'Y' ? true : false;
				$scope.form.REVISITPATIENT = $scope.form.REVISITPATIENT == 'Y' ? true : false;
				$scope.form.POST = $scope.form.POST == 'Y' ? true : false;

				//$scope.ReadOnly = $scope.form.POST;

			}

			$scope.opt.isLoaded = true;

		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isLoaded = true;
		});
	}


	$scope.SMS_Post = function(){

		if( ! $scope.form.APPOINTMENT && ! $scope.form.NEWPATIENT && ! $scope.form.REVISITPATIENT ){
			$scope.form.POST = false;
		}
	}


	$scope.Submit_Form = function(){

		if( $scope.opt.isSubmit ) return;

		$scope.opt.isSubmit = true;


		$http.post($scope.opt.submitUrl, $scope.form, global.ajaxConfig).then( function(response){

			$scope.opt.isSubmit = false;

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM');
			}
			else{

				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {

					if( $scope.form.POST ){

						$scope.form.TOKEN = response.data.suc.TOKEN;
						$scope.form.ID = response.data.suc.ID;
						$scope.SMS_Patient();
					}
					else{

						global.Toast('SAVED');
						$location.url('/sms');	
					}
				}
			}
		},
		function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
		});
	}


	$scope.SMS_Patient = function(){

		$scope.opt.isSending = true;

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_sms_sending.html',
			clickOutsideToClose: false,
			escapeToClose: false,
			locals:{
				patientUrl: $scope.opt.patientUrl,
				opt : $scope.opt,
				form: $scope.form
			},
			controller: function($scope, $mdDialog, opt, form, global){

				
				$scope.currentProgress = 'Checking Patient Status';
				$scope.patients = [];
				
				$scope.progress = 0;

				$scope.Load_Patients_Mobile = function(){

					$http.post( opt.patientUrl, {
						APPOINTMENT : form.APPOINTMENT ? 'Y' : 'N',
						NEWPATIENT : form.NEWPATIENT ? 'Y' : 'N',
						REVISITPATIENT : form.REVISITPATIENT ? 'Y' : 'N'
					}
					,global.ajaxConfig).then( function(response){

						if( response.data == 'RELOGIN'){
							global.Relogin('FORM');
						}
						else{

							if( response.data.length > 0  ){

								$scope.currentProgress = 'Checking Patient Mobile No';

								angular.forEach( response.data, function(v,k){

									var exist = $filter('filter')( $scope.patients,{PATIENTID: v.PATIENTID})[0];

									if( ! exist ){

										$scope.patients.push({
											PATIENTID: v.PATIENTID, 
											CLIENT: 'CLINIC',
											TITLE: 'SMS BLAST ' + v.PATIENTID,
											HEADERID: form.ID,
											MOBILENO: v.MOBILENO,
											BODY: form.MESSAGE,
											DATETOPROCESS: form.SENDDATE,
											STATUS: 'QUEUE',
											PRIORITYLEVEL : 2
										});
									}
								});


								$scope.Queuing_Messages(0);
							}
							else{

								opt.isSending = false;
								$mdDialog.cancel();

								global.Toast('NO PATIENT MOBILE NUMBER FOUND.');
								$location.url('/sms');
							}
							
						}
					},
					function(response){
						$scope.Load_Patients_Mobile();
					});
				}



				$scope.Queuing_Messages = function(key){

					if( $scope.patients[key] != undefined ){

						$scope.progress = (key / $scope.patients.length) * 100;
						$scope.currentProgress = 'Queuing Message '+ $scope.progress +' %';


						var data = [];
						var previousKey = key;
						var limitData = $scope.patients.length  * .1 ;

						if( limitData < 1 )
							limitData = 1;

						angular.forEach( $scope.patients, function(v,k){ 

							if( previousKey <= k && k <= (previousKey + limitData) ){
								data.push(v);
								key += 1;
							}
							
						});

						

						
						var tempData = 'BATCH='+ JSON.stringify(data);

						$http.post( 'http://sms.solarestech.com/api2/insert_batch', tempData, 
							{ 'headers': {'Content-Type': 'application/x-www-form-urlencoded'}}
							).then( function(response){

								$scope.Queuing_Messages(key);
							},
							function(response){
								$scope.Queuing_Messages(previousKey);
							});

						}
						else{

							$scope.progress = 100;
							opt.isSending = false;

							setTimeout(function(){
								$mdDialog.cancel();
							},500);


							global.Toast('QUEUED COMPLETED');
							$location.url('/sms');
						}

					}


					$scope.Load_Patients_Mobile();
				}
			})
		.then(function(answer) {

		}, function() {
			

		});
	}



	if( $routeParams.P1 == 'new' ){

		$scope.opt.formUrl = global.baseUrl + 'sms/form-data/0';
		$rootScope.$emit("Title_Global", { TITLE : 'NEW SMS', BACKURL: '#!/sms' });
	}
	else{
		$scope.opt.formUrl = global.baseUrl + 'sms/form-data/' + $routeParams.P1;
		$rootScope.$emit("Title_Global", { TITLE : 'EDIT SMS', BACKURL: '#!/sms' });
	}

	$scope.Load_Form();



	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.Load_Form();
	});

	var dList1 = $rootScope.$on('RELOGIN_FORM', function (event, data) {
		$scope.Submit_Form();
	});


	$scope.$on('$destroy', function() {
		dList();
		dList1();
	});

});

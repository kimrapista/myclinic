'use strict';

app.config(function($mdThemingProvider) {
	$mdThemingProvider.theme('default')
	.primaryPalette('teal')
	.accentPalette('grey')
	.warnPalette('red');
 }); 

 
app.controller('OnlineAppoint', function($scope, $timeout, $mdDialog, global, OnlineAppointServices){
   
   global.baseUrl = baseUrl;
   $scope.homeUrl = global.baseUrl;
   $scope.vcUrl = global.baseUrl+'video-call';
   $scope.isLoaded = false;
   $scope.isClass = false;



   $scope.VC = function(){
      
      $mdDialog.show({
         templateUrl: 'views/modal_vc.html',
         locals:{
            TOKEN: OnlineAppointServices.Data().DOCTOR.TOKEN,
            USERID: OnlineAppointServices.Data().ID
         } , 
         clickOutsideToClose: false,
         escapeToClose: false,
         fullscreen: true,
         controller: function($scope,$mdDialog, $http, TOKEN, USERID, global){

               
            $scope.Init = function(){
                              
            }

            $scope.Close = function () {
               $mdDialog.cancel();
            }
         }
      }).then(function(answer) {

      }, function() {

      });
   }

   $scope.Data = function(){
      return OnlineAppointServices.Data();
   }
 

   $scope.Pre_Appoint = function(SCHEDULE){

      $mdDialog.show({
         templateUrl: 'views/modal_form_preappointment.html',
         clickOutsideToClose: false,
         fullscreen: true,
         escapeToClose: false,
         locals:{ SCHEDULE: SCHEDULE },
         controller: function($scope, $http, $mdDialog, $filter, $location, SCHEDULE ){

            $scope.isLoaded = false;
            $scope.SCHEDULE = SCHEDULE;

            $scope.FORM = {
               SCHEDULEID: SCHEDULE.ID,
               CLINICID: SCHEDULE.CLINICID,
               TOKEN: SCHEDULE.TOKEN,
               FIRSTNAME: '',
               MIDDLENAME: '',
               LASTNAME: '',
               DOB: null,
               CIVILSTATUS: null,
               SEX: 'MALE',
               STREETNO: '',
               CITY: '',
               PROVINCE: '',
               COMPLAINT: '',
               MOBILENO: '',
               AGREE: false,
               isSubmit: false  
            };

            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;

               $http.post( global.baseUrl +'landing/submit-pre-appointment', $scope.FORM , global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data.suc.TOTAL_ACKNOWLEDGED  ){
                     SCHEDULE.TOTAL_ACKNOWLEDGED = parseInt(response.data.suc.TOTAL_ACKNOWLEDGED);
                  }

                  if( response.data.err.length > 0 ){
                     global.Alert(response.data.err);
                  }
                  else{

                     global.Alert('The information you submitted still needs to be verified before the clinic can acknowledge your complaint. Please wait for an SMS confirmation code and click VERIFY CODE.','Successfully pre-appointed');
                     $mdDialog.hide();
                  }

               },
               function (err) {
                  global.Alert( err.statusText, 'Error ' + err.status);
               });
            }

            $scope.Init = function(){

               $scope.isLoaded = true;
            }

            $scope.Close = function(){
               $mdDialog.cancel();
            }

         }
      }).then(function(answer) {

      }, function(cancel) {

      });
   }

   $scope.Verify_Code = function(){

      $mdDialog.show({
         templateUrl: 'views/modal_form_verify_code.html',
         clickOutsideToClose: false,
         fullscreen: false,
         escapeToClose: false,
         locals:{ TOKEN: OnlineAppointServices.Data().DOCTOR.TOKEN },
         controller: function($scope, $http, $mdDialog, TOKEN ){

            $scope.isLoaded = false;

            $scope.FORM = {
               TOKEN: TOKEN,
               CODE: '',
               isSubmit: false  
            };

            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;

               $http.post( global.baseUrl +'landing/submit-verify-code', $scope.FORM , global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data.err.length > 0 ){
                     global.Alert(response.data.err);
                  }
                  else{

                     global.Alert('Your appointment schedule has been verified. The clinic will contact you anytime for further instructions and information. Thank you!','Verified Code');
                     $mdDialog.hide();
                  }

               },
               function (err) {
                  global.Alert( err.statusText, 'Error ' + err.status);
               });
            }

            $scope.Init = function(){

               $scope.isLoaded = true;
            }

            $scope.Close = function(){
               $mdDialog.cancel();
            }

         }
      }).then(function(answer) {

      }, function(cancel) {

      });
   }



   $scope.Resend_Code = function(){

      $mdDialog.show({
         templateUrl: 'views/modal_form_resend_code.html',
         clickOutsideToClose: false,
         fullscreen: false,
         escapeToClose: false,
         locals:{ TOKEN: OnlineAppointServices.Data().DOCTOR.TOKEN },
         controller: function($scope, $http, $mdDialog, TOKEN ){

            $scope.isLoaded = false;

            $scope.FORM = {
               TOKEN: TOKEN,
               MOBILENO: '',
               isSubmit: false  
            };

            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;

               $http.post( global.baseUrl +'landing/submit-resend-code', $scope.FORM , global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data.err.length > 0 ){
                     global.Alert(response.data.err);
                  }
                  else{

                     global.Alert('Done and please wait for the sms verification code. Thank you');
                     $mdDialog.hide();
                  }

               },
               function (err) {
                  global.Alert( err.statusText, 'Error ' + err.status);
               });
            }

            $scope.Init = function(){

               $scope.isLoaded = true;
            }

            $scope.Close = function(){
               $mdDialog.cancel();
            }

         }
      }).then(function(answer) {

      }, function(cancel) {

      });
   }

   $scope.Init = function(TOKEN){ 

      $scope.isClass = false;

      OnlineAppointServices.Load_Doctor({TOKEN: TOKEN}).then(function(data){

         if( data ){

            $scope.vcUrl = global.baseUrl + (OnlineAppointServices.Data().DOCTOR.LINK != '' ? OnlineAppointServices.Data().DOCTOR.LINK : OnlineAppointServices.Data().DOCTOR.ID) + '/video-call';

            OnlineAppointServices.Load_Schedules({TOKEN: OnlineAppointServices.Data().DOCTOR.TOKEN}).then(function(data1){

            });           
            
         }
         else{

            global.Alert('Sorry invalid data and you are redirect back to home page', '', global.baseUrl);
         }
         
         $scope.isLoaded = true;
         $scope.isClass = true;
      });
      
   }


});
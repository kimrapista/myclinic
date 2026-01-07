'use strict';

app.config(function($mdThemingProvider) {
	$mdThemingProvider.theme('default')
	.primaryPalette('teal')
	.accentPalette('grey')
	.warnPalette('red');
 }); 

 
app.controller('Jitsi', function($scope, $http, $timeout, $mdDialog, global){
   
   global.baseUrl = baseUrl;
   $scope.backUrl = '';

   var api;
   $scope.DOCTOR = [];

   $scope.FORM = {
      LASTNAME: '',
      DOB: null,
      EMAIL: '',
      isSubmit: false,
      ROOMNAME: '',
      isStart: false
   }


   $scope.Submit_Check_Patient = function(){

      if( $scope.FORM.isSubmit ) return;

      $scope.FORM.isSubmit = true;

      $http.post( global.baseUrl + 'landing/submit-jitsi-check-patient',{
         DOCTORID: $scope.DOCTOR.ID,
         LASTNAME: $scope.FORM.LASTNAME,
         DOB: $scope.FORM.DOB
      }, global.ajaxConfig).then(function(response){

         $scope.FORM.isSubmit = false;

         if( response.data.err != '' ){
            global.Alert(response.data.err);
         }
         else{
            $scope.FORM.isStart = true;
            $scope.FORM.ROOMNAME = $scope.DOCTOR.ID + response.data.suc.PATIENTID;

            $scope.Start_VC();
         }

      }, function(err){

      });
      
   }

   $scope.Start_VC = function(){


      var domain = 'meet.jit.si';
      var options = {
         roomName: $scope.FORM.ROOMNAME,
         width: window.innerWidth,
         height: window.innerHeight,
         interfaceConfigOverwrite: {
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_PROMOTIONAL_CLOSE_PAGE: false,
            GENERATE_ROOMNAMES_ON_WELCOME_PAGE: false,
            DISPLAY_WELCOME_PAGE_CONTENT: false
         },
         parentNode: document.querySelector('#conf_div')
      };

      api = new JitsiMeetExternalAPI(domain, options);

      api.executeCommand('displayName', $scope.FORM.LASTNAME);
      api.executeCommand('avatarUrl', global.baseUrl + 'assets/css/images/patient_default2.png');
      
   };


   $scope.Init = function(){

      // DOCTOR JS from page/script
      $scope.DOCTOR = DOCTORJS;

      if( $scope.DOCTOR.AVATAR == '' || $scope.DOCTOR.AVATAR == null){
			$scope.DOCTOR.AVATAR = global.baseUrl + 'assets/css/images/patient_default2.png';
		}
		else{
			$scope.DOCTOR.AVATAR = global.baseUrl + $scope.DOCTOR.AVATAR;
      }

      $scope.backUrl = global.baseUrl + ($scope.DOCTOR.LINK == null || $scope.DOCTOR.LINK == '' ? $scope.DOCTOR.ID : $scope.DOCTOR.LINK );

   }


});
'use strict';


app.controller('ClinicForm', function($scope, $http, $mdDialog, $location, ClinicServices, MeServices, Preview, global){ 


   global.page.title = 'My Clinic';
   global.page.pageBackUrl = '#!/myclinic/';

   $scope.cancel = '#!/myclinic/';

   $scope.isLoaded = false;

   $scope.FORM = [];


   $scope.Submit_Form = function(){

      if( $scope.FORM.isSubmit ) return;

      $scope.FORM.isSubmit = true;

      $http.post( global.baseUrl +'clinics/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

         $scope.FORM.isSubmit = false;

         if( $.trim(response.data) == 'RELOGIN'){
            global.Alert('Submit failed. Please try again');
         }
         else{
            if( $.trim(response.data.err) != '' ){
               global.Alert(response.data.err);
            }
            else{

               ClinicServices.Update($scope.FORM);
               global.Toast('SAVED');
               // $location.url('/myclinic');
            }
         }
      }, 
      function (err) { 
         global.Alert( err.statusText, 'Error ' + err.status);
         $scope.close();
      });
   }


   $scope.Preview_Report = function(report){
      if( report == 'REFERRAL' ){
         Preview.Report(global.baseUrl + 'medicals/report/0/referral-letter', 'Referral Report');
      }
      else{
         Preview.Report(global.baseUrl + 'medicals/report/0/clearance-letter', 'Clearance Report');
      }
   }


   $scope.Init = function(){

      if( MeServices.Data().isAdmin ){
        
         ClinicServices.Form().then(function(data){
            $scope.FORM = angular.copy(data);
            $scope.isLoaded = true;
         });
        
      }
      else{
         $location.url('/error404');
      }

      
   }




});
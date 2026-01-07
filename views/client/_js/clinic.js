'use strict';

app.controller('Clinic', function($scope, $http, $mdDialog, $location, ClinicServices, SubClinicServices, MeServices, Preview, global){ 


   global.page.title = 'My Clinic';
   global.page.pageBackUrl = '';

   $scope.isLoaded = false;


   $scope.Add_Edit = function (ID) {

      $mdDialog.show({
         templateUrl: 'views/client/modal_form_subclinic.html',
         clickOutsideToClose: true,
         fullscreen: false,
         escapeToClose: true,
         locals:{
            ID: ID
         },
         controller: function($scope, $filter, $mdDialog, $q, ID, global, MeServices, SubClinicServices, HospitalsServices){

            $scope.isLoaded = false;

            $scope.FORM = {
               TOKEN: MeServices.Data().TOKEN,
               ID: ID,
               CLINICID: MeServices.Data().CLINICID,
               HOSPITALID: null,
               NAME: '',
               LOCATION: '',
               ISSIG: false,
               isSubmit: false
            };

            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;

               $http.post( global.baseUrl +'clinics/subclinic/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data.err != '' ){
                     global.Alert(response.data.err);
                  }
                  else{

                     $scope.FORM.ID = parseInt(response.data.suc.ID);

                     var tempHospName = $filter('filter')( HospitalsServices.Data(), {ID: $scope.FORM.HOSPITALID}, true)[0];

                     if( tempHospName ){
                        $scope.FORM.HOSPITALNAME = tempHospName.NAME;
                     }
                     else{
                        $scope.FORM.HOSPITALNAME = '';
                     }

                     SubClinicServices.Update($scope.FORM);             

                     global.Toast('SAVED');
                     $mdDialog.hide();

                  }
               }, 
               function (err) { 
                  global.Alert( err.statusText, 'Error ' + err.status);
                  $scope.close();
               });
            }

            $scope.close = function () {
               $mdDialog.cancel();
            };

            $scope.Hospitals = function(){
               return HospitalsServices.Data();
            }

            $scope.HospitalStatus = function(){
               return HospitalsServices.Status();
            }


            $scope.Init = function(){

               $q.all([
                  HospitalsServices.Reload() 
               ]).then(function(result){

                  if( ID > 0 ){
                     SubClinicServices.Form(ID).then(function(data){
                        if( data ){
                           $scope.FORM = data;
                           $scope.FORM.ISSIG = $scope.FORM.ISSIG == 'Y' ? true : false;
                           $scope.isLoaded = true;
                        }
                     });
                  }
                  else{
                     $scope.isLoaded = true;
                  }     
               });
            }

         }           
      }).then(function(answer) {

      }, function(cancel) {

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


   $scope.Data = function(){
      return ClinicServices.Data();
   }

   $scope.Detail = function(){
      return SubClinicServices.Data();
   }

   $scope.Init = function(){

      if( MeServices.Data().isAdmin ){
        
         ClinicServices.Reload().then(function(data){
            $scope.isLoaded = true;
         });
   
         SubClinicServices.Reload().then(function(data){ });        
      }
      else{
         $location.url('/error404');
      }
   
   }

});
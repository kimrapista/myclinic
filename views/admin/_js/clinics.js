'use strict';

app.controller('Clinics', function($scope, $http, $mdDialog, $location, global, MeServices, ClinicsServices){ 

   $scope.Me = function(){
      return MeServices.Data();
  }

   global.page.title = 'Clinics';
   global.page.pageBackUrl = '';

   $scope.isLoaded = false;


   $scope.search = {
      name: '',
      limit : 10,
      from : 0,
      isSubmit: false,
      viewMore: true
   }
 

   $scope.Add_Edit = function(ID){

      $mdDialog.show({
         templateUrl: 'views/admin/modal_form_clinics.html',
         clickOutsideToClose: false,
         fullscreen: true,
         escapeToClose: false,
         locals:{
            ID: ID
         }, 
         controller: function($scope, $filter, $mdDialog, ID, global, MeServices, ClinicsServices ){

            $scope.isLoaded = false;

            $scope.FORM = {
               TOKEN: MeServices.Data().TOKEN,
               ID: ID,
               CLINICNAME: '',
               CLINICSUBNAME: '',
               CLINICSUBNAME1: '',
               TIN: '',
               ADDRESS: '',
               CONTACTNO: '',
               MOBILENO: '',
               DOCTORNAME: '',
               PTR: '',
               LICENSENO: '',
               S2NO: '',
               EMAIL: '',
               
               SALES: false,
               BLAST: false,

               PIRANI: false,
               OPTICAL: false,
               OPHTHALMOLOGIST: false,
               
               
               SETUP_CLINIC_ROMERO: false,
               isSubmit: false
            };


            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;

               $http.post( global.baseUrl +'clinics/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data == 'RELOGIN'){
                     global.Alert('Submit failed. Please try again');
                     $scope.close();
                  }
                  else{

                     if( response.data.err != '' ){
                        global.Alert(response.data.err);
                     }
                     else{

                        $scope.FORM.ID = parseInt(response.data.suc.ID);

                        ClinicsServices.Update($scope.FORM);             

                        global.Toast('SAVED');
                        $mdDialog.hide();
                     }
                  }
               }, 
               function (err) { 
                  global.Alert( err.statusText, 'Error ' + err.status);
                  $scope.close();
               });
            }


            $scope.Init = function(){
               
               if( $scope.FORM.ID > 0 ){

                  ClinicsServices.Form($scope.FORM.ID).then(function(data){
                     
                     $scope.FORM = data;
                     $scope.FORM.isSubmit = false;
                     $scope.isLoaded = true;
                  })
                  
               }
               else{
                  $scope.isLoaded = true;
               }
            }

            $scope.Close = function () {
               $mdDialog.cancel();
            };

         }           
      }).then(function(answer) {

      }, function(cancel) {

      });
   }



   $scope.Report = function(CLINIC){

      $mdDialog.show({
         templateUrl: 'views/admin/modal_clinics_report.html',
         clickOutsideToClose: false,
         fullscreen: true,
         escapeToClose: false,
         locals:{
            CLINIC: CLINIC
         }, 
         controller: function($scope, $filter, $timeout, $mdDialog, CLINIC, global, ClinicsServices ){

            $scope.isLoaded = false;
            $scope.CLINIC = CLINIC;

            $scope.queues = [];

            
            $scope.CLINIC.TITLE = 'HEADER';
            $scope.RTypes = [
               {value: 'HEADER', label: 'Header'},
               {value: 'PRESCRIPTION', label: 'Prescription'},
               {value: 'MEDICAL CERTIFICATE', label: 'Medical Certificate'}
            ];
            
            $scope.CompTypes = [
               {value: 'TEXT', label: 'Text'},
               {value: 'TEXTAREA', label: 'Textarea'}
            ];

            $scope.Aligns = [
               {value: 'L', label: 'Left'},
               {value: 'R', label: 'Right'},
               {value: 'C', label: 'Center'},
               {value: 'J', label: 'Justify'}
            ];

            $scope.Fonts = [
               {value: 'arial', label: 'Arial'},
               {value: 'calibri', label: 'Calibri'},
               {value: 'courier', label: 'Courier'},
               {value: 'helvetica', label: 'Helvetica'},
               {value: 'times', label: 'times'}
            ];
            
            $scope.reportUrl = ''; 
            $scope.FORM = [];

            $scope.Add_Detail = function(){

               $scope.FORM.DETAIL.push({
                  ID: 0,
                  HEADERID: $scope.FORM.ID,
                  COMPONENTTYPE: 'TEXT',
                  DESCRIPTION: '',
                  COORDX: 0,
                  COORDY: 0,
                  WIDTH: 0,
                  HEIGHT: 0,
                  ALIGN: 'L',
                  FONT: 'calibri',
                  FONTSIZE: 10,
                  FONTBOLD: false,
                  FILLCOLOR1: 0,
                  FILLCOLOR2: 0,
                  FILLCOLOR3: 0,
                  TEXTCOLOR1: 0,
                  TEXTCOLOR2: 0,
                  TEXTCOLOR3: 0,
                  BORDERT: false,
                  BORDERR: false,
                  BORDERB: false,
                  BORDERL: false,                  
                  BORDERCOLOR1: 0,
                  BORDERCOLOR2: 0,
                  BORDERCOLOR3: 0,
                  CREATEDTIME: new Date(),
                  CANCELLED: false,
                  TEMPID : Date.now()
               })
            }


            $scope.Submit_Form = function(QueueKey){

               if( $scope.queues[QueueKey].done ) return;
               
               $scope.queues[QueueKey].isSubmit = true;

               $http.post( global.baseUrl +'clinics/submit-report-forms', $scope.FORM , global.ajaxConfig ).then(function (response) {


                  if( response.data.DETAIL ){
                     // update the ID 
                     angular.forEach( response.data.DETAIL, function(v,k){
                        angular.forEach( $scope.FORM.DETAIL, function(v1,k1){
                           if( v1.ID == 0 && v1.TEMPID == v.TEMPID ){
                              $scope.FORM.DETAIL[k1].ID = parseInt(v.ID);
                           }
                        });
                     });
                  }

                  
                  $http.post( global.baseUrl +'medicals/sample-dynamic-report', {
                     CLINICID: $scope.FORM.CLINICID,
                     TITLE: $scope.FORM.TITLE
                  }, global.ajaxConfig ).then(function (response) {
                     
                     $scope.Refresh_Report_View();                     
                  }, 
                  function (err) { 
                     global.Alert( err.statusText, 'Error ' + err.status);
                  });

                  $scope.queues[QueueKey].done = true;
                  $scope.queues[QueueKey].isSubmit = false;
               }, 
               function (err) { 
                  global.Alert( err.statusText, 'Error ' + err.status);
                  $scope.Submit_Form(QueueKey);
               });
            }


            $scope.Add_Queue = function(){
               $scope.queues.push({
                  done: false,
                  isSubmit: false
               });

               $scope.Check_Queue();
            }

            $scope.Check_Queue = function(){
               
               var otherQueueSubmitting = false;
               
               for (var i = 0; i < $scope.queues.length; i++) {
                  
                  if( $scope.queues[i].isSubmit ){
                     otherQueueSubmitting = true;
                     i = $scope.queues.length + 100;
                  }
                  else if( $scope.queues[i].done == false ){
                     $scope.Submit_Form(i);
                     i = $scope.queues.length + 100;
                  }
               }

               // loop again because some still submitting
               if( otherQueueSubmitting )
               $scope.Check_Queue();
            }



            $scope.Refresh_Report_View = function(){

               $scope.reportUrl = '';
                     
               $timeout(function(){
                  $scope.reportUrl = global.baseUrl + 'temp_files_pdf/sample/sample_'+$scope.FORM.CLINICID+'.pdf';
               },10);
            }

            $scope.Init = function(){

               $scope.isLoaded = false;
               ClinicsServices.Load_Report(CLINIC.ID, CLINIC.TITLE).then(function(data){

                  $scope.FORM = data;
                  $scope.isLoaded = true;

                  //$scope.Refresh_Report_View();       
               })               
            }

            $scope.Close = function () {
               $mdDialog.cancel();
            };

         }           
      }).then(function(answer) {

      }, function(cancel) {

      });
   }


   $scope.Data = function(){
      return ClinicsServices.Data();
   }


   $scope.Init = function(){

      ClinicsServices.Reload().then(function(data){
         $scope.isLoaded = true;
      });      
   }

});
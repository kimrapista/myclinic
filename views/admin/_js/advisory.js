'use strict';

app.controller('Advisory', function($scope, $http, $mdDialog, global, AdvisoryServices){ 


   global.page.title = 'Advisory';
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
         templateUrl: 'views/admin/modal_form_advisory.html',
         clickOutsideToClose: false,
         fullscreen: true,
         escapeToClose: false,
         locals:{
            ID: ID
         }, 
         controller: function($scope, $filter, $mdDialog, ID, global, MeServices, AdvisoryServices ){

            $scope.isLoaded = false;

            $scope.FORM = {
               TOKEN: MeServices.Data().TOKEN,
               ID: ID,
               CLINICID: null,
               TITLE: '',
               BODY: '',
               POST: false,
               POSTDATE: null,
               isSubmit: false
            };


            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;

               $http.post( global.baseUrl +'advisory/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data.err != '' ){
                     global.Alert(response.data.err);
                  }
                  else{

                     $scope.FORM.ID = parseInt(response.data.suc.ID);

                     AdvisoryServices.Update($scope.FORM);             

                     global.Toast('SAVED');
                     $mdDialog.hide();
                  }
               }, 
               function (err) { 
                  global.Alert( err.statusText, 'Error ' + err.status);
                  $scope.close();
               });
            }


            $scope.Toggle_Post = function(){
               if( $scope.FORM.POST ){
                  $scope.FORM.POSTDATE = new Date();
               }
               else{
                  $scope.FORM.POSTDATE = null;
               }
            }

            $scope.Init = function(){
               
               if( $scope.FORM.ID > 0 ){

                  AdvisoryServices.Form($scope.FORM.ID).then(function(data){
                     
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


   $scope.Data = function(){
      return AdvisoryServices.Data();
   }


   $scope.Init = function(){

      AdvisoryServices.Reload().then(function(data){
         $scope.isLoaded = true;
      });      
   }

});
'use strict';


app.controller('Dashboard', function($scope, DashboardServices, MeServices, global){ 

   $scope.Me = function(){
      return MeServices.Data();
   }
 
   global.page.title = 'Dashboard';
   global.page.pageBackUrl = '';

   $scope.isLoaded = false;

   $scope.search = {
      dateFrom: new Date(),
      dateTo: new Date(),
      isSubmit: false
   }

  
   $scope.Data = function(){
      return DashboardServices.Data();
   }
   
   $scope.Init = function(){

      $scope.search.isSubmit = true;

      DashboardServices.Load_Clinics({
         DATEFROM: $scope.search.dateFrom,
         DATETO: $scope.search.dateTo
      }).then(function(data){
         
         $scope.search.isSubmit = false;
      });

      DashboardServices.Load_Doctors({
         DATEFROM: $scope.search.dateFrom,
         DATETO: $scope.search.dateTo
      }).then(function(data){
         
         $scope.search.isSubmit = false;
      });

 
      $scope.isLoaded = true;
   }

});
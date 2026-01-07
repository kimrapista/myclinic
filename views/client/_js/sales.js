'use strict';


app.controller('Sales', function($scope, $mdDialog, $location, SalesServices, MeServices, Preview, global){ 

   $scope.Me = function(){
      return MeServices.Data();
   }

   global.page.title = 'Sales';
   global.page.pageBackUrl = '';

   $scope.isLoaded = false;

   $scope.search = {
      name: '',
      dateFrom: new Date(),
      dateTo: new Date(),
      isSubmit: false,
      isClinics: false,
      isHMO : false,
      isServices: false,
      isPatients: false,
      patFrom: 0,
      patLimit: 40,
      patViewMore: true
   }
 
 
   $scope.Data = function(){
      return SalesServices.Data();
   }


   $scope.Submit_Search = function(){

      if( $scope.search.isSubmit || $scope.search.isClinics || $scope.search.isHMO || $scope.search.isServices || $scope.search.isPatients ) return false;

      $scope.search.isSubmit = true;
      $scope.search.isClinics = true;
      $scope.search.isHMO = true;
      $scope.search.isServices = true;
      $scope.search.isPatients = true;

      $scope.search.patFrom = 0;

      var OPTION = {
         SEARCH: $scope.search.name, 
         DATEFROM: $scope.search.dateFrom,
         DATETO: $scope.search.dateTo,
         FROM: $scope.search.patFrom,
         TO: $scope.search.patLimit
      };

      SalesServices.Load_Clinics(OPTION).then(function(data){
         $scope.search.isClinics = false;
         $scope.search.isSubmit = false;
      });

      
      SalesServices.Load_HMO(OPTION).then(function(data){
         $scope.search.isHMO = false;
      });


      SalesServices.Load_Services(OPTION).then(function(data){
         $scope.search.isServices = false;
      });

      SalesServices.Load_PhilHealth(OPTION).then(function(data){
         
      });

      SalesServices.Load_Patients_No_Charges(OPTION).then(function(data){
         
      });

      SalesServices.Load_Patients(OPTION).then(function(data){

         if( data ){
            $scope.search.patViewMore = true;

            if( $scope.Data().PATIENTS.length < $scope.search.patLimit )
               $scope.search.patViewMore = false;
         }
         else{
            $scope.search.patViewMore = false;
         }

         $scope.search.isPatients = false;
      });

   }


   $scope.More_Patients = function(){

      if( $scope.search.isPatients) return false;

      $scope.search.isPatients = true;
      $scope.search.patFrom += $scope.search.patLimit;

      var OPTION = {
         SEARCH: $scope.search.name, 
         DATEFROM: $scope.search.dateFrom,
         DATETO: $scope.search.dateTo,
         FROM: $scope.search.patFrom,
         TO: $scope.search.patLimit
      };

      SalesServices.Load_Patients(OPTION).then(function(data){
         if( data ){
            $scope.search.patViewMore = true;
         }
         else{
            $scope.search.patViewMore = false;
         }

         $scope.search.isPatients = false;
      });
   }

   

 
   $scope.MR_Preview = function(ID){

      Preview.Medical_Record(ID);
   }
  
   $scope.Report_View = function( REPORTTYPE ){

      var OPTION = {
         SEARCH: $scope.search.name, 
         DATEFROM: $scope.search.dateFrom,
         DATETO: $scope.search.dateTo,
         FROM: $scope.search.patFrom,
         TO: $scope.search.patLimit
      };

      if( REPORTTYPE == 'SUMMARY' ){
         Preview.Report(global.baseUrl + 'sales/sales-summary-report', 'Sales Summary Report', OPTION);
      }
      else if( REPORTTYPE == 'DETAIL' ){
         Preview.Report(global.baseUrl + 'sales/sales-detail-report', 'Sales Detail Report', OPTION);
      }
      else if( REPORTTYPE == 'DETAIL-PHILHEALTH' ){
         Preview.Report(global.baseUrl + 'sales/sales-detail-philhealth-report', 'PhilHealth Claims Report', OPTION);
      }
      
   }

   $scope.Report_HMO_View = function(REPORTTYPE, hmoID, paidType ){
      
      var OPTION = {
         HMOID: hmoID, 
         DATEFROM: $scope.search.dateFrom,
         DATETO: $scope.search.dateTo,
         PAIDTYPE: paidType
      };
      
      Preview.Report(global.baseUrl + 'sales/sales-hmo-report', 'HMO Report', OPTION);
   }

	
   $scope.Report_HMO_View1 = function (REPORTTYPE, hmoID, paidType) {
			var OPTION = {
				HMOID: hmoID,
				DATEFROM: $scope.search.dateFrom,
				DATETO: $scope.search.dateTo,
				PAIDTYPE: paidType,
			
			};

			Preview.Report(
				global.baseUrl + "sales/sales-hmo-report1",
				"HMO Report",
				OPTION
			);
		};


   $scope.Init = function(){

      if( MeServices.Data().SALES ){
         $scope.Submit_Search();
         $scope.isLoaded = true;
      }
      else{
         
         $location.url('/error404');
      }
   }

});

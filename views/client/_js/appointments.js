'use strict';


app.controller('Appointments', function($scope, $mdDialog, $location, $timeout, AppointmentsServices, global, Preview){ 


    global.page.title = 'Appointments';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;

    $scope.search = {
        name: '',
        dateFrom: new Date(),
        dateTo: new Date(),
        limit : 20,
        from : 0,
        isSubmit: false,
        viewMore: true
    }

 
    $scope.Data = function(){
        return AppointmentsServices.Data();
    }


    $scope.Submit_Search = function(){

        $scope.search.isSubmit = true;
        $scope.search.viewMore = false;
        $scope.search.from = 0;

        AppointmentsServices.Load({
            SEARCH: $scope.search.name, 
            DATEFROM: $scope.search.dateFrom,
            DATETO: $scope.search.dateTo,
            FROM: $scope.search.from, 
            TO: $scope.search.limit
        }).then(function(data){

            if( AppointmentsServices.Data().length >= $scope.search.limit )
                $scope.search.viewMore = true;

            $scope.search.isSubmit = false;
        });
    }


    $scope.Submit_Search_More = function(){

        if( $scope.search.isSubmit )
            return false;
        
        $scope.search.isSubmit = true;
        $scope.search.from += $scope.search.limit;

        AppointmentsServices.Load({
            SEARCH: $scope.search.name, 
            DATEFROM: $scope.search.dateFrom,
            DATETO: $scope.search.dateTo,
            FROM: $scope.search.from, 
            TO: $scope.search.limit
        }).then(function(data){
            
            if( ! data ){
                $scope.search.viewMore = false;

                if( $scope.search.from > 0)
                    $scope.search.from -= $scope.search.limit;
            }
            
           
            $timeout(function(){
                $scope.search.isSubmit = false;
            },200);
        });
    }


    $scope.MR_Laboratory = function(ID){

        Preview.Laboratory(ID);
    }


    $scope.MR_Preview = function(ID){

        Preview.Medical_Record(ID);
    }

    $scope.Patient_Record_Link = function(PATIENTID){

        $location.url('/patient/'+ PATIENTID +'/record');
    }


    $scope.Report_View = function(){

        var url = global.baseUrl + 'appointments/appointment-report';
        var title = 'Appointment Schedule';
  
        var OPTION = {
           SEARCH: $scope.search.name, 
           DATEFROM: $scope.search.dateFrom,
           DATETO: $scope.search.dateTo
        };
    
        $mdDialog.show({
            templateUrl: 'views/modal_pdf.html',
            locals:{
                url: url,
                title: title,
                OPTION: OPTION
            },  
            clickOutsideToClose: true,
            fullscreen: true,
            controller: function($scope,$mdDialog, $http, global, PDF, url, title, OPTION){
  
                $scope.PDF = function(){
                    return PDF;
                } 
  
                PDF.Title = title;
           
                $http.post( url, OPTION, global.ajaxConfig) .then( function(response) {
  
                    PDF.Init('pdf_wrapper', response.data ).then(function(data){
  
                    });
                }, 
                function(err){ 
                    global.Alert( err.statusText, 'Error ' + err.status);
                });
  
  
                $scope.close = function () {
                    $mdDialog.cancel();
                };
            }
        })
        .then(function(answer) {
  
        }, function() {
  
        });
    }

    
   
    $scope.Init = function(){

        $scope.Submit_Search();
        $scope.isLoaded = true;
    }

});
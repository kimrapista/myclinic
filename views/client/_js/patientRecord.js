'use strict';


app.controller('PatientRecord', function($scope, $mdDialog, $routeParams, $location, $timeout, $rootScope, PatientRecordServices, MeServices, global, Preview){ 

    $scope.Me = function(){
        return MeServices.Data();
    }
    
    global.page.title = 'Patient Record';
    global.page.pageBackUrl = '#!/patients';

    $scope.isLoaded = false;
    $scope.isMRLoaded = false;
    $scope.isPatDeleting = false;

    
    $scope.search = {
        limit: 5,
        from: 0,
        isSubmit : false,
        viewMore: true
    }; 


    $scope.Add_MR_Link = function(){

        if( MeServices.Data().POSITION == 'BRANCH ASSISTANT' && ! MeServices.Data().AUTHORIZATION ){
            global.Alert("Sorry your not authorized to create medical record.");
            return false;
        }

        $location.url('/patient/'+ $scope.Patient().ID +'/0/medical-record');
    }

    $scope.Edit_MR_Link = function(MRID){

        if( MeServices.Data().POSITION == 'BRANCH ASSISTANT' && ! MeServices.Data().EDITMR ){
            global.Alert("Sorry you are not allowed to edit the record.");
            return false;
        }

        $location.url('/patient/'+ $scope.Patient().ID +'/'+ MRID +'/medical-record');
    }

    $scope.Pirani_Report = function(PID){
        
        $mdDialog.show({
            templateUrl: 'views/modal_pdf.html',
            locals:{
                url: global.baseUrl+ 'medicals/report/' + PID +'/pirani-report',
                title: 'PIRANI REPORT'
            }, 
            clickOutsideToClose: true,
            fullscreen: true,
            controller: function($scope,$mdDialog, $http, global, PDF, url, title){

                $scope.PDF = function(){
                    return PDF;
                } 

                PDF.Title = title;
           
                $http.get( url, global.ajaxConfig) .then( function(response) {

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

    $scope.MR_Report = function(MRID){

        $mdDialog.show({
            templateUrl: 'views/modal_pdf.html',
            locals:{
                url: global.baseUrl+ 'medicals/report/' + MRID +'/medical-record',
                title: 'MEDICAL RECORD REPORT'
            }, 
            clickOutsideToClose: true,
            fullscreen: true,
            controller: function($scope,$mdDialog, $http, global, PDF, url, title){

                $scope.PDF = function(){
                    return PDF;
                } 

                PDF.Title = title;
           
                $http.get( url, global.ajaxConfig) .then( function(response) {

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



    $scope.MR_Laboratory = function(ID){
        Preview.Laboratory(ID);
    }


    $scope.MR_Preview = function(ID){
        Preview.Medical_Record(ID);
    }
 

    $scope.Patient = function(){
        return PatientRecordServices.Data_Patient(); 
    }


    $scope.Remove_Patient = function(){

        var confirm = $mdDialog.confirm()
        .title('Patient Info')
        .textContent('Are you sure to delete this patient?')
        .ariaLabel('delete patient')
        .targetEvent(event)
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function() {

            $scope.isPatDeleting = true;
            
            PatientRecordServices.Remove_Patient({TOKEN: $scope.Patient().TOKEN, ID: $scope.Patient().ID }).then(function(data){
                $scope.isPatDeleting = false;
            });

        }, function() {

        });
    }

    $scope.Remove_MR = function(DETAIL){

        var confirm = $mdDialog.confirm()
        .title('Delete Record')
        .textContent('Are you sure to delete this record?')
        .ariaLabel('delete record')
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function() {

            DETAIL.isCancelling = true;
            
            PatientRecordServices.Remove_MR({TOKEN: $scope.Patient().TOKEN, ID: DETAIL.ID }).then(function(data){
                DETAIL.isCancelling = false;

                if( data ){

                   $scope.Refresh_MR();
               }
           });

        }, function() {


        });
    }


    $scope.MR = function(){
        return PatientRecordServices.Data_MR();
    }


    $scope.Refresh_MR = function(){

        $scope.isMRLoaded = false;
        $scope.search.viewMore = false;
        $scope.search.from = 0;

        PatientRecordServices.Load_MR({PATIENTID: $routeParams.P1, FROM: $scope.search.from, TO: $scope.search.limit}).then(function(data){

            if( PatientRecordServices.Data_MR().length >= $scope.search.limit )
                $scope.search.viewMore = true;

            $scope.isMRLoaded = true;
        });
    }

    $scope.More_MR = function(){

        if( $scope.search.isSubmit )
            return false;

        $scope.search.isSubmit = true;

        $scope.search.from += $scope.search.limit;

        PatientRecordServices.Load_MR({PATIENTID: $routeParams.P1, FROM: $scope.search.from, TO: $scope.search.limit}).then(function(data){
            
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
 
    

    $scope.Init = function(){

        $scope.isLoaded = false;

        PatientRecordServices.Load_Patient($routeParams.P1).then(function(data){
            $scope.isLoaded = true;

            $scope.Refresh_MR();
        });
    }


//     var destroy1 = $rootScope.$on('CONTENT_SCROLL', function (event, data) {
// 		if( data.scrollTop > data.scrollTopMax - 50 && $scope.isLoaded && $scope.search.viewMore && ! $scope.search.isSubmit ){
//             $scope.More_MR();
//         }
// 	}); 

//    $scope.$on('$destroy', function() {
//         destroy1();
// 	});

});
'use strict';


app.controller('PatientForm', function($scope, $http, $routeParams, $location, PatientFormServices, global){ 


    global.page.title = $routeParams.P1 > 0 ? 'Edit Patient' : 'New Patient';
    global.page.pageBackUrl = $routeParams.P1 > 0 ? '#!/patient/'+ $routeParams.P1 +'/record' : '#!/patients';

    $scope.isLoaded = false;
    $scope.cancel = $routeParams.P1 > 0 ? '#!/patient/'+ $routeParams.P1 +'/record' : '#!/patients';

    $scope.FORM = [];


    $scope.Submit_Form = function(){

        if( $scope.FORM.isSubmit ) return;

        $scope.FORM.isSubmit = true;

        $http.post( global.baseUrl +'patients/submit-patient', $scope.FORM , global.ajaxConfig ).then(function (response) {

            $scope.FORM.isSubmit = false;

            if( $.trim(response.data) == 'RELOGIN'){
                global.Alert('')
            }
            else{

                if( $.trim(response.data.err) != '' ){
                    global.Alert(response.data.err);
                }
                else{

                    if( $routeParams.P1 > 0  ){
                        $location.url('/patient/'+ $routeParams.P1 +'/record');
                    }
                    else{
                        $location.url('/patient/'+ response.data.suc.ID +'/record');
                    }
                    
                    global.Toast('SAVED');
                }
            }
        }, 
        function (err) { 
            global.Alert( err.statusText, 'Error ' + err.status);
        });
    }

    $scope.Init = function(){

        PatientFormServices.Form($routeParams.P1 > 0 ? $routeParams.P1 : 0).then(function(data){

            $scope.FORM = data;
            $scope.isLoaded = true;
        });
    }




});
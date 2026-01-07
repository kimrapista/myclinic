'use strict';


app.controller('Procedures', function($scope, $http, $timeout, $filter, $mdDialog, ProceduresServices,  global){ 


    global.page.title = 'Procedures';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;


 
    $scope.Add_Edit = function (ID) {

        $mdDialog.show({
            templateUrl: 'views/client/modal_form_procedures.html',
            clickOutsideToClose: true,
            fullscreen: true,
            escapeToClose: true,
            locals:{
                ID: ID
            },
            controller: function($scope, $mdDialog, $filter, $timeout, $q, ProceduresServices, global, ID){

                $scope.isLoaded = false;

                $scope.FORM = [];

                $scope.Submit_Form = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'settings/procedures/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

                        $scope.FORM.isSubmit = false;

                        if( $.trim(response.data) == 'RELOGIN'){
                            global.Alert('Submit failed. Please try again');
                            $scope.close();
                        }
                        else{

                            if( $.trim(response.data.err) != '' ){
                                global.Alert(response.data.err);
                            }
                            else{

                                $scope.FORM.ID = parseInt(response.data.suc.ID);
                                ProceduresServices.Update($scope.FORM);             

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

                $scope.close = function () {
                    $mdDialog.cancel();
                };


                $scope.Init = function(){

                    ProceduresServices.Form(ID).then(function(data){
                        if( data ){
                            $scope.FORM = data;
                            $scope.isLoaded = true;
                        }
                    });
                }



            }           
        }).then(function(answer) {

        }, function(cancel) {

        });
    }



    $scope.Data = function(){
        return ProceduresServices.Data();
    }


    $scope.Init = function(){

        ProceduresServices.Reload().then(function(data){
            $scope.isLoaded = true;
        });
    }

});
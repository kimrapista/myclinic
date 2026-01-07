'use strict';


app.controller('Medicines', function($scope, $http, $timeout, $filter, $mdDialog, MedicinesServices,  global){ 


    global.page.title = 'Medicines';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;

    $scope.search = {
        name: '',
        isSubmit: false
    }


 
    $scope.Add_Edit = function (ID) {

        $mdDialog.show({
            templateUrl: 'views/client/modal_form_medicines.html',
            clickOutsideToClose: true,
            fullscreen: true,
            escapeToClose: true,
            locals:{
                ID: ID
            },
            controller: function($scope, $mdDialog, $filter, $timeout, $q, MedicinesServices, global, ID){

                $scope.isLoaded = false;

                $scope.FORM = [];

                $scope.Submit_Form = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'settings/medicines/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

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
                                MedicinesServices.Update($scope.FORM);             

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

                    MedicinesServices.Form(ID).then(function(data){
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
        return  $filter('filter')(MedicinesServices.Data(), $scope.search.name);
    }



    $scope.Init = function(){

        MedicinesServices.Reload().then(function(data){
            $scope.isLoaded = true;
        });
    }

});
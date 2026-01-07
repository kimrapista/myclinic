'use strict';


app.controller('Discounts', function($scope, $http, $location, $mdDialog, MeServices, DiscountsServices,  global){ 


    global.page.title = 'Discounts';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;


 
    $scope.Add_Edit = function (ID) {

        $mdDialog.show({
            templateUrl: 'views/client/modal_form_discounts.html',
            clickOutsideToClose: true,
            fullscreen: true,
            escapeToClose: true,
            locals:{
                ID: ID
            },
            controller: function($scope, $mdDialog, $filter, $timeout, $q, DiscountsServices, global, ID){

                $scope.isLoaded = false;

                $scope.FORM = [];

                $scope.Submit_Form = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'settings/discounts/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

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
                                DiscountsServices.Update($scope.FORM);             

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

                    DiscountsServices.Form(ID).then(function(data){
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
        return DiscountsServices.Data();
    }


    $scope.Init = function(){

        if( MeServices.Data().isAdmin ){
        
            DiscountsServices.Reload().then(function(data){
                $scope.isLoaded = true;
            }); 
        }
        else{
            $location.url('/error404');
        }
    }

});
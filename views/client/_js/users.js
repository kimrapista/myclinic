'use strict';


app.controller('Users', function($scope, $http, $mdDialog, $filter, $location, UsersServices, MeServices,  global){ 

    $scope.Me = function(){
        return MeServices.Data();
    }

    global.page.title = 'Users';
    global.page.pageBackUrl = '';

    $scope.isLoaded = false;


    $scope.User_Active = function(USER){

        UsersServices.Form_Active({
            ID: USER.ID, 
            ACTIVE: USER.ACTIVE
        }).then(function(data){

            if( USER.ACTIVE ){
                global.Toast('Activated');
            }
            else{
                global.Toast('Deactivated');  
            }
        });
    }


    $scope.Reset_Password = function(USER){

        var confirm = $mdDialog.confirm()
        .title('Confirmation')
        .textContent('Are you sure to reset '+ USER.NAME +' account password?')
        .ariaLabel('Reset Password')
        .targetEvent(event)
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function() {

            UsersServices.Form_Password({ID: USER.ID}).then(function(data){
                if( data )
                    global.Toast("New default password: 123");
            });

            
        }, function() {

        });

    }


    $scope.Add_Edit = function (ID) {

        $mdDialog.show({
            templateUrl: 'views/client/modal_form_users.html',
            clickOutsideToClose: true,
            fullscreen: true,
            escapeToClose: true,
            locals:{
                ID: ID
            },
            controller: function($scope, $mdDialog, $filter, $timeout, $q, UsersServices, MeServices, SubClinicServices, global, ID){

                $scope.Me = function(){
                    return MeServices.Data();
                }
                $scope.isLoaded = false;

                $scope.FORM = {
                    TOKEN: MeServices.Data().TOKEN,
                    ID: ID,
                    CLINICID: null,
                    NAME: '',
                    JOBTITLE: '',
                    EMAIL: '',
                    USERNAME: '',
                    ISADDSERVICES: false,
                    ISDISPLAYSERVICESAMOUNT: false,
                    isSubmit: false
                 };

                $scope.CLINICS = [];


                $scope.Assistant_Opt = function(){
                    if( $scope.FORM.POSITION != 'BRANCH ASSISTANT'){
                        $scope.FORM.AUTHORIZATION = false;
                        $scope.FORM.EDITMR = false;
                        $scope.FORM.ISADDSERVICES = false;
                        $scope.FORM.ISDISPLAYSERVICESAMOUNT = false;
                    }
                }


                $scope.Submit_Form = function(){

                    if( $scope.FORM.isSubmit ) return;

                    $scope.FORM.isSubmit = true;

                    $http.post( global.baseUrl +'settings/users/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

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
                                UsersServices.Update($scope.FORM);             

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

                    SubClinicServices.Reload().then(function(data){
                        if( data ) 
                            $scope.CLINICS =  SubClinicServices.Data();
                    });

                    UsersServices.Form(ID).then(function(data){
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
        return $filter('filter')( UsersServices.Data(), function(v,k){
            if( v.ID != MeServices.Data().ID ) return v;
        });
    }


    $scope.Init = function(){

        if( MeServices.Data().isAdmin ){
        
            UsersServices.Reload().then(function(data){
                $scope.isLoaded = true;
            });    
        }
        else{
            $location.url('/error404');
        }
    }

});
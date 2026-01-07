'use strict';
// doctor

app.controller('Users', function($scope, $http, $timeout, $filter, $rootScope, $mdDialog, global){ 


    $rootScope.$emit("Title_Global", { TITLE : 'USERS', BACKURL:'' });


    $scope.opt = {
        isLoaded : false,
        isSearch : false,
        searchText : '',
        searchUrl : global.baseUrl + 'settings/users/index',
        submitActive : global.baseUrl + 'settings/users/submit-active',
        submitResetPassword : global.baseUrl + 'settings/users/submit-reset-password',
        newUrl : '#!/settings/users/new',
        editUrl :  '#!/settings/users/',
        ACCOUNTBASE : global.ACCOUNTBASE,
        LEVEL : global.LEVEL
    }


    $scope.Load_List = function() {

        $scope.opt.isSearch = true;

        $http.get($scope.opt.searchUrl, global.ajaxConfig) .then( function(response) {

            if( response.data == 'RELOGIN'){
                global.Relogin();
            }
            else{

                $scope.LIST = [];
                $scope.LIST = response.data;

                angular.forEach($scope.LIST,function(v,k){
                    v.AUTHORIZATION = v.AUTHORIZATION == 'Y' ? true : false;
                    v.CANCELLED = v.CANCELLED == 'Y' ? false : true;
                    v.isSubmit = false;
                })
            }
            
            $scope.opt.isLoaded = true;
            $scope.opt.isSearch = false;

        }, 
        function(response){ 
            global.Relogin();
            $scope.opt.isSearch = false;
            $scope.opt.isLoaded = true;
        });

    }  


    $scope.Submit_Active = function(USER){

        $http.post($scope.opt.submitActive,{ ID: USER.ID, CANCELLED: USER.CANCELLED }, global.ajaxConfig) .then( function(response) {

            if( response.data == 'RELOGIN'){
                global.Relogin('FORM');
            }
            else{

               global.Toast(USER.NAME+' account is '+( USER.CANCELLED ? 'activated' : 'deactivated' ));
           }
        
        }, 
        function(response){ 
            global.Relogin('FORM');
        });
    }


    $scope.Submit_Reset_Password = function(USER){

        var confirm = $mdDialog.confirm()
        .title('Confirmation')
        .textContent('Are you sure to reset '+ USER.NAME +' account password?')
        .ariaLabel('Reset Password')
        .targetEvent(event)
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function() {

            $http.post($scope.opt.submitResetPassword,{ ID: USER.ID }, global.ajaxConfig) .then( function(response) {

                if( response.data == 'RELOGIN'){
                    global.Relogin('FORM1');
                }
                else{

                   global.Toast(USER.NAME+' new password is 123');
               }

           }, 
           function(response){ 
            global.Relogin('FORM1');

        });

            
        }, function() {

        });

    }



    $scope.Load_List();


    var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
        $scope.Load_List();
    });

    var dList1 = $rootScope.$on('RELOGIN_FORM', function (event, data) {
        $scope.Submit_Active();
    });

    var dList2 = $rootScope.$on('RELOGIN_FORM', function (event, data) {
        $scope.Submit_Reset_Password();
    });

    $scope.$on('$destroy', function() {
        dList();
        dList1();
        dList2();
    });
});
'use strict';
// admin

app.controller('Users', function($scope, $http, $timeout, $filter, $rootScope, global){ 


    $rootScope.$emit("Title_Global", { TITLE : 'USERS', BACKURL:'' });


    $scope.opt = {
        isLoaded : false,
        isSearch : false,
        searchText : '',
        searchUrl : global.baseUrl + 'settings/users/index',
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
                    v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
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



    $scope.Load_List();


    var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
        $scope.Load_List();
    });

    $scope.$on('$destroy', function() {
        dList();
    });
});
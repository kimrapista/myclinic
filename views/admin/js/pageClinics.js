'use strict';


app.controller('PageClinics', function($scope, $http, $timeout, $filter, $rootScope, global){ 


    $rootScope.$emit("Title_Global", { TITLE : 'CLINICS', BACKURL:'' });


    $scope.opt = {
        isLoaded : false,
        isSearch : false,
        searchText : '',
        searchUrl : global.baseUrl + 'clinics/index',
        newUrl : '#!/clinics/new',
        editUrl :  '#!/clinics/'
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
                    v.SALES = v.SALES == 'Y' ? true : false;
                    v.IS_BLAST = v.IS_BLAST == 'Y' ? true : false;
                    v.ACCOUNTBASE = v.ACCOUNTBASE == 'Y' ? true : false;
                    v.ASSISTANTRECORD = v.ASSISTANTRECORD == 'Y' ? true : false;
                });
            }

            $scope.opt.isLoaded = true;
            $scope.opt.isSearch = false;

        }, 
        function(response){ 
            global.Toast(response.data);
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

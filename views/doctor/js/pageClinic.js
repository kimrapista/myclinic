'use strict';
// doctor

app.controller('PageClinic', function($scope, $http, $timeout, $rootScope, $mdBottomSheet, $filter, global) {
    
    $rootScope.$emit("Title_Global", { TITLE : 'CLINIC', BACKURL:'' });

    $scope.opt = {
        isLoaded : false,
        listUrl : global.baseUrl + 'clinics/index',
        editUrl : '#!/clinic/edit',
        newSubUrl : '#!/clinic/subclinic/new',
        editSubUrl :  '#!/clinic/subclinic/',
        isDisabled : global.USER.LEVEL == 'BRANCH ADMINISTRATOR' ? false : true
    }


    $scope.Load_List = function() {

        $http.get($scope.opt.listUrl, global.ajaxConfig) .then( function(response) {

            if( response.data == 'RELOGIN'){
                global.Relogin();
            }
            else{

                $scope.CLINIC = [];
                $scope.CLINIC = response.data;

                $scope.CLINIC.MEDICALHISTORY = $scope.CLINIC.MEDICALHISTORY == 'Y' ? true : false;
                $scope.CLINIC.REFRACTION = $scope.CLINIC.REFRACTION == 'Y' ? true : false;
            }

            $scope.opt.isLoaded = true;

        }, function(response) {
            global.Relogin();  
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
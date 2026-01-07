"use strict";
// doctor

app.controller('PagePatients', function($scope, $http, $timeout, $filter, $rootScope, global) {

    $rootScope.$emit("Title_Global", { TITLE : 'PATIENTS', BACKURL:'' });


    $scope.opt = {
        isLoaded : false,
        isSearch : false,
        searchText : '',
        currentLimit : 0,
        limit : 100,
        searchUrl : global.baseUrl + 'patients/search-patients/',
        viewPatientUrl : '#!/patients/record/',
        newPatientUrl : '#!/patients/new',
        newMedicalRecorlUrl : '#!/patients/'
    }


    $scope.Search_Patients = function() {

        $scope.opt.isSearch = true;

        $http.post($scope.opt.searchUrl, { search: $scope.opt.searchText, currentLimit: $scope.opt.currentLimit, limit: $scope.opt.limit }, global.ajaxConfig).then(function(response) {

            if( response.data == 'RELOGIN' ){
                global.Relogin();
            }
            else{

                $scope.PATIENTS = [];
                $scope.PATIENTS = response.data;

                angular.forEach($scope.PATIENTS, function(val, key) {
                    val.DATEREG = global.Date(val.DATEREG);
                });
            }
            
            $scope.opt.isLoaded = true;
            $scope.opt.isSearch = false;

            $rootScope.$emit("Search_Global_Done");
        },
        function(response) {
            global.Relogin();
            $scope.opt.isSearch = false;
            $scope.opt.isLoaded = true;
        });

    }



    $scope.Search_Patients();


    var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
        $scope.Search_Patients();
    });


    var dList1 = $rootScope.$on('Search_Global_Control', function (event, data) {
        $scope.opt.searchText = data.text;
        $scope.Search_Patients();
    });


    $scope.$on('$destroy', function() {
        dList();
        dList1();
    });

});
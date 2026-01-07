'use strict';
// admin

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize', 'ngRoute', 'thatisuday.dropzone']);


app.config(function ($routeProvider, $locationProvider) {

	$routeProvider
    .when("/dashboard", {
        templateUrl: "views/admin/page_dashboard.php"
    })
    .when("/appointments", {
        templateUrl: "views/admin/page_appointments.php"
    })
    .when("/advisory", {
        templateUrl: "views/admin/page_advisory.php"
    })
    .when("/advisory/:P1", {
        templateUrl: "views/admin/form_advisory.php"
    })
    .when("/clinics", {
        templateUrl: "views/admin/page_clinics.php"
    })
    .when("/clinics/:P1", {
        templateUrl: "views/admin/form_clinic.php"
    })

    .when("/hospitals", {
        templateUrl: "views/admin/page_hospitals.php"
    })
    .when("/hospitals/:P1", {
        templateUrl: "views/admin/form_hospital.php"
    })

    .when("/settings/specialist", {
        templateUrl: "views/admin/page_specialist.php"
    })
    .when("/settings/specialist/:P1", {
        templateUrl: "views/admin/form_specialist.php"
    })

    .when("/settings/users", {
        templateUrl: "views/admin/page_users.php"
    })
    .when("/settings/users/:P1", {
        templateUrl: "views/admin/form_user.php"
    })




    // .when("/dashboard", {
    //     templateUrl: "views/doctor/page_dashboard.php"
    // })
    // .when("/patients", {
    //     templateUrl: "views/doctor/page_patients.php"
    // })
    // .when("/patients/new", {
    //     templateUrl: "views/doctor/form_patient.php"
    // })
    // .when("/patients/edit/:P1", {
    //     templateUrl: "views/doctor/form_patient.php"
    // })
    // .when("/patients/record/:P1", {
    //     templateUrl: "views/doctor/page_patient.php"
    // })
    // .when("/patients/:P1/medical-record/:P2", {
    //     templateUrl: "views/doctor/form_medical.php"
    // })
    // .when("/medical-records", {
    //     templateUrl: "views/doctor/page_medical.php"
    // })
    
    // .when("/sales", {
    //     templateUrl: "views/doctor/page_sales.php"
    // })
    
    // .when("/settings/subclinic", {
    //     templateUrl: "views/doctor/page_subclinic.php"
    // })
    // .when("/settings/subclinic/:P1", {
    //     templateUrl: "views/doctor/form_subclinic.php"
    // })
    // .when("/settings/services", {
    //     templateUrl: "views/doctor/page_services.php"
    // })
    // .when("/settings/services/:P1", {
    //     templateUrl: "views/doctor/form_service.php"
    // })
    // .when("/settings/discounts", {
    //     templateUrl: "views/doctor/page_discounts.php"
    // })
    // .when("/settings/discounts/:P1", {
    //     templateUrl: "views/doctor/form_discount.php"
    // })
    // .when("/settings/hmo", {
    //     templateUrl: "views/doctor/page_hmo.php"
    // })
    // .when("/settings/hmo/:P1", {
    //     templateUrl: "views/doctor/form_hmo.php"
    // })
    // .when("/settings/prescriptions", {
    //     templateUrl: "views/doctor/page_prescriptions.php"
    // })
    // .when("/settings/prescriptions/:P1", {
    //     templateUrl: "views/doctor/form_prescription.php"
    // })
    // .when("/settings/instructions", {
    //     templateUrl: "views/doctor/page_instructions.php"
    // })
    // .when("/settings/instructions/:P1", {
    //     templateUrl: "views/doctor/form_instruction.php"
    // })

    .when("/account", {
        templateUrl: "views/page_account.php"
    })
    .when("/error404", {
        templateUrl: "views/page_error.php"
    })
    .otherwise("/error404");


});




app.controller('Navigation', function ($scope, $http, $timeout, $filter, $rootScope, $mdToast, $log, $q, $mdDialog, $mdSidenav,global) {


    global.baseUrl = startUp.baseUrl;
    
    global.Set_USER({
        NAME: startUp.NAME, 
        JOBTITLE: startUp.JOBTITLE, 
        SPECIALISTID: startUp.SPECIALISTID, 
        BRANCH: startUp.BRANCH,
        LEVEL : startUp.LEVEL,
        SALES: (startUp.SALES == 1 ? true: false),
        AVATAR: global.baseUrl + 'assets/css/images/patient_default2.png'
    });


    $scope.search = {
        text: '',
        dateFrom: new Date(),
        dateTo: new Date(),
        view: false,
        viewText: false,
        viewDateFrom: false,
        viewDateTo: false,
        pageBackUrl: '',
        isSearching: false
    };

    $scope.opt = {
        title: '',
        USER: global.USER,
        signoutUrl: global.baseUrl + 'signout',
        class : 'fade',
        disabled : true,
        advisoryNotiVisible : false,
        drawer: (localStorage.getItem('drawer') == undefined ? true : (localStorage.getItem('drawer') == 'true' ? true : false))
    };




    $scope.Current_Page = function (title) {

        $scope.opt.title = title;

        $scope.search.view = false;
        $scope.search.viewText = false;
        $scope.search.viewDateFrom = false;
        $scope.search.viewDateTo = false;

        if (title == 'PATIENTS') {
            $scope.search.view = true;
            $scope.search.viewText = true;

        } 
        else if (title == 'DASHBOARD') {
            $scope.search.view = true;
            $scope.search.viewDateFrom = true;
        } 
        else if (title == 'SALES') {
            $scope.search.view = true;
            $scope.search.viewDateFrom = true;
            $scope.search.viewDateTo = true;

        } 
        else if (title == 'MEDICAL RECORDS' || title == 'APPOINTMENTS') {
            $scope.search.view = true;
            $scope.search.viewText = true;
            $scope.search.viewDateFrom = true;
            $scope.search.viewDateTo = true;
        }
    }



    $scope.toggleRight = function (sidepanel) {

        $mdSidenav(sidepanel)
        .toggle()
        .then(function () {
            $log.debug("toggle right is done");
        });
    }

    $scope.Toggle_Drawer = function () {

        localStorage.setItem('drawer', !$scope.opt.drawer);
    }


    $scope.Search_Global = function () {

        $scope.search.isSearching = true;

        $rootScope.$emit("Search_Global_Control", {
            text: $scope.search.text,
            dateFrom: $scope.search.dateFrom,
            dateTo: $scope.search.dateTo,
            SALES: $scope.opt.SALES
        });
    }


    $rootScope.$on('Search_Global_Done', function (event, data) {
        $scope.search.isSearching = false;
    });


    $rootScope.$on('Title_Global', function (event, data) {

        $scope.Current_Page(data.TITLE);

        if( data.BACKURL != undefined )
            $scope.opt.pageBackUrl = data.BACKURL;

        if (data.searchText == undefined) {
            $scope.search.text = '';
        }

        if (data.dateFrom == undefined) {
            $scope.search.dateFrom = new Date();
        }

        if (data.dateTo == undefined) {
            $scope.search.dateTo = new Date();
        }

    });




    $scope.PhilHealth_Case_Rate = function(){

        var deferred = $q.defer();

        $http.get( global.baseUrl + 'doh/case-rate', global.ajaxConfig).then(function (response) {

            deferred.resolve( response.data ); 
        },
        function (response) {
            global.Toast(response.data);
        });

        return deferred.promise;
    }   


    $scope.PhilHealth_Case_Rate().then(function(succ){

        angular.forEach(succ,function(v,k){
            global.Set_Case_Rate(v);
        });

        
    },function(err){
        console.log(err);
    });



    $rootScope.$on('Update_User_Info', function (event, data) {
        $scope.opt.USER = global.USER;
    });


    $scope.Current_Page('DASHBOARD');

    
});



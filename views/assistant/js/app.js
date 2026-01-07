'use strict';
// assistant

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize', 'ngRoute', 'thatisuday.dropzone']);


app.config(function ($routeProvider, $locationProvider) {

    $routeProvider
    .when("/dashboard", {
        templateUrl: "views/assistant/page_dashboard.php"
    })
    .when("/patients", {
        templateUrl: "views/assistant/page_patients.php"
    })
    .when("/patients/new", {
        templateUrl: "views/assistant/form_patient.php"
    })
    .when("/patients/edit/:P1", {
        templateUrl: "views/assistant/form_patient.php"
    })
    .when("/patients/record/:P1", {
        templateUrl: "views/assistant/page_patient.php"
    })
    .when("/patients/:P1/medical-record/:P2", {
        templateUrl: "views/assistant/form_medical.php"
    })
    .when("/medical-records", {
        templateUrl: "views/assistant/page_medical.php"
    })
    .when("/appointments", {
        templateUrl: "views/assistant/page_appointments.php"
    })
    .when("/settings/services", {
        templateUrl: "views/assistant/page_services.php"
    })
    .when("/settings/services/:P1", {
        templateUrl: "views/assistant/form_service.php"
    })
    .when("/settings/discounts", {
        templateUrl: "views/assistant/page_discounts.php"
    })
    .when("/settings/discounts/:P1", {
        templateUrl: "views/assistant/form_discount.php"
    })
    .when("/settings/hmo", {
        templateUrl: "views/assistant/page_hmo.php"
    })
    .when("/settings/hmo/:P1", {
        templateUrl: "views/assistant/form_hmo.php"
    })
    .when("/settings/prescriptions", {
        templateUrl: "views/assistant/page_prescriptions.php"
    })
    .when("/settings/prescriptions/:P1", {
        templateUrl: "views/assistant/form_prescription.php"
    })
    .when("/settings/instructions", {
        templateUrl: "views/assistant/page_instructions.php"
    })
    .when("/settings/instructions/:P1", {
        templateUrl: "views/assistant/form_instruction.php"
    })
   
    
    .when("/account", {
        templateUrl: "views/page_account.php"
    })
    .when("/error404", {
        templateUrl: "views/page_error.php"
    })
    .otherwise("/error404");

});




app.controller('Navigation', function ($scope, $http, $timeout, $filter, $rootScope, $mdToast, $log, $q, $location, $mdDialog, $mdSidenav, global) {


    global.Set_baseUrl(startUp.baseUrl);

    global.Set_USER({
        NAME: startUp.NAME,
        JOBTITLE: startUp.JOBTITLE,
        SPECIALISTID: (isNaN(startUp.SPECIALISTID) ? 0 : startUp.SPECIALISTID),
        LICENSENO: startUp.LICENSENO,
        PTR: startUp.PTR,
        SUBCLINICID: startUp.SUBCLINICID,
        LEVEL: startUp.LEVEL,
        SALES: (startUp.SALES == 1 ? true : false),
        AUTHORIZATION: (startUp.AUTHORIZATION == 1 ? true : false),
        EDITMR: (startUp.EDITMR == 1 ? true : false),
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
        placeholder: '',
        pageBackUrl: '',
        isSearching: false
    };


    $scope.opt = {
        title: '',
        USER: global.USER,
        signoutUrl: global.baseUrl + 'signout',
        class: 'fade',
        disabled: true,
        advisoryNotiVisible: false,
        userWarning: false,
        drawer: (localStorage.getItem('drawer') == undefined ? true : (localStorage.getItem('drawer') == 'true' ? true : false))
    };


    $scope.Toggle_Drawer = function () {

        localStorage.setItem('drawer', !$scope.opt.drawer);
    }


    $scope.Advisory = [];

    $scope.Current_Page = function (title) {

        $scope.opt.title = title;

        $scope.search.view = false;
        $scope.search.viewText = false;
        $scope.search.viewDateFrom = false;
        $scope.search.viewDateTo = false;

        if (title == 'PATIENTS') {
            $scope.search.view = true;
            $scope.search.viewText = true;
            $scope.search.placeholder = 'Search Name';

        } 
        else if (title == 'MEDICAL RECORDS' || title == 'APPOINTMENTS') {
            $scope.search.view = true;
            $scope.search.viewText = true;
            $scope.search.viewDateFrom = true;
            $scope.search.viewDateTo = true;
            $scope.search.placeholder = 'Search Name';
        }
        else if (title == 'SALES') {
            $scope.search.view = true;
            $scope.search.viewText = true;
            $scope.search.viewDateFrom = true;
            $scope.search.viewDateTo = true;
            $scope.search.placeholder = 'Search Clinic';
        } 
    }


    $scope.toggleRight = function (sidepanel) {

        $mdSidenav(sidepanel)
        .toggle()
        .then(function () {
            $log.debug("toggle right is done");
        });
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

        if (data.BACKURL != undefined)
            $scope.search.pageBackUrl = data.BACKURL;

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


    $scope.PhilHealth_Case_Rate = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'doh/icd-rvs', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            global.Toast(response.data);
        });

        return deferred.promise;
    }

    $scope.PhilHealth_Case_Rate().then(function (succ) {

        angular.forEach(succ[0], function (v, k) {
            global.Set_ICD(v);
        });

        angular.forEach(succ[1], function (v, k) {
            global.Set_RVS(v);
        });


    }, function (err) {
        console.log(err);
    });




    $scope.Load_Advisory = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'queue/advisory', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            global.Toast('No Connection');
        });

        return deferred.promise;
    }



    function Reload_Advisory() {

        $scope.Load_Advisory().then(function (succ) {

            angular.forEach(succ, function (v, k) {

                var found = false;

                angular.forEach($scope.Advisory, function (v1, k1) {

                    if (v.ID == v1.ID) {

                        if (v.HASH != v1.HASH) {

                            v1.TITLE = v.TITLE;
                            v1.BODY = v.BODY;
                            v1.LINK = v.LINK == null ? '' : '#!/' + v.LINK;
                            v1.POSTDATE = global.Date(v.POSTDATE);
                        }

                        found = true;
                    }
                });


                if (found == false) {

                    v.POSTDATE = global.Date(v.POSTDATE);
                    v.LINK = v.LINK == null ? null : '#!/' + v.LINK;
                    $scope.Advisory.push(v);
                }
            });


            var datas = [];

            angular.forEach($scope.Advisory, function (v1, k1) {
                angular.forEach(succ, function (v, k) {

                });
            });


            if (localStorage.getItem('advisoryNoti') == undefined) {

                $scope.opt.advisoryNotiVisible = true;
                localStorage.setItem('advisoryNoti', $scope.Advisory.length);
            } else if (parseInt(localStorage.getItem('advisoryNoti')) != $scope.Advisory.length) {

                $scope.opt.advisoryNotiVisible = true;
                localStorage.setItem('advisoryNoti', $scope.Advisory.length);
            }


            // setTimeout(function () {
            //     Reload_Advisory();
            // }, 120000);

        }, function (err) {
            Reload_Advisory();
        });
    }


    Reload_Advisory();


    $scope.Noti_Clear = function () {

        $scope.opt.advisoryNotiVisible = false;
    }


    $rootScope.$on('Update_User_Info', function (event, data) {
        $scope.opt.USER = global.USER;

    });

    $scope.Current_Page('DASHBOARD');


    $scope.PhilHealth_ICD = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'doh/icd', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }



    $scope.PhilHealth_RVS = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'doh/rvs', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }

    

    $scope.Load_Services = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-services', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Discounts = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-discounts', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Hmo = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-hmo', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Medicines = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-medicines', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Instructions = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-instruction', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Subclinics = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-subclinic', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Specialist = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-specialist', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }


    $scope.Load_Laboratory = function () {

        var deferred = $q.defer();

        $http.get(global.baseUrl + 'medicals/masterlist-laboratory', global.ajaxConfig).then(function (response) {

            deferred.resolve(response.data);
        },
        function (response) {
            deferred.reject(response.data);
        });

        return deferred.promise;
    }



    $rootScope.$on('Load_Masterlist', function (event, data) {

        var reloadCache = true;

        if( localStorage.getItem('reloadCacheDate') != undefined ){

            if( localStorage.getItem('reloadCacheDate') == (new Date()).getDate() ){
                reloadCache = false;
            }
        }

        localStorage.setItem('reloadCacheDate', (new Date()).getDate() );


        if( localStorage.getItem('ICD') == undefined  ){

            $scope.PhilHealth_ICD().then(function (succ) {
                global.Set_ICD(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('ICD');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_ICD( JSON.parse(localStorage.getItem('ICD')), true );
        }


        if( localStorage.getItem('RVS') == undefined  ){

            $scope.PhilHealth_RVS().then(function (succ) {
                global.Set_RVS(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('RVS');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_RVS( JSON.parse(localStorage.getItem('RVS')), true );
        }



        if( localStorage.getItem('SERVICES') == undefined || reloadCache ){

            $scope.Load_Services().then(function (succ) {
                angular.forEach(succ,function(v,k){
                    global.Set_Services(v);
                });                
            }, 
            function (err) { 
                localStorage.removeItem('SERVICES');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }   
        else {
            global.Set_Services( JSON.parse(localStorage.getItem('SERVICES')), true );
        }

        

        if( localStorage.getItem('DISCOUNTS') == undefined || reloadCache ){

            $scope.Load_Discounts().then(function (succ) {
                angular.forEach(succ,function(v,k){
                    global.Set_Discounts(v);
                }); 
            }, 
            function (err) { 
                localStorage.removeItem('DISCOUNTS');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Discounts( JSON.parse(localStorage.getItem('DISCOUNTS')), true );
        }




        if( localStorage.getItem('HMO') == undefined || reloadCache ){

            $scope.Load_Hmo().then(function (succ) {
                global.Set_Hmo(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('HMO');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Hmo( JSON.parse(localStorage.getItem('HMO')), true );
        }



        if( localStorage.getItem('MEDICINES') == undefined || reloadCache ){

            $scope.Load_Medicines().then(function (succ) {
                global.Set_Medicines(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('MEDICINES');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Medicines( JSON.parse(localStorage.getItem('MEDICINES')), true );
        }

        



        if( localStorage.getItem('INSTRUCTIONS') == undefined || reloadCache ){

            $scope.Load_Instructions().then(function (succ) {
                global.Set_Instructions(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('INSTRUCTIONS');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Instructions( JSON.parse(localStorage.getItem('INSTRUCTIONS')), true );
        }




        if( localStorage.getItem('SUBCLINICS') == undefined || reloadCache ){

            $scope.Load_Subclinics().then(function (succ) {
                global.Set_Subclinics(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('SUBCLINICS');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Subclinics( JSON.parse(localStorage.getItem('SUBCLINICS')), true );
        }
        



        if( localStorage.getItem('SPECIALIST') == undefined || reloadCache ){

            $scope.Load_Specialist().then(function (succ) {
                global.Set_Specialist(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('SPECIALIST');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Specialist( JSON.parse(localStorage.getItem('SPECIALIST')), true );
        }
        


        if( localStorage.getItem('LABORATORY') == undefined || reloadCache ){

            $scope.Load_Laboratory().then(function (succ) {
                global.Set_Laboratory(succ,true);
            }, 
            function (err) { 
                localStorage.removeItem('LABORATORY');
                $rootScope.$emit("Load_Masterlist"); 
            });
        }
        else {
            global.Set_Laboratory( JSON.parse(localStorage.getItem('LABORATORY')), true );
        }
    });


    $rootScope.$emit("Load_Masterlist");

});

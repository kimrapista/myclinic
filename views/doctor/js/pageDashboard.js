"use strict";
// doctor

app.controller('PageDashboard', function($scope, $http, $timeout, $interval, $filter, $rootScope, global) {

    $rootScope.$emit("Title_Global",  { TITLE : 'DASHBOARD', BACKURL:'' });

    $scope.opt = {
        isLoaded : false,
        summaryUrl : global.baseUrl + 'dashboard/summary-info',
        monthlyUrl : global.baseUrl + 'dashboard/monthly-info',
        yearlyUrl : global.baseUrl + 'dashboard/yearly-info',
        medicalRecordUrl : global.baseUrl + 'dashboard/medical-record',
        patientAgeUrl : global.baseUrl + 'dashboard/patient-age',
        USER : global.USER
    }
    
    
    $scope.Load_Summary = function() {

        $http.get($scope.opt.summaryUrl, global.ajaxConfig) .then( function(response) {

            $scope.summary = [];
            $scope.summary = response.data;

            $scope.summary.served = parseInt($scope.summary.served);
            $scope.summary.totalPatient = parseInt($scope.summary.totalPatient);
            $scope.summary.appointToday = parseInt($scope.summary.appointToday);
            $scope.summary.appointUpcoming = parseInt($scope.summary.appointUpcoming);
            $scope.summary.servicesAmount = parseFloat($scope.summary.servicesAmount);
            $scope.summary.discountAmount = parseFloat($scope.summary.discountAmount);
            $scope.summary.hmoAmount = parseFloat($scope.summary.hmoAmount);
            $scope.summary.cashAmount = parseFloat($scope.summary.cashAmount);

            angular.forEach($scope.summary.icd, function(v,k){
                v.TOTAL = parseInt(v.TOTAL);
            });

            angular.forEach($scope.summary.rvs, function(v,k){
                v.TOTAL = parseInt(v.TOTAL);
            });
            
            var sexLabels = ['MALE','FEMALE'];
            var sexValues = [0,0];

            angular.forEach($scope.summary.sex, function(v,k){
                if( v.labels == 'MALE' ){
                    sexValues[0] += parseInt(v.total);
                }
                else if( v.labels == 'FEMALE' ){
                    sexValues[1] += parseInt(v.total);
                }
                // else {
                //     sexValues[2] += parseInt(v.total);
                // }
            });

            
            var ctx = document.getElementById("chartPatientSex");
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: sexLabels,
                    datasets: [{
                        label: 'Sex',
                        data: sexValues,
                        backgroundColor: ['#00695C', '#4DB6AC', '#FFFFFF'],
                        borderColor:['#00695C', '#4DB6AC', '#FFFFFF'],
                        borderWidth: 1

                    }]
                },
                options: {
                    legend: {
                        display: true,
                        position:'right'
                    }
                }
            });

        }, function(response){});
    }



    $scope.Load_Monthly = function() {

        $http.get($scope.opt.monthlyUrl, global.ajaxConfig) .then( function(response) {

            $scope.monthly.data = [];
            $scope.monthly.data = response.data;

            if( $scope.monthly.data.labels.length < 3 ){
                $scope.monthly.data.labels.splice(0,0,'');
                $scope.monthly.data.labels.push('');

                $scope.monthly.data.served.splice(0,0,0);
                $scope.monthly.data.services.splice(0,0,0);
                $scope.monthly.data.discounts.splice(0,0,0);

                $scope.monthly.data.served.push(0);
                $scope.monthly.data.services.push(0);
                $scope.monthly.data.discounts.push(0);
            }
            
            $scope.monthly.data.labels.push('');
            $scope.monthly.data.served.push(0);

            var ctx = document.getElementById("chartMonthlServed");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: $scope.monthly.data.labels,
                    datasets: [{
                        label: 'SERVED THIS MONTH',
                        data: $scope.monthly.data.served,
                        backgroundColor: '#009688',
                        borderColor: '#009688',
                        borderWidth: 1,
                        pointHoverRadius: 0,
                        pointRadius: 0

                    }]
                },
                options: {
                    legend: false,
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: false
                    },
                    scales: {
                        xAxes: [{
                            ticks: { display: false },
                            gridLines: false
                        }],
                        yAxes:[{
                            ticks: { min: 0 }
                        }]
                    }
                }
            });
            

            $scope.monthly.totalserved = 0;
            
            angular.forEach($scope.monthly.data.served,function(v,k){
                $scope.monthly.totalserved += parseInt(v);
            });


            if( ! $scope.opt.USER.SALES ) return;

            if( $scope.monthly.data.labels.length > 0 )
                $scope.monthly.data.labels.splice( $scope.monthly.data.labels.length - 1, 1 );

            var ctx = document.getElementById("chartMonthlCollectables");
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $scope.monthly.data.labels,
                    datasets: [{
                        label: 'SERVICES',
                        data: $scope.monthly.data.services,
                        backgroundColor: 'transparent',
                        borderColor: '#00796B',
                        borderWidth: 2,
                        pointHoverRadius: 0,
                        pointBackgroundColor: '#00796B',
                        pointRadius: 2
                    },{
                        label: 'DISCOUNT',
                        data: $scope.monthly.data.discounts,
                        backgroundColor: 'transparent',
                        borderColor: '#4DB6AC',
                        borderWidth: 2,
                        pointHoverRadius: 0,
                        pointBackgroundColor: '#4DB6AC',
                        pointRadius: 2
                    }]
                },
                options: {
                    legend: false,
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    elements: { 
                        line: { tension: 0 }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                min: 0,
                                display: false
                            },
                            gridLines: false
                        }],
                        yAxes:[{
                            ticks: { min: 0 },
                            gridLines:{
                                display: true
                            }
                        }]
                    }
                }
            });

            
            $scope.monthly.totalservices = 0;
            $scope.monthly.totaldiscounts = 0;
            $scope.monthly.totalhmo = 0;
            $scope.monthly.totalcash = 0;

            

            angular.forEach($scope.monthly.data.services,function(v,k){
                $scope.monthly.totalservices += parseFloat(v);
            });

            angular.forEach($scope.monthly.data.discounts,function(v,k){
                $scope.monthly.totaldiscounts += parseFloat(v);
            });

            angular.forEach($scope.monthly.data.hmo,function(v,k){
                $scope.monthly.totalhmo += parseFloat(v);
            });

            angular.forEach($scope.monthly.data.cash,function(v,k){
                $scope.monthly.totalcash += parseFloat(v);
            });

        }, 
        function(response) { });
        // -- nothing
    }


    $scope.Load_Yearly = function() {

        if( ! $scope.opt.USER.SALES ) return;

        $http.get($scope.opt.yearlyUrl, global.ajaxConfig) .then( function(response) {

            $scope.yearly.data = [];
            $scope.yearly.data = response.data;

            if( $scope.yearly.data.labels.length < 3 ){
                $scope.yearly.data.labels.splice(0,0,'');
                $scope.yearly.data.labels.push('');
                $scope.yearly.data.netincome.splice(0,0,0);
                $scope.yearly.data.netincome.push(0);
            }

            var ctx = document.getElementById("chartYearlyCollectables");
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $scope.yearly.data.labels,
                    datasets: [{
                        label: 'Income',
                        data: $scope.yearly.data.netincome,
                        backgroundColor: '#009688',
                        borderColor: '#009688',
                        borderWidth: 1,
                        pointHoverRadius: 0,
                        pointBackgroundColor: '#009688',
                        pointRadius: 0
                    }]
                },
                options: {
                    legend: false,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    elements: { 
                        line: { tension: 0.1 }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                min: 0,
                                display: false
                            },
                            gridLines: false
                        }],
                        yAxes:[{
                            ticks: { min: 0 },
                            gridLines:{
                                display: true
                            }
                        }]
                    }
                }
            });


            $scope.yearly.netincome = 0;

            angular.forEach($scope.yearly.data.netincome,function(v,k){
                $scope.yearly.netincome += parseFloat(v);
            });

        },  
        function(response) {});
    }



    $scope.Load_MedicalRecord = function() {

        $http.get($scope.opt.medicalRecordUrl, global.ajaxConfig) .then( function(response) {

            $scope.medical = {data:[], total:0};
            $scope.medical.data = response.data;

            if( $scope.medical.data[0].length < 3 ){
                $scope.medical.data[0].splice(0,0,'');
                $scope.medical.data[0].push('');
                $scope.medical.data[1].splice(0,0,0);
                $scope.medical.data[1].push(0);
            }
          
            var ctx = document.getElementById("chartMedicalHistory");
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: $scope.medical.data[0],
                    datasets: [{
                        label: 'Medical Checkup',
                        data: $scope.medical.data[1],
                        backgroundColor: '#009688',
                        borderColor: '#009688',
                        borderWidth: 1,
                        pointRadius: 0

                    }]
                },
                options: {
                    legend: false,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    elements: { 
                        line: { tension: 0.2 }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                min: 0,
                                display: false
                            },
                            gridLines: false
                        }],
                        yAxes:[{
                            ticks: { min: 0 },
                            gridLines:{
                                display: true
                            }
                        }]
                    }
                }
            });

            angular.forEach($scope.medical.data[1], function(v,k){
                $scope.medical.total += parseInt(v);
            });

        },function(response){});
    }


    $scope.Load_PatientAge = function() {

        $http.get($scope.opt.patientAgeUrl, global.ajaxConfig) .then( function(response) {

            if( response.data == 'RELOGIN'){
                global.Relogin();
            }
            else{

                $scope.patientAge = [];
                $scope.patientAge = response.data;

                $scope.patientAge.labels.push('');
                $scope.patientAge.values.push(0);

                var ctx = document.getElementById("chartPatientAge");
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: $scope.patientAge.labels,
                        datasets: [{
                            label: 'Age %',
                            data: $scope.patientAge.values,
                            backgroundColor: ['#4DB6AC','#26A69A', '#009688', '#00897B','#00796B','#00695C','#004D40'],
                            borderColor:['#4DB6AC','#26A69A', '#009688', '#00897B','#00796B','#00695C','#004D40'],
                            borderWidth: 1

                        }]
                    },
                    options: {
                        legend: false,
                        scales: {
                            xAxes: [{
                                ticks: {
                                    min: 0,
                                    display: true
                                },
                                gridLines: false
                            }],
                            yAxes:[{
                                ticks: { 
                                    min: 0,
                                    display: true
                                },
                                gridLines:{
                                    display: true
                                }
                            }]
                        }
                    }
                });
            }

            $scope.opt.isLoaded = true;

        },
        function(response) {  
            global.Relogin();
            $scope.opt.isLoaded = true;
        });
    }


    // excute after load
    $scope.monthly = [];
    $scope.yearly = [];
    $scope.yearly.year = new Date();
    $scope.yearly.year = $scope.yearly.year.getFullYear();

    $scope.Load_Summary();
    $scope.Load_Monthly();
    $scope.Load_Yearly();

    $scope.Load_MedicalRecord();
    $scope.Load_PatientAge();


    var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
        $scope.Load_Summary();
        $scope.Load_Monthly();
        $scope.Load_Yearly();

        $scope.Load_MedicalRecord();
        $scope.Load_PatientAge();
    });



    $scope.$on('$destroy', function() {
        dList();

    });


});
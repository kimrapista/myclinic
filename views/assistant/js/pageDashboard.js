"use strict";
// ASSISTANT

app.controller('PageDashboard', function($scope, $http, $timeout, $interval, $filter, $rootScope, global) {

    $rootScope.$emit("Title_Global",  { TITLE : 'DASHBOARD', BACKURL:'' });

    $scope.opt = {
        isLoaded : false,
        summaryUrl : global.baseUrl + 'dashboard/summary-info',
        monthlyUrl : global.baseUrl + 'dashboard/monthly-info',
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


            $scope.opt.isLoaded = true;

        },
        function(response) {  
            global.Relogin();
            $scope.opt.isLoaded = true;
        });
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

        },
        function(response) {  
           
        });
    }


    // excute after load
    $scope.monthly = [];
    $scope.yearly = [];
    $scope.yearly.year = new Date();
    $scope.yearly.year = $scope.yearly.year.getFullYear();

    $scope.Load_Summary();
    $scope.Load_Monthly();

    var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
        $scope.Load_Summary();
        $scope.Load_Monthly();
    });



    $scope.$on('$destroy', function() {
        dList();

    });


});
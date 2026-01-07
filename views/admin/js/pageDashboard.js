"use strict";
// admin

app.controller('PageDashboard', function($scope, $http, $timeout, $interval, $filter, $rootScope, global) {

    $rootScope.$emit("Title_Global",  { TITLE : 'DASHBOARD', BACKURL:'' });

    $scope.opt = {
        isLoaded : false,
        clinicsServed : global.baseUrl + 'dashboard/clinics-served',
        SEARCHDATE: new Date()
    }

    $scope.DATA = {
        CLINICS:[],
        TOTAL_TODAY_SERVED: 0,
        TOTAL_TODAY_MALE: 0,
        TOTAL_TODAY_FEMALE: 0,
        MONTHMR:{DAY:[],TOTAL:[]},
        TOTAL_MONTH_SERVED: 0,
        TOTAL_MONTH_NEWREG: 0,
        TOTAL_MONTH_MALE: 0,
        TOTAL_MONTH_FEMALE: 0,
    }



    $scope.Load_Clinics_Served = function() {

        $http.post($scope.opt.clinicsServed, {SEARCHDATE: $scope.opt.SEARCHDATE}, global.ajaxConfig) .then( function(response) {

            $scope.DATA.CLINICS = [];
            $scope.DATA.TOTAL_TODAY_SERVED = 0;  
            $scope.DATA.TOTAL_TODAY_MALE = 0;           
            $scope.DATA.TOTAL_TODAY_FEMALE = 0;           

            angular.forEach( response.data.CLINICS, function(v,k){
                $scope.DATA.CLINICS.push({
                    NAME: v.CLINICNAME,
                    TODAY: parseInt(v.SERVEDTODAY),
                    MONTH: parseInt(v.SERVEDMONTH),
                    AVERAGE: 0
                })

                $scope.DATA.TOTAL_TODAY_SERVED += parseInt(v.SERVEDTODAY);
                $scope.DATA.TOTAL_TODAY_MALE += parseInt(v.MALE);
                $scope.DATA.TOTAL_TODAY_FEMALE += parseInt(v.FEMALE);
            });


            angular.forEach( $scope.DATA.CLINICS,function(v,k){
                v.AVERAGE = ((v.TODAY/$scope.DATA.TOTAL_TODAY_SERVED) * 100);
            });


            document.getElementById('chartTodaySex').innerHTML = '';
            document.getElementById('chartTodaySex').innerHTML = '<canvas height="80"></canvas>';
            var sexCanvas =  document.getElementById('chartTodaySex').querySelector("canvas").getContext('2d');

            var sexChart = new Chart(sexCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['MALE','FEMALE'],
                    datasets: [{
                        label: 'Sex',
                        data: [$scope.DATA.TOTAL_TODAY_MALE, $scope.DATA.TOTAL_TODAY_FEMALE],
                        backgroundColor: ['#00695C', '#4DB6AC'],
                        borderColor:['#00695C', '#4DB6AC'],
                        borderWidth: 1

                    }]
                },
                options: {
                    legend: false,
                    rotation: 1 * Math.PI,
                    circumference: 1 * Math.PI,
                    animation: {
                        animateRotate: true
                    }
                }
            });

            $scope.DATA.TOTAL_MONTH_NEWREG = parseInt(response.data.MONTHPATIENT.NEWREG);
            $scope.DATA.TOTAL_MONTH_MALE = parseInt(response.data.MONTHPATIENT.MALE);
            $scope.DATA.TOTAL_MONTH_FEMALE = parseInt(response.data.MONTHPATIENT.FEMALE);

            $scope.DATA.TOTAL_MONTH_SERVED = 0;
            $scope.DATA.MONTHMR.DAY = [];
            $scope.DATA.MONTHMR.TOTAL = [];


            angular.forEach( response.data.MONTHMR, function(v,k){
                $scope.DATA.MONTHMR.DAY.push(v.CHECKUPDATE);
                $scope.DATA.MONTHMR.TOTAL.push(parseInt(v.TOTAL));

                $scope.DATA.TOTAL_MONTH_SERVED += parseInt(v.TOTAL);
            });


            document.getElementById('chartMonthMR').innerHTML = '';
            document.getElementById('chartMonthMR').innerHTML = '<canvas height="80"></canvas>';
            var monthMRCanvas =  document.getElementById('chartMonthMR').querySelector("canvas").getContext('2d');

            var myChart = new Chart(monthMRCanvas, {
                type: 'line',
                data: {
                    labels: $scope.DATA.MONTHMR.DAY,
                    datasets: [{
                        label: 'SERVED',
                        data: $scope.DATA.MONTHMR.TOTAL,
                        backgroundColor: '#00796B',
                        borderColor: '#00796B',
                        borderWidth: 2,
                        pointHoverRadius: 0,
                        pointRadius: 0

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
                            ticks: { 
                                min: 0 ,
                                display: false
                            },
                            gridLines:{
                                display: false
                            }
                        }]
                    }
                }
            });



            $scope.opt.isLoaded = true;

            $rootScope.$emit("Search_Global_Done");
        }, 
        function(response) { 
        });

}





$scope.Load_Clinics_Served();


var dList1 = $rootScope.$on('Search_Global_Control', function (event, data) {
    $scope.opt.SEARCHDATE = data.dateFrom;
    $scope.Load_Clinics_Served();
});



$scope.$on('$destroy', function() {
    dList();
});


});
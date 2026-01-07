'use strict';


app.controller('Dashboard', function($scope, $location, DashboardServices, MeServices, global){ 

   $scope.Me = function(){
      return MeServices.Data();
   }

   global.page.title = 'Dashboard';
   global.page.pageBackUrl = '';

   $scope.isLoaded = false;

   
   
   
   $scope.Month_Net_Chart = function(){

      document.getElementById('monthNetCharts').innerHTML = '';
      document.getElementById('monthNetCharts').innerHTML = '<canvas height="265"></canvas>';
      var ctx = document.getElementById('monthNetCharts').querySelector("canvas").getContext('2d');

      var gradientFill = ctx.createLinearGradient(600, 0, 0, 0);
      gradientFill.addColorStop(0, "rgba(0, 150, 136, 0.8)");
      gradientFill.addColorStop(1, "rgba(200, 200, 200, 0.7)");
  
      var chartVS = new Chart(ctx, {
         type: 'line',
         data: {
            labels: DashboardServices.Data().MONTHNET.labels,
            datasets: [{
               label: 'Net Income',
               data: DashboardServices.Data().MONTHNET.values,
               backgroundColor: gradientFill,
               borderColor: gradientFill,
               pointBorderColor: gradientFill,
               pointBackgroundColor: gradientFill,
               pointHoverBackgroundColor: gradientFill,
               pointHoverBorderColor: gradientFill,
               borderWidth: 1,
               pointHoverRadius: 0,
               pointRadius: 0,
               fill: true
      
            }]
         },
         options: {
            scaleLineColor: "transparent",
            legend: false,
            responsive: true,
            maintainAspectRatio: false,
            animation: {
               easing: "easeInOutBack"
               // duration: 0
            },
            tooltips: {
               mode: 'index',
               intersect: false
            },
            hover: {
               mode: 'nearest',
               intersect: true,
               animationDuration: 200
            },
            elements: { 
               line: { tension: 0.05 }
            },
            layout:{
               padding:{
                  top: 0,
                  right: 0,
                  bottom: 0,
                  left: 0
               }
            },
            scales: {
               xAxes: [{
                  ticks: {

                     display: false
                  },
                  gridLines: true
               }],
               yAxes:[{
                  ticks: { 
                     
                     display: false
                  },
                  gridLines:{
                     display: false
                  }
               }]
            }
         }
      });

   }
 
   $scope.Month_Served_Chart = function(){

      document.getElementById('monthServedCharts').innerHTML = '';
      document.getElementById('monthServedCharts').innerHTML = '<canvas height="265"></canvas>';
      var ctx = document.getElementById('monthServedCharts').querySelector("canvas").getContext('2d');

      var gradientFill = ctx.createLinearGradient(600, 0, 0, 0);
      gradientFill.addColorStop(0, "rgba(0, 150, 136, 0.8)");
      gradientFill.addColorStop(1, "rgba(200, 200, 200, 0.7)");
  
      var chartVS = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: DashboardServices.Data().MONTHSERVED.labels,
            datasets: [{
               label: 'MR',
               data: DashboardServices.Data().MONTHSERVED.values,
               backgroundColor: gradientFill,
               borderColor: gradientFill,
               pointBorderColor: gradientFill,
               pointBackgroundColor: gradientFill,
               pointHoverBackgroundColor: gradientFill,
               pointHoverBorderColor: gradientFill,
               borderWidth: 0,
               pointHoverRadius: 0,
               pointRadius: 0,
               fill: true
      
            }]
         },
         options: {
            legend: false,
            responsive: true,
            maintainAspectRatio: false,
            animation: {
               easing: "easeInOutBack"
            },
            tooltips: {
               mode: 'index',
               intersect: false
            },
            hover: {
               mode: 'nearest',
               intersect: true,
               animationDuration: 100
            },
            elements: { 
               line: { tension: 0.1 }
            },
            layout:{
               padding:{
                  top: 20,
                  right: 0,
                  bottom: 0,
                  left: 0
               }
            },
            scales: {
               xAxes: [{
                  ticks: {

                     display: false
                  },
                  gridLines: false
               }],
               yAxes:[{
                  ticks: { 
                     
                     display: false
                  },
                  gridLines:{
                     display: false
                  }
               }]
            }
         }
      });

   }


   $scope.Today_Sex_Chart = function(){

      document.getElementById('todaySexCharts').innerHTML = '';
      document.getElementById('todaySexCharts').innerHTML = '<canvas height="140"></canvas>';
      var sexCanvas =  document.getElementById('todaySexCharts').querySelector("canvas").getContext('2d');

      var sexChart = new Chart(sexCanvas, {
            type: 'doughnut',
            data: {
               labels: ['MALE','FEMALE'],
               datasets: [{
                  label: 'Sex',
                  data: [ DashboardServices.Data().SUMMARY.NEWMALE, DashboardServices.Data().SUMMARY.NEWFEMALE ],
                  backgroundColor: ['#00695C', '#4DB6AC'],
                  borderColor:['#00695C', '#4DB6AC'],
                  borderWidth: 1

               }]
            },
            options: {
               legend: {
                  display: true,
                  position:'right'
               },
               responsive: true,
               maintainAspectRatio: false,
               // rotation: 1 * Math.PI,
               // circumference: 1 * Math.PI,
               animation: {
                  animateRotate: true
               }
            }
      });
   }


   $scope.Patients_Chart = function(){

      document.getElementById('patientsCharts').innerHTML = '';
      document.getElementById('patientsCharts').innerHTML = '<canvas height="200"></canvas>';
      var ctx = document.getElementById('patientsCharts').querySelector("canvas").getContext('2d');

      var gradientFill = ctx.createLinearGradient(600, 0, 0, 0);
      gradientFill.addColorStop(0, "rgba(0, 150, 136, 0.8)");
      gradientFill.addColorStop(1, "rgba(200, 200, 200, 0.7)");
  
      var chartVS = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: DashboardServices.Data().PATIENTS.labels,
            datasets: [{
               label: 'New Registered',
               data: DashboardServices.Data().PATIENTS.values,
               backgroundColor: gradientFill,
               borderColor: gradientFill,
               pointBorderColor: gradientFill,
               pointBackgroundColor: gradientFill,
               pointHoverBackgroundColor: gradientFill,
               pointHoverBorderColor: gradientFill,
               borderWidth: 1,
               pointHoverRadius: 0,
               pointRadius: 0,
               fill: true
      
            }]
         },
         options: {
            legend: false,
            responsive: true,
            maintainAspectRatio: false,
            animation: {
               easing: "easeInOutBack"
            },
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

                     display: false
                  },
                  gridLines: true
               }],
               yAxes:[{
                  ticks: { 
                     
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

   $scope.Data = function(){
      return DashboardServices.Data();
   }
   
   $scope.Init = function(){

      if( MeServices.Data().isAssistant ){
         $scope.isLoaded = true;
         $location.url('/patients');
      }
      else{

         DashboardServices.Load_Summary().then(function(data){
            $scope.Today_Sex_Chart();
         });
   
         DashboardServices.Load_Month_Net_Chart().then(function(data){
            $scope.Month_Net_Chart();
         });
   
         DashboardServices.Load_Month_Served_Chart().then(function(data){
            $scope.Month_Served_Chart();
         });
      }

      $scope.isLoaded = true;
   }

});
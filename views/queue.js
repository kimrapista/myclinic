"use strict";

app.controller('Queue', function($scope, $http, $timeout, $filter) {


    $scope.init = function(baseUrl) {
        $scope.baseUrl = baseUrl
        $scope.listUrl = baseUrl + 'queue/index';
        $scope.removeUrl = baseUrl +'queue/patient-remove';
        $scope.priorityUrl = baseUrl +'queue/patient-priority';
        $scope.tomorrowUrl = baseUrl +'queue/patient-tomorrow';

        $scope.isSearch = false;
        $scope.liveCheck = false;

        if (localStorage.getItem("liveCheck")){ 
            $scope.liveCheck = localStorage.getItem("liveCheck") == 'true' ? true : false; 

        }

        $scope.Load_Queued();
    }


    $scope.Load_Queued = function(){

        $scope.isSearch = true;
        $http.get($scope.listUrl).then(function(response){

            $scope.queues = [];
            $scope.queues = response.data;

            angular.forEach($scope.queues,function(v,k){
                v.PRIORITYNO = parseInt(v.PRIORITYNO);
                v.WAITING = Boolean(parseInt(v.WAITING));
                v.SERVING = Boolean(parseInt(v.SERVING));
                v.PAID = Boolean(parseInt(v.PAID));
                v.ACTIVE = Boolean(parseInt(v.ACTIVE));
            });

            $scope.isSearch = false;

        },  function(response){  });
    }


    $scope.Toggle_Live = function(){
        $scope.liveCheck = !$scope.liveCheck;
        localStorage.setItem('liveCheck', $scope.liveCheck);
        console.log($scope.liveCheck)
    }


    $scope.Remove_Queued = function(key,ID){

        Loading(true);

        $http.get($scope.removeUrl+'/'+ID).then(function(response){ 
            Form_Success('Removed');
        },function(response){  });
        
        $scope.queues.splice(key,1);
    }


    $scope.Change_PriorityNo = function(queue){


        $http.post($scope.priorityUrl,{ID:queue.ID,PNO:queue.PRIORITYNO}).then(function(response){ 
            
        },function(response){  });

        setTimeout(function(){$('#pno_'+queue.ID).focus();});
        
    }


     $scope.Patient_Qtomorrow = function(id){

        Loading(true);
        $http.get($scope.tomorrowUrl+'/'+id).then(function(response){

            if( response.data.err != '' ){
                Form_Error(response.data.err);
            }
            else{
                Form_Success(response.data.suc);
            }
        }, 
        function(response){ 
            Form_Error(response.data); 
        });

    }


    setInterval(function(){ 
        if ($scope.liveCheck) { $scope.Load_Queued();  }
    },5000);




});
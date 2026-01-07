'use strict';


app.controller('PatientVideoCall', function($scope, $routeParams, global, MeServices, PatientRecordServices, WebRTC1){ 
   
   $scope.Me = function(){
      return MeServices.Data();
   }
  
   global.page.title = 'Patient Video Call';
   global.page.pageBackUrl = '#!/patient/' + $routeParams.PID + '/record';


   $scope.Patient = function(){
      return PatientRecordServices.Data_Patient(); 
   }

   // -------- for webRTCP2P -----------------
   // $scope.Start = function(event){
   //    WebRTCP2P.Start();
   // }
   // $scope.Call = function(){
   //    WebRTCP2P.Call();
   // }
   // $scope.Hangup = function(){
   //    WebRTCP2P.Hangup();
   // }
   // $scope.RTC = function(){
   //    return WebRTC1.Data();
   // }

   $scope.RTC = function(){
      return WebRTC1.Data();
   }

   $scope.Get_Media = function(){
      WebRTC1.Get_Media();
   }

   $scope.Create_Peer_Connection = function(){
      WebRTC1.Create_Peer_Connection();
   }

   $scope.Create_Offer = function(){
      WebRTC1.Create_Offer();
   }

   $scope.Set_Offer = function(){
      WebRTC1.Set_Offer();
   }

   $scope.Create_Answer = function(){
      WebRTC1.Create_Answer();
   }

   $scope.Set_Answer = function(){
      WebRTC1.Set_Answer();
   }

   $scope.Hang_up = function(){
      WebRTC1.Hang_up();
   }
 




   $scope.Init = function(){

      $scope.isLoaded = false;

      PatientRecordServices.Load_Patient($routeParams.PID).then(function(data){
         $scope.isLoaded = true;

         WebRTC1.Init();
      });
   }

     
});
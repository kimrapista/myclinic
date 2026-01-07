"use strict";

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages','ngSanitize']);


app.controller('VideoCall', function($scope, $http, $timeout, global, WebRTCRemote) {


   
   $scope.RTC = function(){
      return WebRTCRemote.Data();
   }

   $scope.Get_Media = function(){
      WebRTCRemote.Get_Media();
   }

   $scope.Create_Peer_Connection = function(){
      WebRTCRemote.Create_Peer_Connection();
   }

   $scope.Create_Offer = function(){
      WebRTCRemote.Create_Offer();
   }

   $scope.Set_Offer = function(){
      WebRTCRemote.Set_Offer();
   }

   $scope.Create_Answer = function(){
      WebRTCRemote.Create_Answer();
   }

   $scope.Set_Answer = function(){
      WebRTCRemote.Set_Answer();
   }

   $scope.Hang_up = function(){
      WebRTCRemote.Hang_up();
   }
 
 
   $scope.Init = function(){

      WebRTCRemote.Init();
   }

});
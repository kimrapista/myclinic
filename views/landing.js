'use strict';

app.config(function($mdThemingProvider) {
	$mdThemingProvider.theme('default')
	.primaryPalette('teal')
	.accentPalette('grey')
	.warnPalette('red');
 }); 


app.controller('Landing', function($scope, $filter, $timeout, global, Map, LandingServices){

   global.baseUrl = baseUrl;
   $scope.isLoaded = false;

   $scope.search = {
      NAME: '',
      FROM: 0,
      TO: 20,
      LIMIT: 20
   }

   
   $scope.Data = function(){
      return LandingServices.Data();
   }


   $scope.Search_Doctor = function(editInForm){

      $scope.Remove_Clinic_Layer();
            
      LandingServices.Data().isSearching = true;

      LandingServices.Load_Doctors({
         TOKEN: global.TOKEN,
         NAME: $scope.search.NAME,
         FROM: $scope.search.FROM,
         TO: $scope.search.TO
      }).then(function(data){

         LandingServices.Data().isSearching = false;

         if( LandingServices.Data().DOCTORS.length == 0 && $scope.search.NAME != '' ){
            global.Toast('Search not found')
         }

         if( editInForm ){
            setTimeout(function(){
               $('#search').focus();
            },200);
         }

      });
   }

   $scope.View_Profile = function(DOCTOR){

      if( DOCTOR.LINK ){
         location.replace(global.baseUrl + DOCTOR.LINK);
      }
      else{
         location.replace(global.baseUrl + DOCTOR.ID);
      }
   }

   
   $scope.Set_Layers = function(){

      angular.forEach( LandingServices.Data().MAPS, function(v,k){
         Map.Add_Layer( v.NAME, v.COORDINATEL);
      });
   }


   $scope.Remove_Clinic_Layer = function(){

      angular.forEach( LandingServices.Data().DOCTORS, function(v,k){
         angular.forEach( v.LOCATIONS, function(v1,k1){
            Map.Remove_Layer_Name( 'subclinic_'+v1.ID);
         });
      });
   }

   
   

   
   $scope.Doctor_Location_Focus = function(DOCTOR){

      var foundLocationKey = null;
      var tempCoord = null;

      for (var i = 0; i < LandingServices.Data().MAPS.length; i++) {

         var temp = LandingServices.Data().MAPS[i];
        
         for (var ii = 0; ii < temp.DOCTORS.length; ii++) {

            if( temp.DOCTORS[ii].DOCTORID === DOCTOR.ID  ){

               if( ! temp.isFocus ){
                  Map.Remove_Layer_Name(temp.NAME);
                  Map.Add_Layer( temp.NAME, temp.COORDINATEL, 'focus');     
                  temp.isFocus = true;
               }

               if( foundLocationKey == null ){
                  tempCoord = temp.COORDINATEL;
               }
               
               foundLocationKey = i;
            }
         }

         // unfocus others clinic
         if( foundLocationKey != i && temp.isFocus ){

            Map.Remove_Layer_Name(temp.NAME);
            Map.Add_Layer( temp.NAME, temp.COORDINATEL);
            temp.isFocus = false;
         }
      }


      if( foundLocationKey == null ){
         global.Toast('No location provided');
      }
      // let change first the icon then goto location
      else if( tempCoord) {
         $timeout(function(){
            Map.Goto(tempCoord);
         },200,false)         
      }
   }



   $scope.Back_Home = function(){

      if( Map.Data().my_location[0] != undefined ){
         Map.Goto(Map.Data().my_location);
      }
      else{
         Map.Set_My_Location();
      }      
   }

   $scope.Init = function(TOKEN){

      global.TOKEN = TOKEN;
      
      Map.Data().enablePopup = true;
      Map.Init();
      Map.Set_My_Location();

      $scope.Search_Doctor();   
      
      LandingServices.Reload_Clinics().then(function(data){

         angular.forEach( LandingServices.Data().MAPS, function(v,k){
            Map.Add_Layer( v.NAME, v.COORDINATEL);
         });
      });
      
   
      
      $scope.isLoaded = true;    
      $scope.Scrolling();
   }


   $scope.Scrolling = function(ELEMENT){
      
      var top = 0;

      if( ELEMENT != '') {
         
         if( $('#' + ELEMENT).position() != undefined){
            top = $('#' + ELEMENT).get( 0 ).offsetTop;
         }
      }

      $('.mdl-layout__content').stop().animate({ scrollTop: top }, 1000);
   }
  
});





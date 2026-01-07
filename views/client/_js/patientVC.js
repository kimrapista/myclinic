'use strict';


app.controller('PatientVC', function($scope, $routeParams, $http, $timeout, $location, global, MeServices){ 
   
   $scope.Me = function(){
      return MeServices.Data();
   }
  
   global.page.title = 'Patient Video Call';
   global.page.pageBackUrl = '#!/patient/' + $routeParams.PID + '/record';


   var api;

   $scope.Init = function(){

      var domain = 'meet.jit.si';
      var options = {
         roomName: MeServices.Data().ID + $routeParams.PID,
         width: window.innerWidth,
         height: window.innerHeight,
         parentNode: document.querySelector('#conf_div')
      };

      api = new JitsiMeetExternalAPI(domain, options);

      api.executeCommand('displayName', MeServices.Data().NAME);
      api.executeCommand('avatarUrl', MeServices.Data().AVATAR);
   }

    
});
'use strict';

app.factory('WebRTC', function( global ){

   
   const constraints = window.constraints = {
      audio: true,
      video: true
    };
    

    function handleSuccess(stream) {

      const video = document.querySelector('video');
      const audio = document.querySelector('audio');

      window.stream = stream; // make variable available to browser console
      video.srcObject = stream;
      audio.srcObject = stream;
    }
    
    function handleError(error) {

      if (error.name === 'ConstraintNotSatisfiedError') {
         const v = constraints.video;
         global.Alert(`The resolution ${v.width.exact}x${v.height.exact} px is not supported by your device.`);
      } 
      else if (error.name === 'PermissionDeniedError') {
        
         global.Alert('Permissions have not been granted to use your camera and ' +
          'microphone, you need to allow the page access to your devices in ' +
          'order for the demo to work.');
      }
      else{
         global.Alert(error);
      }
    }
  

   async function init() {
      try {
         const stream = await navigator.mediaDevices.getUserMedia(constraints);
         handleSuccess(stream);
      } 
      catch (e) {
         handleError(e);
      }
    }
    

   return{

      Start: function(){

         init();
      }

   }

});
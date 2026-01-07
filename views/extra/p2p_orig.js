'use strict';

app.factory('P2P', function($http, $timeout, global ){

   

   var peer;
   
   var PATIENT = [];

   var data = { 
      TOKEN: null,
      USERID: null,
      PID: null,
      initiator: true,
      localCode : [],
      remoteCode: [],
      messages: [],
      isVideo : true,
      isConnecting: false,
      isConnected: false,
      isClosed: false,
      localHttp: false,
      remoteHttp: false
   };
   
   


   async function Init(initiator){

      data.initiator = initiator;
      data.isConnecting = true;
      data.isConnected = false;
      data.isClosed = false;
      data.messages = [];
      data.localCode = [];
      data.remoteCode = [];


      navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
      

      if (navigator.getUserMedia) {

         navigator.getUserMedia({
            video: true,
            audio: true
         }, function(stream){
            
            data.isVideo = true;
            GotStream(initiator, stream);
   
         }, function(err){
            
            data.isVideo = false;
            console.log(err);
            global.Alert('Sorry no media devices detected.');
            GotStream(initiator, null);
         });

      } 
      else {
         console.log("getUserMedia not supported");
      }

      
   }

   
   function GotStream(initiator, stream){
      
      peer = new SimplePeer({
         initiator: initiator,
         trickle: false,
         stream: stream
      });

      peer.on('connect', ()=>{
         $timeout(function(){
            data.isConnecting = false;
            data.isConnected = true;
         })
         console.log('connect');
      });
      
      peer.on('signal', function(code){ 
         $timeout(function(){
            data.localCode = code;
         })
      });

      peer.on('data', function(message){
         data.messages.push({
            local: false,
            message: message,
            date: new Date()
         })
      });


      peer.on('stream', function(stream){
         
         if( data.isVideo  ){
            var localVideo = document.querySelector('#MeVideo');
         
            if ('srcObject' in localVideo) {
               localVideo.srcObject = stream
            } 
            else {
               localVideo.src = window.URL.createObjectURL(stream) // for older browsers
            }
   
            localVideo.play();
         }
         
      });


      peer.on('error', (err) => {
         console.log('peer error', err);
      })

      peer.on('close', () => {
         console.log('peer close', 'close');
         Close();
      })
   }


   function Remote(code, PID){

      var samePID = true;

      if( PATIENT.ID != undefined ){
         if( PATIENT.ID != PID ){
            samePID = false;
         }
      }

      if( peer && samePID ){
         data.remoteCode = code;
         peer.signal(data.remoteCode);

         if( data.initiator ){
            console.log('initiator', 'received remote code');
         }
         else{
            console.log('remote', 'received local code');
         }
      }
   }


   function Send(message){
      if( peer ){
         peer.send(message);

         data.messages.push({
            local: true,
            message: message,
            date: new Date()
         })
      }
   }  

 
   function Close(){
      if( peer ){

         if( data.localHttp || data.remoteHttp )
            End_Session();

         PATIENT = [];
         data.isClosed = true;
         data.localHttp = false;
         data.remoteHttp = false;
         data.messages = [];
         data.localCode = [];
         data.remoteCode = [];

         if( peer.streams[0] != undefined ){
            peer.streams[0].getTracks().forEach( (track) => {
               track.stop();
            });
         }

         peer.destroy();
         peer = null;

         
      }
   }



   /// ------------------ http version ----------------------

   function Local_New_Session(){ 

      $timeout(function(){
         
         if( ! data.isClosed ){
            if( ! data.localHttp && data.localCode.sdp != undefined ){
               
               $http.post( global.baseUrl + 'rtc/new-session',{
                  TOKEN: data.TOKEN,
                  USERID: data.USERID,
                  PATIENTID: data.PID,
                  CODE: JSON.stringify(data.localCode)
               }, global.ajaxConfig).then(function(response){

                  data.localHttp = true;
                  
                  Check_Session();
               }, function(err){

                  data.localHttp = false;
                  Local_New_Session();
               });

            }
            else{
               Local_New_Session(); 
            }
         }
      }, 1000 )
   }


   function Check_Session(){

      $timeout(function(){

         $http.post( global.baseUrl + 'rtc/check-session',{
            TOKEN: data.TOKEN,
            USERID: data.USERID,
            PATIENTID: data.PID,
         }, global.ajaxConfig).then(function(response){

            if( response.data.suc.VC != null ){

               if( data.initiator ){

                  if( response.data.suc.VC.REMOTECODE != null ){
                     console.log('local received remote code')

                     var patiID = response.data.suc.VC.PATIENTID != null ? parseInt(response.data.suc.VC.PATIENTID): null;
                     Remote( JSON.parse(response.data.suc.VC.REMOTECODE), patiID);
                  }
                  else{
                     Check_Session(); 
                  }
               }
               else{

                  if( response.data.suc.VC.LOCALCODE != null &&  ! data.remoteHttp ){
                     console.log('remote received local code')

                     var patiID = response.data.suc.VC.PATIENTID != null ? parseInt(response.data.suc.VC.PATIENTID): null;
                     Remote(  JSON.parse(response.data.suc.VC.LOCALCODE), patiID);

                     Remote_Session();
                  } 
                  else{
                     Check_Session();
                  }
               }
            }
            else{
               Check_Session();
            }

         }, function(err){
            Check_Session();
         });

      }, 5000);
   }


   function Remote_Session(){

      $timeout(function(){
         if( ! data.isClosed ){


            if( data.initiator ){

               if( data.remoteCode.sdp == undefined ){

               }
            }
            else{

               if( ! data.remoteHttp && data.localCode.sdp != undefined ){

                  $http.post( global.baseUrl + 'rtc/remote-session',{
                     TOKEN: data.TOKEN,
                     USERID: data.USERID,
                     PATIENTID: data.PID,
                     REMOTECODE: JSON.stringify(data.localCode)
                  }, global.ajaxConfig).then(function(response){
   
                     data.remoteHttp = true;
                     console.log('send local code from remote');

                  }, function(err){
   
                     data.remoteHttp = false;
                     Remote_Session();
                  });

               }
               else{
                  Remote_Session();
               }
            }

         }
      }, 1000 )
   }


   function End_Session(){

      $http.post( global.baseUrl + 'rtc/remote-session',{
         TOKEN: data.TOKEN,
         PATIENTID: data.PID
      }, global.ajaxConfig).then(function(response){

         console.log('end session');

      }, function(err){
         End_Session();
      });

   }  

   
   return{
      Data: function(){
         return data;
      },
      Init: function(initiator , PID, USERID, TOKEN){

         data.PID = PID;
         data.USERID = USERID == undefined ? null : USERID;
         data.TOKEN = TOKEN == undefined ? null : TOKEN;
        
         Init(initiator);
        
      },
      Remote: function(code){
         Remote(code);
      },
      Send: function(message){
         Send(message);
      },
      Close: function(){
         Close();         
      },
      VC_Http: function(){

         if( data.initiator ){
            Local_New_Session();
         }
         else{
            Check_Session();
         }
      }
   }

});


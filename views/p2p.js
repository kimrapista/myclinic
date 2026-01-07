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
      isClosed: false
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

         PATIENT = [];
         data.isClosed = true;
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
      }
   }

});


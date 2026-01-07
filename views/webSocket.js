app.factory('WebSocket', function ($timeout, global) {


   var server;

   var serverUrl	= (window.location.protocol == 'https:' ? 'wss://' : 'ws://') + window.location.hostname  + (window.location.protocol == 'https:' ? ':4433' : ':8080');
   var warningDone = false;
 
 
   function Start(){ 

      server = new WebSocket(serverUrl);

      server.addEventListener('open', ()=>{ 
         console.log('connect'); 
         warningDone = false;
      });	
   
      server.addEventListener('close', ()=>{ 
         console.log('reconnect'); 
         // RECONNECT TIMEOUTE 10 SECONDS 
         $timeout(function(){ Start(); }, 2000);
      });
   
      server.addEventListener('message', e => {
         
         try{

         }
         catch(e){ console.log('invalid json',e); }
      });
   }

   return{
      Start: function(){
         Start();
      },
      Send: function(data){
         
         if( server.readyState == 1 ){
        
            server.send(JSON.stringify(data));
         }
         else{
            if( ! warningDone ){
               global.Toast('WebSocket Server is offline. Any notification wont received');
               warningDone = true;
            }
         }
      },
      Status: function(){
         if( server.readyState ){
            return server.readyState;
         }
         else{
            return 0;
         }
      }
   }
});
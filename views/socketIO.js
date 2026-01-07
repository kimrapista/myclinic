app.factory('SocketIO', function ($timeout, global) {

 
   var socket = io('http://localhost:3000');

   socket.on('myclinic', function(msg){
      console.log(msg);
   });


   return{
      Send: function(data){
         socket.emit('myclinic', data);         
      }
   }
});
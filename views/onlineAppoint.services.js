
app.factory('OnlineAppointServices',function ($http, $q, $filter, global) {

   var isLoading = false;
   var isLoadingScheds = false;

   var data = {
      isLoading: false,
      isLoadingScheds: false,
      DOCTOR: [],
      SCHEDULES: []
   }; 


   function Format_Doctor(detail){

      if( detail.AVATAR == '' || detail.AVATAR == null){
			detail.AVATAR = global.baseUrl + 'assets/css/images/patient_default2.png';
		}
		else{
			detail.AVATAR = global.baseUrl + detail.AVATAR;
      }

      if( detail.BACKGROUNDIMG == '' || detail.BACKGROUNDIMG == null){
         detail.BACKGROUNDIMG = global.baseUrl + 'assets/css/images/background/doctor_chest.jpg';
         
		}
		else{
			detail.BACKGROUNDIMG = global.baseUrl + detail.BACKGROUNDIMG;
      }

      if( detail.LINK == null)
         detail.LINK = '';


      angular.forEach( detail.SUBCLINIC, function(v,k){
         v.LOCATION = v.LOCATION == null ? '' : v.LOCATION;
         v.NAME = v.NAME == null ? '' : v.NAME;
      });  
      
      
      angular.forEach( detail.MEMBERS, function(v,k){
         
         if( v.AVATAR == '' || v.AVATAR == null){
            v.AVATAR = global.baseUrl + 'assets/css/images/patient_default2.png';
         }
         else{
            v.AVATAR = global.baseUrl + v.AVATAR;
         }

         if( v.LINK == null)
            v.LINK = '';

         if( v.POSITION != 'BRANCH ASSISTANT' ){
            if( v.LINK != ''  ){
               v.LINK = global.baseUrl + v.LINK;
            }
            else{
               v.LINK = global.baseUrl + v.ID;
            }
         }

      });

      return detail;
   }



   function Format_Schedules(detail){

      detail.ID = parseInt(detail.ID);

      detail.HOSPITALNAME = detail.HOSPITALNAME == null ? '' : detail.HOSPITALNAME;
      
      if( detail.SDATETIME != null ){
			if ( !angular.isDate(detail.SDATETIME) )
				detail.SDATETIME =  global.Date(detail.SDATETIME);
      }

      detail.MAXPATIENT = parseInt(detail.MAXPATIENT);

      detail.TOTAL_UNVERIFIED = detail.TOTAL_UNVERIFIED == null ? 0 : parseInt(detail.TOTAL_UNVERIFIED);
      detail.TOTAL_ACKNOWLEDGED = detail.TOTAL_ACKNOWLEDGED == null ? 0 : parseInt(detail.TOTAL_ACKNOWLEDGED);

      return detail;
   }


   function Group_Schedules(detail){

      detail = Format_Schedules(detail);

      var found = false;

      var found = $filter('filter')( data.SCHEDULES, function(v,k){
         if( $filter('date')( v.date,'M/d/y') == $filter('date')( detail.SDATETIME,'M/d/y') ){
				return v;
			}
      },true)[0];

      if( ! found ){
         data.SCHEDULES.push({
            date: detail.SDATETIME,
            data: []
         });

         data.SCHEDULES[data.SCHEDULES.length - 1].data.push(detail);
      }
      else{
         found.data.push(detail);
      }

   }


 
   return {
      Data: function(){
         return data;
      },
      Load_Doctor: function(OPTIONS){

         data.isLoading = true;

         return $http.post( global.baseUrl +'landing/doctor-profile', OPTIONS, global.ajaxConfig).then(function(response){ 

            data.isLoading = false;

            if( response.data.err.length == 0 ){

               data.DOCTOR = Format_Doctor(response.data.suc);
               return true;
            }
            else{
               return false; 
            }

         }, function(err){ 
            
            data.isLoading = false;
            return false;
         })
         
      },
      Load_Schedules: function(OPTIONS){

         data.isLoadingScheds = true;

         return $http.post( global.baseUrl +'landing/doctor-schedules', OPTIONS, global.ajaxConfig).then(function(response){ 

            data.SCHEDULES = [];

            angular.forEach( response.data, function(v,k){
               Group_Schedules(v);
            });

            data.isLoadingScheds = false;
            return true;

         }, function(err){ 
            
            data.isLoadingScheds = false;
            return false;
         })
         
      }
      
   }
})

app.factory('LandingServices',function ($http, $q, $filter, global) {


   var data = {
      isSearching: false,
      isDoctorLoading: false,
      DOCTORS: [],
      MAPS: []
   }

   function Format_Doctor(detail){

      if( detail.AVATAR == '' || detail.AVATAR == null){
			detail.AVATAR = global.baseUrl + 'assets/css/images/patient_default2.png';
		}
		else{
			detail.AVATAR = global.baseUrl + detail.AVATAR;
      }
 
      detail.MAXPATIENT = detail.MAXPATIENT == null ? 0 : parseInt(detail.MAXPATIENT);
      detail.TOTAL_ACKNOWLEDGED = detail.TOTAL_ACKNOWLEDGED == null ? 0 : parseInt(detail.TOTAL_ACKNOWLEDGED);
      
      detail.REMAINING = detail.MAXPATIENT - detail.TOTAL_ACKNOWLEDGED;

      if( detail.REMAINING < 0 ){
         detail.REMAINING = 0;
      }

      angular.forEach( detail.LOCATIONS, function(v,k){

         v = Format_Location(v);

         Add_Map(v);
         Add_Map_Doctor(v.ID, detail);
      });

      detail.focusMap = false;

      return detail; 
   }

   
   function Format_Location(detail){

      detail.ID = parseInt(detail.ID);
      detail.COORDLONG_0 = detail.COORDLONG_0 == null ? null : parseFloat(detail.COORDLONG_0);
      detail.COORDLONG_1 = detail.COORDLONG_1 == null ? null : parseFloat(detail.COORDLONG_1);
      detail.COORDSHORT_0 = detail.COORDSHORT_0 == null ? null : parseFloat(detail.COORDSHORT_0);
      detail.COORDSHORT_1 = detail.COORDSHORT_1 == null ? null : parseFloat(detail.COORDSHORT_1);

      return detail;
   }


   function Add_Map(LOCATION){

      var temp = $filter('filter')( data.MAPS,{ID:LOCATION.ID}, true )[0];

      if( temp == undefined ){
         data.MAPS.push({
            ID: LOCATION.ID,
            NAME: 'SUBCLINIC_'+LOCATION.ID,
            COORDINATEL: [LOCATION.COORDLONG_0, LOCATION.COORDLONG_1],
            COORDINATES: [LOCATION.COORDSHORT_0, LOCATION.COORDSHORT_1],
            DOCTORS: [],
            isFocus: false
         })
      }
   }


   function Add_Map_Doctor(LOCATIONID, DOCTOR){

      for (var i = 0; i < data.MAPS.length; i++) {
         var temp = data.MAPS[i];
         
         if( temp.ID == LOCATIONID ){

            if( $filter('filter')( temp.DOCTORS,{ DOCTORID: DOCTOR.ID}, true)[0] == undefined ){
               temp.DOCTORS.push({
                  DOCTORID: DOCTOR.ID
               });
            }
         }
      }
   }


   return {
      Load_Doctors: function(OPTIONS){

         data.isDoctorLoading = true;

         return $http.post( global.baseUrl +'landing/search-doctor', OPTIONS, global.ajaxConfig).then(function(response){ 

            data.DOCTORS = [];
            angular.forEach( response.data, function(v,k){
               data.DOCTORS.push(Format_Doctor(v));
            });

            data.isDoctorLoading = false;
            
            return true;

         }, function(err){ 
            
            data.isDoctorLoading = false;
            return false;
         })
         
      },
      Reload_Clinics: function(){

         if( localStorage.getItem('MAPS') == undefined ){
            return this.Load_Clinics();
         }
         else{
            var deferred = $q.defer();
            var temp = JSON.parse( localStorage.getItem('MAPS'));
            
            if( $filter('date')( temp.date, 'MMMM/d/y') == $filter('date')( new Date(), 'MMMM/d/y') ){
               data.MAPS = temp.data;
                              
               deferred.resolve(true);
            }
            else{
               deferred.resolve(this.Load_Clinics());
            }

            return deferred.promise;
         }

      },
      Load_Clinics: function(){

         data.isDoctorLoading = true;

         return $http.get( global.baseUrl +'landing/clinics-location', global.ajaxConfig).then(function(response){ 

            angular.forEach( response.data, function(v,k){
               v = Format_Location(v);
               Add_Map(v);
            });

            if( localStorage.getItem('MAPS') == undefined )
            localStorage.setItem('MAPS',  JSON.stringify({ date: new Date(), data: data.MAPS}));

            data.isDoctorLoading = false;
            
            return true;

         }, function(err){ 
            
            data.isDoctorLoading = false;
            return false;
         })
         
      },
      Data: function(){
         return data;
      }
   }
})
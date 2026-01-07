'use strict';

app.factory('NotifyServices',function ($http, $q, $filter, global) {

   var data = {
      APPOINTMENTS: [],
      SCHEDULES: [],
      NEWPATIENTS: [],
      REVISITS: [],
      isLoadingPatients: false,
      ADVISORY: [],
      isLoadingAdvisory: false
   };
   
   function Format_Appointment(detail){
      detail.ID = parseInt(detail.ID);

      detail.MRID = detail.MRID == null ? null : parseInt(detail.MRID);

      return detail;
   }

   function Format_Schedule(detail){
      detail.ID = parseInt(detail.ID);
      detail.MRID = detail.MRID == null ? null : parseInt(detail.MRID);

      return detail;
   }

   function Format_New_Patient(detail){
      detail.ID = parseInt(detail.ID);

      return detail;
   }

   function Format_Revisit(detail){
      detail.ID = parseInt(detail.ID);

      return detail;
   }


   return{
      Data: function(){
         return data;
      },
      Load_Today_Patients: function(){

         data.isLoadingPatients = true;

         return $http.get( global.baseUrl + 'notify/today-patients', global.ajaxConfig).then(function(respsonse){
            
            data.APPOINTMENTS = [];
            data.SCHEDULES = [];
            data.NEWPATIENTS = [];
            data.REVISITS= [];
                        
            angular.forEach( respsonse.data.APPOINTMENTS, function(v,k){
               data.APPOINTMENTS.push(Format_Appointment(v));
            })

            angular.forEach( respsonse.data.SCHEDULES, function(v,k){
               data.SCHEDULES.push(Format_Schedule(v));
            })

            angular.forEach( respsonse.data.NEWPATIENTS, function(v,k){
               data.NEWPATIENTS.push(Format_New_Patient(v));
            })

            angular.forEach( respsonse.data.REVISITS, function(v,k){
               v = Format_Revisit(v);

               if( ! $filter('filter')( data.APPOINTMENTS, {ID: v.ID},true)[0] && ! $filter('filter')( data.SCHEDULES, {ID: v.ID},true)[0] && ! $filter('filter')( data.NEWPATIENTS, {ID: v.ID},true)[0] ){
                  data.REVISITS.push(v);
               }

            })

            data.isLoadingPatients = false;
            return true;

         },function(err){
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isLoadingPatients = false;
            return false;
         });
      },
      Reload_Today_Patients: function(){

         if( data.APPOINTMENTS.length == 0 && data.SCHEDULES.length == 0 ){

				return this.Load_Today_Patients();
			}
			else{
				// return true means already loaded
				var deferred = $q.defer();
				deferred.resolve(true);
				return deferred.promise;
			}
      },
      Update_Today_Patients: function(PATIENT,MRID){

         var found = false;

         angular.forEach( data.APPOINTMENTS, function(v,k){
            if( v.ID == PATIENT.ID ){
               v.MRID = MRID;
               found = true;
            }
         })

         angular.forEach( data.SCHEDULES, function(v,k){
            if( v.ID == PATIENT.ID ){
               v.MRID = MRID;
               found = true;
            }
         })

         angular.forEach( data.NEWPATIENTS, function(v,k){
            if( v.ID == PATIENT.ID ){
               v.MRID = MRID;
               found = true;
            }
         })

         angular.forEach( data.REVISITS, function(v,k){
            if( v.ID == PATIENT.ID ){
               v.MRID = MRID;
               found = true;
            }
         })

         if( ! found ){
            data.REVISITS.push({
               ID: PATIENT.ID,
               FIRSTNAME: PATIENT.FIRSTNAME,
               MIDDLENAME: PATIENT.MIDDLENAME,
               LASTNAME: PATIENT.LASTNAME,
               MRID: MRID
            })
         }
      }
      
   }
});
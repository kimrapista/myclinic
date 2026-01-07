
app.factory('DashboardServices',function ($http, global) {

   var urlDoctors = global.baseUrl + 'dashboard/doctors';
   var urlClinics = global.baseUrl + 'dashboard/clinics';
	 
	var data = {
      DOCTORS: [],
      isDoctors: false,
      CLINICS: [],
      isClinics: false
   }


	return {
      Load_Clinics: function(OPTION){

         data.isClinics = true;

			return $http.post( urlClinics, OPTION, global.ajaxConfig) .then( function(response) {

            data.CLINICS = response.data;

            angular.forEach( data.CLINICS, function(v,k){
               v.CLINICID = parseInt(v.CLINICID);
               v.SERVED = v.SERVED == null ? 0 : parseInt(v.SERVED);
               v.NETPAYABLES = v.NETPAYABLES == null ? 0 : parseFloat(v.NETPAYABLES);
            });
          
            data.isClinics = false;
				return true;
				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isClinics = false;
				return false;
			});

      },
      Load_Doctors: function(OPTION){

         data.isDoctors = true;

			return $http.post( urlDoctors, OPTION, global.ajaxConfig) .then( function(response) {

            data.DOCTORS = response.data;

            angular.forEach( data.DOCTORS, function(v,k){
               v.CLINICID = parseInt(v.CLINICID);
               v.SERVED = v.SERVED == null ? 0 : parseInt(v.SERVED);
               v.NETPAYABLES = v.NETPAYABLES == null ? 0 : parseFloat(v.NETPAYABLES);
            });
          
            data.isDoctors = false;
				return true;
				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isDoctors = false;
				return false;
			});

      },
		Data: function(){

			if( data != undefined ){
				return data;
			}
			else{
				return [];
			}
      }
		
	}
	
});

app.factory('PatientRecordServices',function ($http, $q, $location, global) {

	var urlPatientRemove =  global.baseUrl + 'patients/submit-remove-patient';
	var urlPatient = global.baseUrl + 'patients/patient-information/';
	var dataPatient = [];

	var urlMRRemove =  global.baseUrl + 'patients/submit-remove-record';
	var urlMR = global.baseUrl + 'patients/patient-medical-record/';
	var dataMR = [];
	

	function Format_Patient(patient){

		patient.ID = parseInt(patient.ID);

		if( patient.DATEREG != null ){
			if ( !angular.isDate(patient.DATEREG) )
				patient.DATEREG =  global.Date(patient.DATEREG);
		}

		if( patient.DOB != null ){
			if ( !angular.isDate(patient.DOB) )
				patient.DOB =  global.Date(patient.DOB);
		}

		if( patient.PICTURE == '' || patient.PICTURE == null)
			patient.PICTURE = global.baseUrl + 'assets/css/images/patient_default.png';

		return patient;
	}


	function Format_MR(detail){

		detail.ID = parseInt(detail.ID);

		if( detail.CHECKUPDATE != null ){
			if ( !angular.isDate(detail.CHECKUPDATE) )
				detail.CHECKUPDATE =  global.Date(detail.CHECKUPDATE);
		}

		if ( typeof detail.APPOINTMENT != 'boolean' )
			detail.APPOINTMENT = detail.APPOINTMENT == 'Y' ? true : false;

		if ( typeof detail.APPOINTMENT != 'boolean' )
			detail.APPOINTMENT = detail.APPOINTMENT == 'Y' ? true : false;

		if( detail.APPOINTMENT ){

			if( detail.APPOINTMENTDATE != null ){
				if ( !angular.isDate(detail.APPOINTMENTDATE) )
					detail.APPOINTMENTDATE =  global.Date(detail.APPOINTMENTDATE);
			}
		}

		if ( typeof detail.APPOINTMENTSERVED != 'boolean' )
			detail.APPOINTMENTSERVED = detail.APPOINTMENTSERVED == 'Y' ? true : false;

		detail.NETPAYABLES = parseFloat(detail.NETPAYABLES);
		detail.TOTAL_LAB = parseInt(detail.TOTAL_LAB);

		detail.SERVICES.forEach((v)=>{
			v.AMOUNT = v.AMOUNT == null ? 0 : parseFloat(v.AMOUNT);
		})

		detail.isSubmit = false;
		detail.isCancelling = false;

		return detail;
	} 


	return {
		Data_Patient: function(){

			return dataPatient;

		},
		Load_Patient: function(PATIENTID){

			dataMR = [];

			return $http.get( urlPatient + PATIENTID, global.ajaxConfig) .then( function(response) {

				// if deleted or other clinic
				if( response.data.error ){
					global.Alert(response.data.error);
					$location.url('/patients');
					return false;
				}
				
				dataPatient = Format_Patient(response.data);

				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Remove_Patient: function(OPTIONS){

			return $http.post( urlPatientRemove, OPTIONS, global.ajaxConfig) .then( function(response) {

				if( response.data.err ){
					global.Alert(response.data.err);
					return false;
				}
				else{
					global.Toast('Deleted');
					$location.url('/patients');
					return true;
				}

				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Data_MR: function(){

			if( dataMR != undefined ){
				return dataMR;
			}
			else{
				return [];
			}
			
		},
		Load_MR: function(OPTIONS){

			if( OPTIONS.FROM == 0 )
				dataMR = [];

			return $http.post( urlMR, OPTIONS, global.ajaxConfig) .then( function(response) {

				angular.forEach( response.data, function (v, k) { 
					dataMR.push(Format_MR(v)); 
				});

				if( response.data.length == 0 ){

					if( OPTIONS.FROM > 0 ){
						global.Toast('No more result');
					}

					return false;
				}
				else{
					return true;
				}

				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Remove_MR: function(OPTIONS){

			return $http.post( urlMRRemove, OPTIONS, global.ajaxConfig) .then( function(response) {

				if( response.data.err ){
					global.Alert(response.data.err);
					return false;
				}
				else{
					global.Toast('Deleted');
					return true;
				}

				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Reload: function(OPTIONS){

			if( data.length == 0 ){

				return this.Load(OPTIONS);
			}
			else{
				// return true means already loaded
				var deferred = $q.defer();
				deferred.resolve(true);
				return deferred.promise;
			}
		},
		
		Update: function( DETAIL){

			if( DETAIL != null ){

				//detail = Format(detail);
				var found = false;

				for (var i = 0; i < data.length ; i++) {
					if( data[i].ID == DETAIL.ID ){
						data[i] = DETAIL;
						found = true;
					}
				}

				if( found == false )
					data.push(DETAIL);
			}
			
		},
		Remove: function(ID){

			var key = null;

			for (var i = 0; i < data.length; i++) {
				if( data[i].ID == ID ){
					key = i;
					i = data.length + 100;
				}
			}

			if( key != null )
				data.splice(key,1);
		}		
	}
	
});
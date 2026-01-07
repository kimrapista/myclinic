
app.factory('MeServices',function ($http, global) {

	var data = {
		SALES: localStorage.getItem('SALES') != undefined ? (localStorage.getItem('SALES') == 'true' ? true : false) : false,
		PIRANI: localStorage.getItem('PIRANI') != undefined ? (localStorage.getItem('PIRANI') == 'true' ? true : false) : false, 
		OPHTHALMOLOGIST: localStorage.getItem('OPHTHALMOLOGIST') != undefined ? (localStorage.getItem('OPHTHALMOLOGIST') == 'true' ? true : false) : false, 
		OPTICAL: localStorage.getItem('OPTICAL') != undefined ? (localStorage.getItem('OPTICAL') == 'true' ? true : false) : false, 
		ISADDSERVICES: localStorage.getItem('ISADDSERVICES') != undefined ? (localStorage.getItem('ISADDSERVICES') == 'true' ? true : false) : false, 
		ISDISPLAYSERVICESAMOUNT: localStorage.getItem('ISDISPLAYSERVICESAMOUNT') != undefined ? (localStorage.getItem('ISDISPLAYSERVICESAMOUNT') == 'true' ? true : false) : false, 
		isAdmin: localStorage.getItem('isAdmin') != undefined ? (localStorage.getItem('isAdmin') == 'true' ? true : false) : false
	};


	function Format(detail){

		detail.ID = parseInt(detail.ID);
		detail.CLINICID = parseInt(detail.CLINICID);
		detail.SUBCLINICID = parseInt(detail.SUBCLINICID);

		if( detail.SPECIALISTID != null  )
			detail.SPECIALISTID = parseInt(detail.SPECIALISTID);


		if ( typeof detail.SALES != 'boolean' )
			detail.SALES = detail.SALES == 'Y' ? true : false;

		if ( typeof detail.BLAST != 'boolean' )
			detail.BLAST = detail.BLAST == 'Y' ? true : false;

		if ( typeof detail.PIRANI != 'boolean' )
			detail.PIRANI = detail.PIRANI == 'Y' ? true : false;

		if ( typeof detail.OPHTHALMOLOGIST != 'boolean' )
			detail.OPHTHALMOLOGIST = detail.OPHTHALMOLOGIST == 'Y' ? true : false;

		if ( typeof detail.OPTICAL != 'boolean' )
			detail.OPTICAL = detail.OPTICAL == 'Y' ? true : false;

		if ( typeof detail.SETUP_CLINIC_ROMERO != 'boolean' )
			detail.SETUP_CLINIC_ROMERO = detail.SETUP_CLINIC_ROMERO == 'Y' ? true : false;
			
		if ( typeof detail.AUTHORIZATION != 'boolean' )
			detail.AUTHORIZATION = detail.AUTHORIZATION == 'Y' ? true : false;

		if ( typeof detail.EDITMR != 'boolean' )
			detail.EDITMR = detail.EDITMR == 'Y' ? true : false;

		if ( typeof detail.ISADDSERVICES != 'boolean' )
			detail.ISADDSERVICES = detail.ISADDSERVICES == 'Y' ? true : false;

		if ( typeof detail.ISDISPLAYSERVICESAMOUNT != 'boolean' )
			detail.ISDISPLAYSERVICESAMOUNT = detail.ISDISPLAYSERVICESAMOUNT == 'Y' ? true : false;

		if ( typeof detail.ISONLINE != 'boolean' )
			detail.ISONLINE = detail.ISONLINE == 'Y' ? true : false;


		if( detail.AVATAR == '' || detail.AVATAR == null){
			detail.AVATAR = global.baseUrl + 'assets/css/images/patient_default2.png';
			detail.isAvatar = false;
		}
		else{
			detail.AVATAR = global.baseUrl + '/' + detail.AVATAR;
			detail.isAvatar = true;
		}

		
		if( detail.POSITION == 'ADMINISTRATOR' ){

			detail.isSuperAdmin = true;
			detail.isAdmin = false;
			detail.isDoctor = false;
			detail.isConsultant = false;
			detail.isResident = false;
			detail.isAssistant = false;
		}

		else if( detail.POSITION == 'BRANCH ADMINISTRATOR' ){

			detail.isSuperAdmin = false;
			detail.isAdmin = true;
			detail.isDoctor = true;
			detail.isConsultant = false;
			detail.isResident = false;
			detail.isAssistant = false;
		}
		else if( detail.POSITION == 'BRANCH CONSULTANT'){

			detail.isSuperAdmin = false;
			detail.isAdmin = false;
			detail.isDoctor = true;
			detail.isConsultant = true;
			detail.isResident = false;
			detail.isAssistant = false;
		}
		else if( detail.POSITION == 'BRANCH RESIDENT'){

			detail.isSuperAdmin = false;
			detail.isAdmin = false;
			detail.isDoctor = true;
			detail.isConsultant = false; 
			detail.isResident = true;
			detail.isAssistant = false;
		}
		else if( detail.POSITION == 'BRANCH ASSISTANT' ){
			detail.isSuperAdmin = false;
			detail.isAdmin = false;
			detail.isDoctor = false;
			detail.isConsultant = false;
			detail.isResident = false;
			detail.isAssistant = true;
		}


		detail.isUploading = false;
		
		localStorage.setItem('SALES', detail.SALES);
		localStorage.setItem('PIRANI', detail.PIRANI);
		localStorage.setItem('OPHTHALMOLOGIST', detail.OPHTHALMOLOGIST);
		localStorage.setItem('OPTICAL', detail.OPTICAL);
		localStorage.setItem('ISADDSERVICES', detail.ISADDSERVICES);
		localStorage.setItem('ISDISPLAYSERVICESAMOUNT', detail.ISDISPLAYSERVICESAMOUNT);
		localStorage.setItem('isAdmin', detail.isAdmin);

		detail.isSubmit = false; 

		return detail;
	}


	return {
		Form: function(){

			return $http.get( global.baseUrl + 'account/my-account', global.ajaxConfig) .then( function(response) {

				if( response.data.err ){
					data = [];
					return false;
				}
				else{
					data = Format(response.data.suc);
					return true;	
				}
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Data: function(){
			return data;
		},
		New_Crop: function(base64){

			data.isUploading = true;

			return $http.post( global.baseUrl + 'account/submit-crop',{ 
				base64: base64
			}, global.ajaxConfig) .then( function(response) {

				data.isUploading = false;
				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				data.isUploading = false;
				return false;
			});
		}
	}
	
});
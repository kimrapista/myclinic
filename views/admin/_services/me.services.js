
app.factory('MeServices',function ($http, $q, $filter, $timeout, $interval, $location, global) {


	var urlForm = global.baseUrl + 'account/my-account';
	var urlRelogin = global.baseUrl + 'account/submit-relogin';
	
	var data = {
		SALES: localStorage.getItem('SALES') != undefined ? (localStorage.getItem('SALES') == 'true' ? true : false) : false, 
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

			localStorage.setItem('SALES', detail.SALES);

		if ( typeof detail.BLAST != 'boolean' )
			detail.BLAST = detail.BLAST == 'Y' ? true : false;

		if ( typeof detail.AUTHORIZATION != 'boolean' )
			detail.AUTHORIZATION = detail.AUTHORIZATION == 'Y' ? true : false;

		if ( typeof detail.EDITMR != 'boolean' )
			detail.EDITMR = detail.EDITMR == 'Y' ? true : false;

		if( detail.AVATAR == '' || detail.AVATAR == null)
			detail.AVATAR = global.baseUrl + 'assets/css/images/patient_default2.png';

		
			
		if( detail.POSITION == 'BRANCH ADMINISTRATOR' ){
			detail.isAdmin = true;
		}
		else{
			detail.isAdmin = false;
		}

		localStorage.setItem('isAdmin', detail.isAdmin);

		if( detail.POSITION == 'BRANCH ASSISTANT' ){
			detail.isAssistant = true;
			detail.isDoctor = false;
		}
		else{
			detail.isDoctor = true;
			detail.isAssistant = false;
		}

		detail.isSubmit = false;

		return detail;
	}


	return {
		Form: function(){

			return $http.get( urlForm, global.ajaxConfig) .then( function(response) {

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
		}
	}
	
});

app.factory('PatientFormServices',function ($http, $q, $filter, $timeout, $interval, $location, global) {


	var urlForm = global.baseUrl + 'patients/form-data/';
	


	function Format(detail){

		detail.ID = parseInt(detail.ID);

		if( detail.DATEREG != null ){
			if ( !angular.isDate(detail.DATEREG) )
				detail.DATEREG =  global.Date(detail.DATEREG);
		}

		if( detail.DOB != null ){
			if ( !angular.isDate(detail.DOB) )
				detail.DOB =  global.Date(detail.DOB);
		}

		if( detail.CIVILSTATUS == '')
			detail.CIVILSTATUS = null;

		detail.isSubmit = false;

		return detail;
	}


	return {
		Form: function(ID){

			return $http.get( urlForm + ID, global.ajaxConfig) .then( function(response) {

				// if deleted or other clinic
				if( response.data.error ){
					global.Alert(response.data.error);
					$location.url('/patients');
					return false;
				}

				return Format(response.data);
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		}
	}
	
});
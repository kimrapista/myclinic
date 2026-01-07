
app.factory('UsersServices',function ($http, $q, $filter, $timeout, $interval, global) {

	var url = global.baseUrl + 'settings/users/index';
	var urlForm = global.baseUrl + 'settings/users/form-data/';
	var urlFormActive = global.baseUrl + 'settings/users/submit-active';
	var urlFormPassword = global.baseUrl + 'settings/users/submit-reset-password';
	
	var data = [];


	function Format(detail){

		detail.ID = parseInt(detail.ID);
		detail.CLINICID = parseInt(detail.CLINICID);
		
		if ( typeof detail.AUTHORIZATION != 'boolean' )
			detail.AUTHORIZATION = detail.AUTHORIZATION == 'Y' ? true : false;


		if ( typeof detail.EDITMR != 'boolean' )
			detail.EDITMR = detail.EDITMR == 'Y' ? true : false;

		if ( typeof detail.ISADDSERVICES != 'boolean' )
			detail.ISADDSERVICES = detail.ISADDSERVICES == 'Y' ? true : false;

		if ( typeof detail.ISDISPLAYSERVICESAMOUNT != 'boolean' )
			detail.ISDISPLAYSERVICESAMOUNT = detail.ISDISPLAYSERVICESAMOUNT == 'Y' ? true : false;

		//cancelled act as Active field
		if ( typeof detail.CANCELLED != 'boolean' )
			detail.CANCELLED = detail.CANCELLED == 'Y' ? true : false;

		detail.ACTIVE = detail.CANCELLED ? false : true;


		if( detail.POSITION == 'BRANCH ASSISTANT' ){
			detail.isAssistant = true;
			detail.isDoctor = false;
		}
		else{
			detail.isDoctor = true;
			detail.isAssistant = false;
		}
		if( detail.POSITION == 'BRANCH ADMINISTRATOR' ){
			detail.isAssistant = false;
			detail.isDoctor = true;
		}
		else{
			detail.isDoctor = false;
			detail.isAssistant = true;
		}


		detail.isSubmit 		= false;
		detail.isActivating 	= false;
		detail.isReseting 		= false;

		return detail;
	}


	return {
		Load: function(OPTIONS){

			if( OPTIONS == undefined)
				OPTIONS = {};

			return $http.post( url, OPTIONS, global.ajaxConfig) .then( function(response) {

				data = [];
				data = response.data;

				angular.forEach( data, function (v, k) { v = Format(v); });

				return true;
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
		Form: function(ID){

			return $http.get( urlForm + ID, global.ajaxConfig) .then( function(response) {

				return Format(response.data);
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Form_Active: function(OPTIONS){

			return $http.post( urlFormActive, OPTIONS, global.ajaxConfig) .then( function(response) {

				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
		Form_Password: function(OPTIONS){

			return $http.post( urlFormPassword, OPTIONS, global.ajaxConfig) .then( function(response) {

				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
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

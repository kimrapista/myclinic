
app.factory('AppointmentsServices',function ($http, $q, global) {

	var url = global.baseUrl + 'appointments/search-appontments';
	
	var data = [];


	function Format(detail){

		detail.ID = parseInt(detail.ID);

		if( detail.CHECKUPDATE != null ){
			if ( !angular.isDate(detail.CHECKUPDATE) )
				detail.CHECKUPDATE =  global.Date(detail.CHECKUPDATE);
		}

		if ( typeof detail.APPOINTMENT != 'boolean' )
			detail.APPOINTMENT = detail.APPOINTMENT == 'Y' ? true : false;

		if( detail.APPOINTMENT ){

			if( detail.APPOINTMENTDATE != null ){
				if ( !angular.isDate(detail.APPOINTMENTDATE) )
					detail.APPOINTMENTDATE =  global.Date(detail.APPOINTMENTDATE);
			}
		}

		detail.TOTAL_LAB = parseInt(detail.TOTAL_LAB);
		detail.NETPAYABLES = parseFloat(detail.NETPAYABLES);

		return detail;
	}


	return {
		Load: function(OPTIONS){

			if( OPTIONS.FROM == 0 )
				data = [];

			return $http.post( url, OPTIONS, global.ajaxConfig) .then( function(response) {

				angular.forEach( response.data, function (v, k) { 
					data.push(Format(v)); 
				});

				if( response.data.length == 0 ){

					if( OPTIONS.FROM == 0 && OPTIONS.SEARCH.length > 0 ){

						global.Toast("Search '"+ OPTIONS.SEARCH +"' not found");
						return false;
					}
					else if( OPTIONS.FROM > 0 ){
						// global.Toast('No more result');
						return false;
					}
					else{
						return false;
					}
				}
				else{
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
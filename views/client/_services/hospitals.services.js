
app.factory('HospitalsServices',function ($http, $q, global) {

	var url = global.baseUrl + 'hospitals/index';
	
	var data = [];
	var isLoading = false;


	function Format(detail){

		detail.ID = parseInt(detail.ID);
	
		detail.isSubmit = false;

		return detail;
	}


	return {
		Load: function(){
			
			isLoading = true;

			return $http.post( url, {}, global.ajaxConfig) .then( function(response) {

				data = [];
				data = response.data;

				angular.forEach( data, function (v, k) { v = Format(v); });
				
				isLoading = false;
				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				isLoading = false;
				return false;
			});

		},
		Reload: function(){

			if( data.length == 0 ){

				return this.Load();
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
		Status: function(){
			return isLoading;
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
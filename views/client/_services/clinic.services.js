
app.factory('ClinicServices',function ($http, $q, global) {

	var url = global.baseUrl + 'clinics/index';
	var urlForm = global.baseUrl + 'clinics/form-data/';
	
   var data = [];
   var isLoading = false;

   function Format(detail){

      return detail;
   }

	return {
		Load: function(){
         isLoading = true;

			return $http.post( url,{}, global.ajaxConfig) .then( function(response) {

				data = Format(response.data);
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
		Form: function(){

			return $http.get( urlForm, global.ajaxConfig) .then( function(response) {
            data = Format(response.data);
				return data;
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
      Status: function(){
         return isLoading;
      },
		Update: function(DATA){

			data = DATA;
			
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
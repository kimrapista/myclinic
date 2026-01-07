
app.factory('ICDServices',function ($http, $q, global) {


	var data = [];

   var isLoading = false; 

	return {
		Load: function(){
         isLoading = true;

			return $http.get(  global.baseUrl + 'doh/icd', global.ajaxConfig) .then( function(response) {

				data = [];
				data = response.data;

				localStorage.setItem('ICD', JSON.stringify(data));

            isLoading = false;
				return true;
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            isLoading = false;
				return false;
			});

		},
		Reload: function(OPTIONS){

			if( data.length == 0 ){

				
				if( localStorage.getItem('ICD') ){
					data = JSON.parse(localStorage.getItem('ICD'));

					var deferred = $q.defer();
					deferred.resolve(true);
					return deferred.promise;
				}
				else{
					return this.Load(OPTIONS);
				}

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
      }
	}
	
});
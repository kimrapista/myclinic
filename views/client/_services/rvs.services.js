
app.factory('RVSServices',function ($http, $q, global) {

	
	var data = [];

   var isLoading = false; 

	return {
		Load: function(){
         isLoading = true;

			return $http.get(  global.baseUrl + 'doh/rvs', global.ajaxConfig) .then( function(response) {

				data = [];
				data = response.data;

				localStorage.setItem('RVS', JSON.stringify(data));
                      
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

				if( localStorage.getItem('RVS') ){
					data = JSON.parse(localStorage.getItem('RVS'));

					var deferred = $q.defer();
					deferred.resolve(true);
					return deferred.promise;
				}
				else{
					return this.Load(OPTIONS);
				}				
			}
			else{
				
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
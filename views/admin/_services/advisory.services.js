
app.factory('AdvisoryServices',function ($http, $q, global) {
	
   var data = [];
   var isLoading = false;
 
   function Format(detail){

		detail.ID = parseInt(detail.ID);

		if ( typeof detail.POST != 'boolean' )
			detail.POST = detail.POST == 'Y' ? true : false;

		if( detail.POSTDATE != null ){
			if ( !angular.isDate(detail.POSTDATE) )
				detail.POSTDATE =  global.Date(detail.POSTDATE);
		}


		if ( typeof detail.CANCELLED != 'boolean' )
			detail.CANCELLED = detail.CANCELLED == 'Y' ? true : false;

      return detail;
   }


 
	return {
		Load: function(){
         isLoading = true;

			return $http.get( global.baseUrl + 'advisory/index', global.ajaxConfig) .then( function(response) {

				data = [];
				angular.forEach( response.data, function(v,k){
					data.push(Format(v));
				})

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
		Form: function(ID){

			return $http.post( global.baseUrl + 'advisory/form-data', {ID:ID}, global.ajaxConfig) .then( function(response) {
            return Format(response.data);
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

			var found = false;

			angular.forEach( data, function(v,k){
				if( DATA.ID == v.ID ){
					data[k] = angular.copy(DATA);
					found = true;
				}
			});
			
			if( ! found ){
				DATA.USERS = [];
				data.push(DATA);
			}
		}	
	}
	
});
app.factory('ChangelogServices',function ($http, $q, global) {

   var data = {
      datas: [],
      isLoading: false
   }

   function Format(detail){

      if( detail.POSTDATE != null ){
			if ( !angular.isDate(detail.POSTDATE) )
				detail.POSTDATE =  global.Date(detail.POSTDATE);
		}


      return detail;
   }


   return {
      Data: function(){
         return data;
      },
      Load: function(){

         data.isLoading = true;

         return $http.get( global.baseUrl + 'advisory/latest-news', global.ajaxConfig) .then( function(response) {

            data.datas = [];
            
            angular.forEach( response.data, function(v,k){
               data.datas.push(Format(v));
            });
            
            data.isLoading = false;
            return true;
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isLoading = false;
				return false;
			});

      },
      Reload: function(){

			if( data.datas.length == 0 ){

				return this.Load();
			}
			else{
				// return true means already loaded
				var deferred = $q.defer();
				deferred.resolve(true);
				return deferred.promise;
			}
		}
   }


});
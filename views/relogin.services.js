
app.factory('ReloginServices',function ($http, global, MeServices) {

	var urlRelogin = global.baseUrl + 'account/submit-relogin';
	
	return {
		Form: function(){

			return $http.post( urlRelogin, {TOKEN: MeServices.Data().TOKEN}, global.ajaxConfig) .then( function(response) {

            // call again the account info
				MeServices.Form().then(function(data){
               global.Alert('Auto sign in and please refresh or re-action your work', 'System');
            });

				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});
			
		}
		
	}
	
});
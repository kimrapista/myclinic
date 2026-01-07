
app.factory('ClinicsServices',function ($http, $q, global) {
	
   var data = [];
   var isLoading = false;
 
   function Format(detail){

		detail.ID = parseInt(detail.ID);

		if ( typeof detail.SALES != 'boolean' )
			detail.SALES = detail.SALES == 'Y' ? true : false;

		if ( typeof detail.BLAST != 'boolean' )
			detail.BLAST = detail.BLAST == 'Y' ? true : false;

		if ( typeof detail.PIRANI != 'boolean' )
			detail.PIRANI = detail.PIRANI == 'Y' ? true : false;

		if ( typeof detail.OPHTHALMOLOGIST != 'boolean' )
			detail.OPHTHALMOLOGIST = detail.OPHTHALMOLOGIST == 'Y' ? true : false;

		if ( typeof detail.OPTICAL != 'boolean' )
			detail.OPTICAL = detail.OPTICAL == 'Y' ? true : false;

		if ( typeof detail.SETUP_CLINIC_ROMERO != 'boolean' )
			detail.SETUP_CLINIC_ROMERO = detail.SETUP_CLINIC_ROMERO == 'Y' ? true : false;
			
      return detail;
   }


	function Format_Report(detail){

		detail.ID = parseInt(detail.ID);
		detail.CLINICID = parseInt(detail.CLINICID);

		detail.WIDTH = parseFloat(detail.WIDTH);
		detail.HEIGHT = parseFloat(detail.HEIGHT);
		detail.MARGINTOP = parseFloat(detail.MARGINTOP);
		detail.MARGINLEFT = parseFloat(detail.MARGINLEFT);

		if( detail.DETAIL == undefined )
		detail.DETAIL = [];

		angular.forEach( detail.DETAIL, function(v,k){
			v.COORDX = parseFloat(v.COORDX);
			v.COORDY = parseFloat(v.COORDY);
			v.WIDTH = parseFloat(v.WIDTH);
			v.HEIGHT = parseFloat(v.HEIGHT);

			v.FONTSIZE = parseFloat(v.FONTSIZE);

			v.FILLCOLOR1 = parseFloat(v.FILLCOLOR1);
			v.FILLCOLOR2 = parseFloat(v.FILLCOLOR2);
			v.FILLCOLOR3 = parseFloat(v.FILLCOLOR3);

			v.TEXTCOLOR1 = parseFloat(v.TEXTCOLOR1);
			v.TEXTCOLOR2 = parseFloat(v.TEXTCOLOR2);
			v.TEXTCOLOR3 = parseFloat(v.TEXTCOLOR3);

			if ( typeof v.FONTBOLD != 'boolean' )
			v.FONTBOLD = v.FONTBOLD == 'Y' ? true : false;

			if ( typeof v.BORDERT != 'boolean' )
			v.BORDERT = v.BORDERT == 'Y' ? true : false;

			if ( typeof v.BORDERR != 'boolean' )
			v.BORDERR = v.BORDERR == 'Y' ? true : false;

			if ( typeof v.BORDERB != 'boolean' )
			v.BORDERB = v.BORDERB == 'Y' ? true : false;

			if ( typeof v.BORDERL != 'boolean' )
			v.BORDERL = v.BORDERL == 'Y' ? true : false;

			v.BORDERCOLOR1 = parseFloat(v.BORDERCOLOR1);
			v.BORDERCOLOR2 = parseFloat(v.BORDERCOLOR2);
			v.BORDERCOLOR3 = parseFloat(v.BORDERCOLOR3);

			if ( typeof v.CANCELLED != 'boolean' )
			v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
		});

		return detail;
	}
 
	return {
		Load: function(){
         isLoading = true;

			return $http.post( global.baseUrl + 'clinics/index',{}, global.ajaxConfig) .then( function(response) {

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

			return $http.post( global.baseUrl + 'clinics/form-data/', {ID:ID}, global.ajaxConfig) .then( function(response) {
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
					var users = angular.copy(v.USERS);
					data[k] = angular.copy(DATA);
					data[k].USERS = users;
					
					found = true;
				}
			});
			
			if( ! found ){
				DATA.USERS = [];
				data.push(DATA);
			}
		},
		Load_Report: function(ID,TITLE){

			return $http.post( global.baseUrl + 'clinics/report-forms', {CLINICID:ID, TITLE:TITLE}, global.ajaxConfig) .then( function(response) {
            return Format_Report(response.data);
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});
		}		
	}
	
});
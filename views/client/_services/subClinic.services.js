
app.factory('SubClinicServices',function ($http, $q, global) {

	var url = global.baseUrl + 'clinics/subclinic/index';
	var urlForm = global.baseUrl + 'clinics/subclinic/form-data/';
	var urlFormCancel = global.baseUrl + 'clinics/submit-form';


	var dayTime =[
		{value: null, label: 'N/A'},
		{value: 3600000 * 6, label: '6:00 AM'},
		{value: 3600000 * 6.5, label: '6:30 AM'},
		{value: 3600000 * 7, label: '7:00 AM'},
		{value: 3600000 * 7.5, label: '7:30 AM'},
		{value: 3600000 * 8, label: '8:00 AM'},
		{value: 3600000 * 8.5, label: '8:30 AM'},
		{value: 3600000 * 9, label: '9:00 AM'},
		{value: 3600000 * 9.5, label: '9:30 AM'},
		{value: 3600000 * 10, label: '10:00 AM'},
		{value: 3600000 * 10.5, label: '10:30 AM'},
		{value: 3600000 * 11, label: '11:00 AM'},
		{value: 3600000 * 11.5, label: '11:30 AM'},
		{value: 3600000 * 12, label: '12:00 PM'},
		{value: 3600000 * 12.5, label: '12:30 PM'},
		{value: 3600000 * 13, label: '1:00 PM'},
		{value: 3600000 * 13.5, label: '1:30 PM'},
		{value: 3600000 * 14, label: '2:00 PM'},
		{value: 3600000 * 14.5, label: '2:30 PM'},
		{value: 3600000 * 15, label: '3:00 PM'},
		{value: 3600000 * 15.5, label: '3:30 PM'},
		{value: 3600000 * 16, label: '4:00 PM'},
		{value: 3600000 * 16.5, label: '4:30 PM'},
		{value: 3600000 * 17, label: '5:00 PM'},
		{value: 3600000 * 17.5, label: '5:30 PM'},
		{value: 3600000 * 18, label: '6:00 PM'},
		{value: 3600000 * 18.5, label: '6:30 PM'},
	];

	var data = [];
	var isLoading = false;


	function Format(detail){

		detail.ID = parseInt(detail.ID);
		detail.HOSPITALID = parseInt(detail.HOSPITALID);

		
		if( detail.MONTIME != null ){
			detail.MONTIME = parseInt(detail.MONTIME);
			detail.MONDATE = new Date(2020,1,1);
			detail.MONDATE.setMilliseconds(detail.MONTIME);
		}
		else{
			detail.MONDATE = null;
		}


		if( detail.TUETIME != null ){
			detail.TUETIME = parseInt(detail.TUETIME);
			detail.TUEDATE = new Date(2020,1,1);
			detail.TUEDATE.setMilliseconds(detail.TUETIME);
		}
		else{
			detail.TUEDATE = null;
		}


		if( detail.WEDTIME != null ){
			detail.WEDTIME = parseInt(detail.WEDTIME);
			detail.WEDDATE = new Date(2020,1,1);
			detail.WEDDATE.setMilliseconds(detail.WEDTIME);
		}
		else{
			detail.WEDDATE = null;
		}


		if( detail.THUTIME != null ){
			detail.THUTIME = parseInt(detail.THUTIME);
			detail.THUDATE = new Date(2020,1,1);
			detail.THUDATE.setMilliseconds(detail.THUTIME);
		}
		else{
			detail.THUDATE = null;
		}


		if( detail.FRITIME != null ){
			detail.FRITIME = parseInt(detail.FRITIME);
			detail.FRIDATE = new Date(2020,1,1);
			detail.FRIDATE.setMilliseconds(detail.FRITIME);
		}
		else{
			detail.FRIDATE = null;
		}


		if( detail.SATTIME != null ){
			detail.SATTIME = parseInt(detail.SATTIME);
			detail.SATDATE = new Date(2020,1,1);
			detail.SATDATE.setMilliseconds(detail.SATTIME);
		}
		else{
			detail.SATDATE = null;
		}

		if( detail.SUNTIME != null ){
			detail.SUNTIME = parseInt(detail.SUNTIME);
			detail.SUNDATE = new Date(2020,1,1);
			detail.SUNDATE.setMilliseconds(detail.SUNTIME);
		}
		else{
			detail.SUNDATE = null;
		}
	
		detail.isSubmit = false;

		if ( typeof detail.ISONLINE != 'boolean' )
			detail.ISONLINE = detail.ISONLINE == 'Y' ? true : false;


		detail.COORDLONG_0 = detail.COORDLONG_0 == null ? null : parseFloat(detail.COORDLONG_0);
		detail.COORDLONG_1 = detail.COORDLONG_1 == null ? null : parseFloat(detail.COORDLONG_1);
		detail.COORDSHORT_0 = detail.COORDSHORT_0 == null ? null : parseFloat(detail.COORDSHORT_0);
		detail.COORDSHORT_1 = detail.COORDSHORT_1 == null ? null : parseFloat(detail.COORDSHORT_1);
		

		return detail;
	}
 

	return { 
		Load: function(OPTIONS){
			
			isLoading = true;

			if( OPTIONS == undefined)
				OPTIONS = {};

			return $http.post( url, OPTIONS, global.ajaxConfig) .then( function(response) {

				data = [];

				angular.forEach( response.data, function (v, k) { 
					data.push(Format(v)); 
				});

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

			isLoading = true;

			return $http.get( urlForm + ID, global.ajaxConfig) .then( function(response) {

				isLoading = false;
				return Format(response.data);
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				isLoading = false;
				return false;
			});

		},
		Form_Cancel: function(OPTIONS){

			return $http.post( urlFormCancel, OPTIONS, global.ajaxConfig) .then( function(response) {

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

				DETAIL = Format(DETAIL);
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
		},
		Status: function(){
			return isLoading;
		},
		Day_Time: function(){
			return dayTime;
		}
	}
	
});

app.factory('MedicinesServices',function ($http, $q, $filter, $timeout, $interval, global) {

	var url = global.baseUrl + 'settings/medicines/index';
	var urlForm = global.baseUrl + 'settings/medicines/form-data/';
	
	var data = [];


	function Format(detail){

		detail.ID = parseInt(detail.ID);
		detail.isSubmit = false;

		return detail;
	}


	return {
		Load: function(OPTIONS){

			if( OPTIONS == undefined)
				OPTIONS = {};

			return $http.post( url, OPTIONS, global.ajaxConfig) .then( function(response) {

				data = [];
				data = response.data;

				angular.forEach( data, function (v, k) { v = Format(v); });

				return true;
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
		Form: function(ID){

			return $http.get( urlForm + ID, global.ajaxConfig) .then( function(response) {

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

app.factory('SalesServices',function ($http, $q,  global) {

	
	var data = {
      TOTAL_NETPAYABLES: 0,
      TOTAL_SERVED: 0,
      CLINICS: [],
      TOTAL_HMO: 0,
      HMO: [],
      HMO_POSTING: [],
      TOTAL_PHILHEALTH: 0,
      PHILHEALTH: [],
      SERVICES: [],
      PATIENTS: [],
      NOCHARGES: []
   }


	function Format_Clinics(detail){
      detail.SUBCLINICID = parseInt(detail.SUBCLINICID);
      detail.RECORDS = parseInt(detail.RECORDS);

      detail.GROSSAMOUNT = parseFloat(detail.GROSSAMOUNT);
      detail.DISCOUNTAMOUNT = parseFloat(detail.DISCOUNTAMOUNT);
      detail.NETPAYABLES = parseFloat(detail.NETPAYABLES);
      
		return detail;
   }
   
   function Format_HMO(detail){

      detail.RECORDS = parseInt(detail.RECORDS);
      detail.RECEIVED_COUNT = parseInt(detail.RECEIVED_COUNT);
      detail.NOT_RECEIVED_COUNT = parseInt(detail.NOT_RECEIVED_COUNT);
      
      detail.percentage = 0;
		return detail;
   }
   
 
   function Format_Services(detail){

      detail.QUANTITY = parseFloat(detail.QUANTITY);
      detail.AMOUNT = parseFloat(detail.AMOUNT);

		return detail;
   }

   function Format_PhilHealth(detail){

      if ( typeof detail.PHILHEALTH != 'boolean' )
         detail.PHILHEALTH = detail.PHILHEALTH == 'Y' ? true : false;
         
      if ( typeof detail.PHILHEALTHRECEIVED != 'boolean' )
			detail.PHILHEALTHRECEIVED = detail.PHILHEALTHRECEIVED == 'Y' ? true : false;

      if( detail.PHILHEALTHRECEIVED ){

         if( detail.PHILHEALTHCHEQUEDATE != null ){
            if ( !angular.isDate(detail.PHILHEALTHCHEQUEDATE) )
               detail.PHILHEALTHCHEQUEDATE =  global.Date(detail.PHILHEALTHCHEQUEDATE);
         }

         detail.PHILHEALTHAMOUNT = detail.PHILHEALTHAMOUNT == null ? 0 : parseFloat(detail.PHILHEALTHAMOUNT);
      }
      else{
         detail.PHILHEALTHCHEQUENO = '';
         detail.PHILHEALTHCHEQUEDATE = null;
         detail.PHILHEALTHAMOUNT = 0;
      }
   }


   function Format_Patients(detail){

      if( detail.CHECKUPDATE != null ){
			if ( !angular.isDate(detail.CHECKUPDATE) )
				detail.CHECKUPDATE =  global.Date(detail.CHECKUPDATE);
      }
      
      if ( typeof detail.PHILHEALTH != 'boolean' )
			detail.PHILHEALTH = detail.PHILHEALTH == 'Y' ? true : false;

      detail.NETPAYABLES = detail.NETPAYABLES == null ? 0 : parseFloat(detail.NETPAYABLES);
      detail.PAIDAMOUNT = detail.PAIDAMOUNT == null ? 0 : parseFloat(detail.PAIDAMOUNT);
      detail.CHEQUEAMOUNT = detail.CHEQUEAMOUNT == null ? 0 : parseFloat(detail.CHEQUEAMOUNT);
      detail.HMOAMOUNT = detail.HMOAMOUNT == null ? 0 : parseFloat(detail.HMOAMOUNT);
      detail.PHILHEALTHAMOUNT = detail.PHILHEALTHAMOUNT == null ? 0 : parseFloat(detail.PHILHEALTHAMOUNT);

      detail.totalPaid = detail.PAIDAMOUNT + detail.CHEQUEAMOUNT + detail.HMOAMOUNT + detail.PHILHEALTHAMOUNT;
 	   return detail;
   }

   function Format_No_Charges(detail){

      if( detail.CHECKUPDATE != null ){
			if ( !angular.isDate(detail.CHECKUPDATE) )
				detail.CHECKUPDATE =  global.Date(detail.CHECKUPDATE);
      }

 	   return detail;
   }


	return {
		Load_Clinics: function(OPTIONS){

			return $http.post( global.baseUrl + 'sales/clinics', OPTIONS, global.ajaxConfig) .then( function(response) {

            data.CLINICS = [];
            data.TOTAL_NETPAYABLES = 0;
            data.TOTAL_SERVED = 0;
            
				angular.forEach( response.data, function (v, k) { 
               var temp = Format_Clinics(v);
               data.TOTAL_NETPAYABLES += temp.NETPAYABLES;
               data.TOTAL_SERVED += temp.RECORDS;
               data.CLINICS.push(temp); 
            });

				return true;
				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

      },
      Load_HMO: function(OPTIONS){

			return $http.post( global.baseUrl + 'sales/hmo', OPTIONS, global.ajaxConfig) .then( function(response) {
   console.log(response.data)
            data.TOTAL_HMO = 0;
            data.TOTAL_HMO_RECEIVED = 0;
            data.TOTAL_HMO_NOT_RECEIVED = 0;
            data.TOTAL_HMO_AMOUNT = 0;
            data.HMO = [];

				angular.forEach( response.data, function (v, k) { 
					v = Format_HMO(v);

               data.TOTAL_HMO += v.RECORDS;
               data.TOTAL_HMO_RECEIVED += v.RECEIVED_COUNT;
               data.TOTAL_HMO_NOT_RECEIVED += v.NOT_RECEIVED_COUNT;

               data.HMO.push(v); 
            });

            angular.forEach( data.HMO, function(v,k){
               v.percentage = (v.RECORDS/ data.TOTAL_HMO) * 100;
            });
         
				return true;
				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

      },

            Load_HMO_POSTING: function(OPTIONS){

			return $http.post( global.baseUrl + 'sales/hmo_posting', OPTIONS, global.ajaxConfig) .then( function(response) {
                console.log(response.data)

            data.TOTAL_HMO = 0;
            data.TOTAL_HMO_RECEIVED = 0;
            data.TOTAL_HMO_NOT_RECEIVED = 0;
            data.HMO_POSTING = [];

				angular.forEach( response.data, function (v, k) { 
					v = Format_HMO(v);

               data.TOTAL_HMO += v.RECORDS;
               data.TOTAL_HMO_RECEIVED += v.RECEIVED_COUNT;
               data.TOTAL_HMO_NOT_RECEIVED += v.NOT_RECEIVED_COUNT;

               data.HMO_POSTING.push(v); 
            });

            angular.forEach( data.HMO_POSTING, function(v,k){
               v.percentage = (v.RECORDS/ data.TOTAL_HMO) * 100;
            });
         
				return true;
				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

      },
      Load_PhilHealth: function(OPTIONS){

			return $http.post( global.baseUrl + 'sales/philhealth', OPTIONS, global.ajaxConfig) .then( function(response) {

            data.TOTAL_PHILHEALTH = 0;
            data.PHILHEALTH = [];

				angular.forEach( response.data, function (v, k) { 
               v = Format_PhilHealth(v);
               
					data.PHILHEALTH.push(v);
               data.TOTAL_PHILHEALTH += 1;
            });
         
				return true;				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

      },
      Load_Services: function(OPTIONS){

			return $http.post( global.baseUrl + 'sales/services', OPTIONS, global.ajaxConfig) .then( function(response) {

            data.SERVICES = [];

				angular.forEach( response.data, function (v, k) { 
					v = Format_Services(v);
               data.SERVICES.push(v); 
            });
            
				return true;
				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

      },
      Load_Patients: function(OPTIONS){

			return $http.post( global.baseUrl + 'sales/patients', OPTIONS, global.ajaxConfig) .then( function(response) {

            if( OPTIONS.FROM == 0 ){
               data.PATIENTS = [];
            }

            if( response.data.length > 0 ){

               angular.forEach( response.data, function (v, k) { 
                  v = Format_Patients(v);
                  data.PATIENTS.push(v);
               });
              
               return true;
            }
            else{
               return false;
            }
				
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});

		},
      Load_Patients_No_Charges: function(OPTIONS){

         data.NOCHARGES = [];
         
			return $http.post( global.baseUrl + 'sales/patients-no-charges', OPTIONS, global.ajaxConfig) .then( function(response) {

            if( response.data.length > 0 ){

               angular.forEach( response.data, function (v, k) { 
                  v = Format_No_Charges(v);
                  data.NOCHARGES.push(v);
               });
               
               return true;
            }
            else{
               return false;
            }
				
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
      }
		
	}
	
});

app.factory('DashboardServices',function ($http, $filter,  global) {

   var urlSummary = global.baseUrl + 'dashboard/summary';
   var urlPatients = global.baseUrl + 'dashboard/patients-chart';
   var urlMonthNet = global.baseUrl + 'dashboard/month-net-chart';
   var urlMonthServed = global.baseUrl + 'dashboard/month-served-chart';
	
	var data = {
      SUMMARY: [{
         NEWPAT: 0,
         NEWMALE: 0,
         NEWFEMALE: 0,
         MONTHPAT: 0,
         TOTALPAT: 0,
         TODAYAPPOINT: 0,
         MONTHAPPOINT: 0,
         TOTALAPPOINT: 0,
         TODAYMR: 0,
         GROSSAMOUNT: 0,
         DISCOUNTAMOUNT: 0,
         NETPAYABLES: 0,
         MONTHMR: 0,
         MONTHGROSSAMOUNT: 0,
         MONTHDISCOUNTAMOUNT: 0,
         MONTHNETPAYABLES: 0
      }],
      PATIENTS: [],
      MONTHNET: [],
      MONTHSERVED: [],
      isSummary: false,
      isPatients: false,
      isMonthNet: false,
      isMonthServed: false
   }


	return {
      Load_Summary: function(){

         data.isSummary = true;

			return $http.get( urlSummary, global.ajaxConfig) .then( function(response) {

            data.SUMMARY = response.data;

            data.SUMMARY.NEWPAT = parseInt(data.SUMMARY.NEWPAT);

            if( data.SUMMARY.NEWMALE == null )
               data.SUMMARY.NEWMALE = 0;
            else
               data.SUMMARY.NEWMALE = parseInt(data.SUMMARY.NEWMALE);

            
            if( data.SUMMARY.NEWFEMALE == null )
               data.SUMMARY.NEWFEMALE = 0;
            else
               data.SUMMARY.NEWFEMALE = parseInt(data.SUMMARY.NEWFEMALE);

            if( data.SUMMARY.MONTHPAT == null )
               data.SUMMARY.MONTHPAT = 0;
            else
               data.SUMMARY.MONTHPAT = parseInt(data.SUMMARY.MONTHPAT);

            if( data.SUMMARY.TOTALPAT == null )
               data.SUMMARY.TOTALPAT = 0;
            else
               data.SUMMARY.TOTALPAT = parseInt(data.SUMMARY.TOTALPAT);

            if( data.SUMMARY.TODAYAPPOINT == null )
               data.SUMMARY.TODAYAPPOINT = 0;
            else
               data.SUMMARY.TODAYAPPOINT = parseInt(data.SUMMARY.TODAYAPPOINT);

            if( data.SUMMARY.MONTHAPPOINT == null )
               data.SUMMARY.MONTHAPPOINT = 0;
            else
               data.SUMMARY.MONTHAPPOINT = parseInt(data.SUMMARY.MONTHAPPOINT);

            if( data.SUMMARY.TOTALAPPOINT == null )
               data.SUMMARY.TOTALAPPOINT = 0;
            else
               data.SUMMARY.TOTALAPPOINT = parseInt(data.SUMMARY.TOTALAPPOINT);

            if( data.SUMMARY.TODAYHMO == null )
               data.SUMMARY.TODAYHMO = 0;
            else
               data.SUMMARY.TODAYHMO = parseInt(data.SUMMARY.TODAYHMO);

            if( data.SUMMARY.MONTHHMO == null )
               data.SUMMARY.MONTHHMO = 0;
            else
               data.SUMMARY.MONTHHMO = parseInt(data.SUMMARY.MONTHHMO);

            if( data.SUMMARY.TODAYMR == null )
               data.SUMMARY.TODAYMR = 0;
            else
               data.SUMMARY.TODAYMR = parseInt(data.SUMMARY.TODAYMR);

            if( data.SUMMARY.GROSSAMOUNT == null )
               data.SUMMARY.GROSSAMOUNT = 0;
            else
               data.SUMMARY.GROSSAMOUNT = parseFloat(data.SUMMARY.GROSSAMOUNT);

            if( data.SUMMARY.DISCOUNTAMOUNT == null )
               data.SUMMARY.DISCOUNTAMOUNT = 0;
            else
               data.SUMMARY.DISCOUNTAMOUNT = parseFloat(data.SUMMARY.DISCOUNTAMOUNT);

            if( data.SUMMARY.NETPAYABLES == null )
               data.SUMMARY.NETPAYABLES = 0;
            else
               data.SUMMARY.NETPAYABLES = parseFloat(data.SUMMARY.NETPAYABLES);

            if( data.SUMMARY.MONTHMR == null )
               data.SUMMARY.MONTHMR = 0;
            else
               data.SUMMARY.MONTHMR = parseInt(data.SUMMARY.MONTHMR);

            if( data.SUMMARY.MONTHGROSSAMOUNT == null )
               data.SUMMARY.MONTHGROSSAMOUNT = 0;
            else
               data.SUMMARY.MONTHGROSSAMOUNT = parseFloat(data.SUMMARY.MONTHGROSSAMOUNT);

            if( data.SUMMARY.MONTHDISCOUNTAMOUNT == null )
               data.SUMMARY.MONTHDISCOUNTAMOUNT = 0;
            else
               data.SUMMARY.MONTHDISCOUNTAMOUNT = parseFloat(data.SUMMARY.MONTHDISCOUNTAMOUNT);

            if( data.SUMMARY.MONTHNETPAYABLES == null )
               data.SUMMARY.MONTHNETPAYABLES = 0;
            else
               data.SUMMARY.MONTHNETPAYABLES = parseFloat(data.SUMMARY.MONTHNETPAYABLES);
            
            
            data.isSummary = false;
				return true;
				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isSummary = false;
				return false;
			});

      },
		Load_Patients_Chart: function(){

         data.isPatients = true;

			return $http.get( urlPatients, global.ajaxConfig) .then( function(response) {

            data.PATIENTS = {
               datas: [],
               labels: [],
               values: []
            }

				angular.forEach( response.data, function (v, k) { 
              
               if( v.DATEREG != null ){
                  if ( !angular.isDate(v.DATEREG) )
                     v.DATEREG =  $filter('date')(global.Date(v.DATEREG), "M/d");
               }
               
               v.TOTAL = parseInt(v.TOTAL);
               
               data.PATIENTS.datas.push(v);
               data.PATIENTS.labels.push(v.DATEREG);
               data.PATIENTS.values.push(v.TOTAL);
            });

            data.PATIENTS.labels.push('');
            data.PATIENTS.values.push(0);

            data.isPatients = false;
				return true;
				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isPatients = false;
				return false;
			});

      },
      Load_Month_Net_Chart: function(){

         data.isMonthNet = true;

			return $http.get( urlMonthNet, global.ajaxConfig) .then( function(response) {

            data.MONTHNET = {
               datas: [],
               labels: [],
               values: []
            }

				angular.forEach( response.data, function (v, k) { 
 
               if( v.CHECKUPDATE != null ){
                  if ( !angular.isDate(v.CHECKUPDATE) )
                     v.CHECKUPDATE =  $filter('date')(global.Date(v.CHECKUPDATE), "M/d");
               }
               
               v.TOTALNET = parseInt(v.TOTALNET);

                            
               data.MONTHNET.datas.push(v);
               data.MONTHNET.labels.push(v.CHECKUPDATE);
               data.MONTHNET.values.push(v.TOTALNET);
            });

            
            data.MONTHNET.labels.push('');
            data.MONTHNET.values.push(0);
            
            data.isMonthNet = false;
				return true;
				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isMonthNet = false;
				return false;
			});

      },
      Load_Month_Served_Chart: function(){
          
         data.isMonthServed = true;

			return $http.get( urlMonthServed, global.ajaxConfig) .then( function(response) {

            data.MONTHSERVED = {
               datas: [],
               labels: [],
               values: []
            }

				angular.forEach( response.data, function (v, k) { 
 
               if( v.CHECKUPDATE != null ){
                  if ( !angular.isDate(v.CHECKUPDATE) )
                     v.CHECKUPDATE =  $filter('date')(global.Date(v.CHECKUPDATE), "M/d");
               }
               
               v.TOTAL = parseInt(v.TOTAL);

                            
               data.MONTHSERVED.datas.push(v);
               data.MONTHSERVED.labels.push(v.CHECKUPDATE);
               data.MONTHSERVED.values.push(v.TOTAL);
            });

            
            data.MONTHSERVED.labels.push('');
            data.MONTHSERVED.values.push(0);

            data.isMonthServed = false;
				return true;
				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            data.isMonthServed = false;
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
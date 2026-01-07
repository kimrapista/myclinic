
app.factory('SchedulesServices',function ($http, $q, $filter, global) {

	
   var data = {
		TOTAL_APPOINTMENT: 0,
		TOTAL_UNVERIFIED: 0,
		TOTAL_UNACKNOWLEDGED: 0,
		TOTAL_ACKNOWLEDGED: 0,
		TOTAL_NOSCHEDULED: 0,
		CLINICS: []
	};

	var sms ={
		data: [],
		viewInfo: false,
		isLoading: [],
		isSubmit: false
	}

	var calendar = [];

	var isLoading = false;


	function Format_Appointment(detail){

		// if ID is undefined means summary info
		if( detail.ID == undefined ){

			if( detail.APPOINTMENTDATE != null ){
				if ( !angular.isDate(detail.APPOINTMENTDATE) )
					detail.APPOINTMENTDATE =  global.Date(detail.APPOINTMENTDATE);
			}
			
			detail.TOTAL = detail.TOTAL != null ? parseInt(detail.TOTAL) : 0;
		}
		else{

			detail.ID = parseInt(detail.ID);
			detail.PATIENTID = parseInt(detail.PATIENTID);

			if( detail.APPOINTMENTDATE != null ){
				if ( !angular.isDate(detail.APPOINTMENTDATE) )
					detail.APPOINTMENTDATE =  global.Date(detail.APPOINTMENTDATE);
			}
			
			if ( typeof detail.APPOINTMENTSERVED != 'boolean' )
			detail.APPOINTMENTSERVED = detail.APPOINTMENTSERVED == 'Y' ? true : false;

			if( detail.CHECKUPDATE != null ){
				if ( !angular.isDate(detail.CHECKUPDATE) )
					detail.CHECKUPDATE =  global.Date(detail.CHECKUPDATE);
			}
	
			detail.TOTAL_LAB = detail.TOTAL_LAB != null ? parseInt(detail.TOTAL_LAB) : 0;
		}
		
		return detail;
	}
	

	function Format_Schedule(detail){

		detail.ID = parseInt(detail.ID);
		detail.USERID = parseInt(detail.USERID);
		detail.SUBCLINICID = parseInt(detail.SUBCLINICID);

		if( detail.SDATETIME != null ){
			if ( !angular.isDate(detail.SDATETIME) )
				detail.SDATETIME =  global.Date(detail.SDATETIME);
		}

		detail.MAXPATIENT = detail.MAXPATIENT != null ? parseInt(detail.MAXPATIENT) : 0;
		detail.UNVERIFIED = detail.UNVERIFIED != null ? parseInt(detail.UNVERIFIED) : 0;
		detail.UNACKNOWLEDGED = detail.UNACKNOWLEDGED != null ? parseInt(detail.UNACKNOWLEDGED) : 0;
		detail.ACKNOWLEDGED = detail.ACKNOWLEDGED != null ? parseInt(detail.ACKNOWLEDGED) : 0;

		detail.PATIENTS = [];

		return detail;
	}

	
	function Format_Patient_No_Scheduled(detail){

		if( detail.ID == undefined ){

			if( detail.RESCHEDULETIME != null ){
				if ( !angular.isDate(detail.RESCHEDULETIME) )
					detail.RESCHEDULETIME =  global.Date(detail.RESCHEDULETIME);
			}
			
			detail.TOTAL = detail.TOTAL != null ? parseInt(detail.TOTAL) : 0;
		}
		else{

			detail = Format_Patient(detail);
		}

		return detail;
	}


	function Format_Patient (detail){

		detail.ID = parseInt(detail.ID);
		detail.PATIENTID = parseInt(detail.PATIENTID);
		detail.SCHEDULEID = parseInt(detail.SCHEDULEID);

		if ( typeof detail.VERIFIED != 'boolean' )
		detail.VERIFIED = detail.VERIFIED == 'Y' ? true : false;


		if( detail.VERIFIED && detail.VERIFIEDDATE != null ){
			if ( !angular.isDate(detail.VERIFIEDDATE) )
			detail.VERIFIEDDATE =  global.Date(detail.VERIFIEDDATE);
		}

		if ( typeof detail.ACKNOWLEDGED != 'boolean' )
		detail.ACKNOWLEDGED = detail.ACKNOWLEDGED == 'Y' ? true : false;

		if( detail.ACKNOWLEDGED && detail.VERIFIED != null ){
			if ( !angular.isDate(detail.ACKNOWLEDGEDTIME) )
			detail.ACKNOWLEDGEDTIME =  global.Date(detail.ACKNOWLEDGEDTIME);
		}


		if ( typeof detail.CANCELLED != 'boolean' )
		detail.CANCELLED = detail.CANCELLED == 'Y' ? true : false;

		if( detail.CANCELLED && detail.CANCELLEDTIME != null ){
			if ( !angular.isDate(detail.CANCELLEDTIME) )
			detail.CANCELLEDTIME =  global.Date(detail.CANCELLEDTIME);
		}

		if( detail.CREATEDTIME != null ){
			if ( !angular.isDate(detail.CREATEDTIME) )
			detail.CREATEDTIME =  global.Date(detail.CREATEDTIME);
		}

		if ( typeof detail.BLOCKED != 'boolean' )
		detail.BLOCKED = detail.BLOCKED == 'Y' ? true : false;

		detail.isAcknowledging = false;
		detail.isCancelling = false;
		detail.isBlocking = false;
		detail.isSubmit = false;

		return detail;
	}


	function Format_Note(detail){


		detail.ID = parseInt(detail.ID);
		detail.USERID = parseInt(detail.USERID);
		
		if( detail.NOTETIME != null ){
			if ( !angular.isDate(detail.NOTETIME) )
				detail.NOTETIME =  global.Date(detail.NOTETIME);
		}

		return detail;
	}

	function Format_SMS(detail){


		detail.ID = parseInt(detail.ID);

		if ( typeof detail.SEND != 'boolean' )
		detail.SEND = detail.SEND == 'Y' ? true : false;

		detail.isSending = false;
		detail.isError = false;
		detail.errorMessage = '';

		return detail;
	}


   function Reset_Calendar(monthDate){

		data.TOTAL_APPOINTMENT = 0;
		data.TOTAL_UNVERIFIED = 0;
		data.TOTAL_UNACKNOWLEDGED = 0;
		data.TOTAL_ACKNOWLEDGED = 0;
		data.TOTAL_NOSCHEDULED = 0;
		data.CLINICS = [];
		
		var days = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();

		calendar = [];
      
      for(var i=1 ; i <= days; i++ ){


			// for none of this month but still to in the 1st week
			if( i == 1 ){
				for(var ii = 0; ii < (new Date(monthDate.getFullYear(), monthDate.getMonth(), 1)).getDay(); ii++ ){
					calendar.push({
						day: 0,
						date: '',
						active: false,
						today: false,
						SCHEDULES: [],
						APPOINTMENTS: [],
						NOSCHEDULES: [],
						NOTES: [],
						SUBTOTAL_APPOINTMENT: 0,
						isLoadingAppointment: false,
						SUBTOTAL_UNVERIFIED: 0,
						SUBTOTAL_UNACKNOWLEDGED: 0,
						SUBTOTAL_ACKNOWLEDGED: 0,
						isLoadingDay: false,
						SUBTOTAL_NOSCHEDULED: 0,
						isLoadingNoSched : false
					});
				} 
			}


         calendar.push({
            day: i,
				date: new Date(monthDate.getFullYear(), monthDate.getMonth(), i),
				active: true,
				today: false,
				SCHEDULES: [],
				APPOINTMENTS: [],
				NOSCHEDULES: [],
				NOTES: [],
				SUBTOTAL_APPOINTMENT: 0,
				isLoadingAppointment: false,
				SUBTOTAL_UNVERIFIED: 0,
				SUBTOTAL_UNACKNOWLEDGED: 0,
				SUBTOTAL_ACKNOWLEDGED: 0,
				isLoadingDay: false,
				SUBTOTAL_NOSCHEDULED: 0,
				isLoadingNoSched : false
			});
			
		}

		// set the current day
		$filter('filter')( calendar, function(v,k){ if( $filter('date')( v.date,'M/d/y') == $filter('date')( new Date(),'M/d/y') ) v.today = true; });
	}


	function Add_Calendar_Notes(detail){

		angular.forEach( calendar, function(v,k){
			if( $filter('date')( v.date,'M/d/y') == $filter('date')( detail.NOTETIME,'M/d/y') ){
				
				v.NOTES.push(detail);
			}
		});
	}


	function Add_Calendar_Summary_Appointment(detail){

		angular.forEach( calendar, function(v,k){
			if( $filter('date')( v.date,'M/d/y') == $filter('date')( detail.APPOINTMENTDATE,'M/d/y') ){
				
				v.SUBTOTAL_APPOINTMENT += detail.TOTAL;
				data.TOTAL_APPOINTMENT += detail.TOTAL;
			}
		});
	}

	function Add_Calendar_Summary_No_Schedule_Patient(detail){

		angular.forEach( calendar, function(v,k){
			if( $filter('date')( v.date,'M/d/y') == $filter('date')( detail.RESCHEDULETIME,'M/d/y') ){
				
				v.SUBTOTAL_NOSCHEDULED += detail.TOTAL;
				data.TOTAL_NOSCHEDULED += detail.TOTAL;
			}
		});
	}


	function Add_Calendar_Summary_Schedule(detail){

		angular.forEach( calendar, function(v,k){
			if( $filter('date')( v.date,'M/d/y') == $filter('date')( detail.SDATETIME,'M/d/y') ){
				
				v.SCHEDULES.push(detail);
				v.SUBTOTAL_UNVERIFIED += detail.UNVERIFIED;
				v.SUBTOTAL_UNACKNOWLEDGED += detail.UNACKNOWLEDGED;
				v.SUBTOTAL_ACKNOWLEDGED += detail.ACKNOWLEDGED;

				data.TOTAL_UNVERIFIED += detail.UNVERIFIED;
				data.TOTAL_UNACKNOWLEDGED += detail.UNACKNOWLEDGED;
				data.TOTAL_ACKNOWLEDGED += detail.ACKNOWLEDGED;
			}
		});
	}


	function Scheduled_Counts(Day){

		// deduct the global counts by day
		data.TOTAL_UNVERIFIED -= Day.SUBTOTAL_UNVERIFIED;
		data.TOTAL_UNACKNOWLEDGED -= Day.SUBTOTAL_UNACKNOWLEDGED;
		data.TOTAL_ACKNOWLEDGED -= Day.SUBTOTAL_ACKNOWLEDGED;
		

		Day.SUBTOTAL_UNVERIFIED = 0;
		Day.SUBTOTAL_UNACKNOWLEDGED = 0;
		Day.SUBTOTAL_ACKNOWLEDGED = 0;


		angular.forEach( Day.SCHEDULES, function(sched,k){
			
			sched.UNVERIFIED = 0;
			sched.UNACKNOWLEDGED = 0;
			sched.ACKNOWLEDGED = 0;

			angular.forEach( sched.PATIENTS, function(pat, k){

				if( ! pat.CANCELLED &&  ! pat.BLOCKED ){

					if( ! pat.VERIFIED && ! pat.ACKNOWLEDGED ){
						sched.UNVERIFIED += 1;
					}
					else if( pat.VERIFIED && ! pat.ACKNOWLEDGED ){
						sched.UNACKNOWLEDGED += 1;
					}
					else{
						sched.ACKNOWLEDGED += 1;
					}
				}
			});

		
			Day.SUBTOTAL_UNVERIFIED += sched.UNVERIFIED;
			Day.SUBTOTAL_UNACKNOWLEDGED += sched.UNACKNOWLEDGED;
			Day.SUBTOTAL_ACKNOWLEDGED += sched.ACKNOWLEDGED;

			data.TOTAL_UNVERIFIED += sched.UNVERIFIED;
			data.TOTAL_UNACKNOWLEDGED += sched.UNACKNOWLEDGED;
			data.TOTAL_ACKNOWLEDGED += sched.ACKNOWLEDGED;

		});
	}

	return {
		Load: function(OPTIONS){

			isLoading = true;

			return $http.post( global.baseUrl+'schedules/search-schedules', OPTIONS, global.ajaxConfig) .then( function(response) {

				Reset_Calendar(OPTIONS.monthDate);

				angular.forEach( response.data.APPOINTMENTS, function (v, k) { 
					Add_Calendar_Summary_Appointment(Format_Appointment(v));
				});
				
            angular.forEach( response.data.SCHEDULES, function (v, k) { 
					Add_Calendar_Summary_Schedule(Format_Schedule(v)); 
				});

				angular.forEach( response.data.NOSCHEDULES, function (v, k) { 
					Add_Calendar_Summary_No_Schedule_Patient(Format_Patient_No_Scheduled(v)); 
				});

				angular.forEach( response.data.NOTES, function (v, k) { 
					Add_Calendar_Notes(Format_Note(v)); 
				});
 
				data.CLINICS = response.data.CLINICS;
				
				isLoading = false;
				return true;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            isLoading = false;
				return false;
			});

		},
		Load_Schedule_Day_Patients: function(Day){

			Day.isLoadingDay = true;

			return $http.post( global.baseUrl+'schedules/schedule-day-patients', {DayDate: Day.date}, global.ajaxConfig) .then( function(response) {
				
				// CLEAR PATIENTS
				angular.forEach( Day.SCHEDULES, function(v,k){ v.PATIENTS = []; });

            angular.forEach( response.data, function ( res, k) { 
					res = Format_Patient(res);

					angular.forEach( Day.SCHEDULES, function( sched,k1){
						if( res.SCHEDULEID == sched.ID ){
							Day.SCHEDULES[k1].PATIENTS.push(res);
						}
					});
				});

				Scheduled_Counts(Day);

            Day.isLoadingDay = false;
				return true;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            Day.isLoadingDay = false;
				return false;
			});
		},
		Load_Appointments_Day: function(Day){

			Day.isLoadingAppointment = true;
			
			return $http.post( global.baseUrl+'schedules/appointments-day', {DayDate: Day.date}, global.ajaxConfig) .then( function(response) {

				// reset counts
				data.TOTAL_APPOINTMENT -= Day.SUBTOTAL_APPOINTMENT;
				Day.SUBTOTAL_APPOINTMENT = 0;

				Day.APPOINTMENTS = [];

				angular.forEach( response.data, function (v, k) { 
					Day.APPOINTMENTS.push(Format_Appointment(v)); 

					Day.SUBTOTAL_APPOINTMENT += 1;
					data.TOTAL_APPOINTMENT += 1;
				});
							
            Day.isLoadingAppointment = false;
				return true;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            Day.isLoadingAppointment = false;
				return false;
			});

		},
		Load_No_Schedule_Patients: function(Day){

			Day.isLoadingNoSched = true;

			return $http.post( global.baseUrl+'schedules/no-schedule-day-patients', {DayDate: Day.date}, global.ajaxConfig) .then( function(response) {
				
				// reset counts
				data.TOTAL_NOSCHEDULED -= Day.SUBTOTAL_NOSCHEDULED;
				Day.SUBTOTAL_NOSCHEDULED = 0;

				Day.NOSCHEDULES = [];

				angular.forEach( response.data, function ( v, k) { 
					Day.NOSCHEDULES.push(Format_Patient_No_Scheduled(v));

					Day.SUBTOTAL_NOSCHEDULED += 1;
					data.TOTAL_NOSCHEDULED += 1;
				});

		
            Day.isLoadingNoSched = false;
				return true;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            Day.isLoadingNoSched = false;
				return false;
			});
		},
		Add_Note: function(Day){

			Add_Calendar_Notes({
				ID: 0,
				NOTETIME: Day.date,
				REMARKS: ''
			})
		},
		Load_Form: function(ID){

         return $http.post( global.baseUrl+'schedules/schedule-form', {ID: ID}, global.ajaxConfig) .then( function(response) {

				return Format_Schedule(response.data);				
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
      Calendar: function(){
         return calendar;
		},
		Update: function(SCHEDULE){
			
			var found = false;

			SCHEDULE = Format_Schedule(SCHEDULE);

			angular.forEach( calendar, function(v,k){
				if( $filter('date')( SCHEDULE.SDATETIME,'M/d/y') == $filter('date')( v.date,'M/d/y') ){
		
					angular.forEach( v.SCHEDULES, function( sched, k1){
						if( sched.ID == SCHEDULE.ID ){
							var PATIENTS = angular.copy(sched.PATIENTS);
							calendar[k].SCHEDULES[k1] = angular.copy(SCHEDULE);
							calendar[k].SCHEDULES[k1].PATIENTS = angular.copy(PATIENTS);
							found = true;
						}
					});

					if( ! found ){
						calendar[k].SCHEDULES.push(SCHEDULE);
					}
				}
			});
					
		},
		Add_Schedule_Patient: function(Day, PATIENT){
			
			angular.forEach( Day.SCHEDULES, function(v,k){
				if( v.ID == PATIENT.SCHEDULEID ){
					Day.SUBTOTAL_ACKNOWLEDGED += 1;
					data.TOTAL_ACKNOWLEDGED += 1;

					Day.SCHEDULES[k].PATIENTS.push(PATIENT);
				}
			})
		},
		Remove: function(SCHEDULE){

			var removeKey = null;

			angular.forEach( calendar, function(v,k){
				if( $filter('date')( SCHEDULE.SDATETIME,'M/d/y') == $filter('date')( v.date,'M/d/y') && removeKey == null ){
					
					angular.forEach( v.SCHEDULES, function( sched, k1){
						if( sched.ID == SCHEDULE.ID ){
							removeKey = k1;
						}
					});

					if( removeKey != null ){
						calendar[k].SCHEDULES.splice(removeKey,1);
					}
				}
			});
		},
		Remove_No_Schedule_Patient: function(Day, PATIENT){

			angular.forEach( Day.NOSCHEDULES, function(v,k){
				if( v.ID == PATIENT.ID ){
					Day.SUBTOTAL_NOSCHEDULED -= 1;
					data.TOTAL_NOSCHEDULED -= 1;

					Day.NOSCHEDULES.splice(k,1);
				}
			});
		},
		Update_Scheduled_Counts: function(Day){
			
			Scheduled_Counts(Day);
		},
		List_of_Dates: function(Day){

			var curDate = new Date();

			return $http.post( global.baseUrl+'schedules/last-appointment-summary',{DATE: curDate }, global.ajaxConfig) .then( function(response) {
				
				var temp = [];
				//86,400,000 1 day in milliseconds
				var dayMilliseconds = 86400000;
				var maxDate = new Date();
				

				angular.forEach( response.data.APPOINTMENTS, function(v,k){

					v.APPOINTMENTDATE =  global.Date(v.APPOINTMENTDATE);
					v.APPOINTMENTDATE.setHours(0)
					v.APPOINTMENTDATE.setMinutes(0);
					v.APPOINTMENTDATE.setSeconds(0);
					v.APPOINTMENTDATE.setMilliseconds(0);

					
					v.TOTAL = v.TOTAL != null ? parseInt(v.TOTAL) : 0;

					if( maxDate < v.APPOINTMENTDATE )
					maxDate = v.APPOINTMENTDATE;
				});


				angular.forEach( response.data.SCHEDULES, function(v,k){

					v.SDATETIME =  global.Date(v.SDATETIME);
					v.SDATETIME.setHours(0)
					v.SDATETIME.setMinutes(0);
					v.SDATETIME.setSeconds(0);
					v.SDATETIME.setMilliseconds(0);
			
					v.UNVERIFIED = v.UNVERIFIED != null ? parseInt(v.UNVERIFIED) : 0;
					v.UNACKNOWLEDGED = v.UNACKNOWLEDGED != null ? parseInt(v.UNACKNOWLEDGED) : 0;
					v.ACKNOWLEDGED = v.ACKNOWLEDGED != null ? parseInt(v.ACKNOWLEDGED) : 0;

					if( maxDate < v.SDATETIME )
					maxDate = v.SDATETIME;
				});

				angular.forEach( response.data.NOSCHEDULES, function(v,k){

					v.RESCHEDULETIME =  global.Date(v.RESCHEDULETIME);
					v.RESCHEDULETIME.setHours(0)
					v.RESCHEDULETIME.setMinutes(0);
					v.RESCHEDULETIME.setSeconds(0);
					v.RESCHEDULETIME.setMilliseconds(0);
				
					v.TOTAL = v.TOTAL != null ? parseInt(v.TOTAL) : 0;

					if( maxDate < v.RESCHEDULETIME )
					maxDate = v.RESCHEDULETIME;
				});


				angular.forEach( response.data.NOTES, function(v,k){

					v.NOTETIME =  global.Date(v.NOTETIME);
					v.NOTETIME.setHours(0)
					v.NOTETIME.setMinutes(0);
					v.NOTETIME.setSeconds(0);
					v.NOTETIME.setMilliseconds(0);
				
					if( maxDate < v.NOTETIME )
					maxDate = v.NOTETIME;
				});

 
				curDate.setHours(0)
				curDate.setMinutes(0);
				curDate.setSeconds(0);
				curDate.setMilliseconds(0);

				maxDate.setHours(0)
				maxDate.setMinutes(0);
				maxDate.setSeconds(0);
				maxDate.setMilliseconds(0);
				

				while ( curDate < maxDate) {

					var totalAppointment = 0;
					var totalUnverified = 0;
					var totalUnacknowledged = 0;
					var totalAcknowledged = 0;
					var totalNoScheduled = 0;
					var note = '';

					angular.forEach( response.data.APPOINTMENTS, function(v,k){ 
						if( $filter('date')( v.APPOINTMENTDATE,'M/d/y') == $filter('date')( curDate,'M/d/y') ){
							totalAppointment += v.TOTAL;
						} 
					});

					angular.forEach( response.data.SCHEDULES, function(v,k){ 
						if( $filter('date')( v.SDATETIME,'M/d/y') == $filter('date')( curDate,'M/d/y') ){
							totalUnverified += v.UNVERIFIED;
							totalUnacknowledged += v.UNACKNOWLEDGED;
							totalAcknowledged += v.ACKNOWLEDGED;
						} 
					});

					angular.forEach( response.data.NOSCHEDULES, function(v,k){ 
						if( $filter('date')( v.RESCHEDULETIME,'M/d/y') == $filter('date')( curDate,'M/d/y') ){
							totalNoScheduled += v.TOTAL;
						} 
					});

					angular.forEach( response.data.NOTES, function(v,k){ 
						if( $filter('date')( v.NOTETIME,'M/d/y') == $filter('date')( curDate,'M/d/y') ){
							note = note + v.REMARKS;
						} 
					});

					temp.push({
						date: angular.copy(curDate),
						COUNT_APPOINTMENT: totalAppointment,
						COUNT_UNVERIFIED: totalUnverified,
						COUNT_UNACKNOWLEDGED: totalUnacknowledged,
						COUNT_ACKNOWLEDGED: totalAcknowledged,
						COUNT_NOSCHEDULED: totalNoScheduled,
						TOTAL_PATIENT: totalAppointment + totalUnverified + totalUnacknowledged + totalAcknowledged + totalNoScheduled,
						NOTES: note,
						APPOINTMENTS: [],
						PATIENTS: [],
						NOSCHEDULES: []
					});

					curDate.setMilliseconds(dayMilliseconds);
				}


				while ( temp.length <= 200 ) {
					
					temp.push({
						date: angular.copy(curDate),
						COUNT_APPOINTMENT: 0,
						COUNT_UNVERIFIED: 0,
						COUNT_UNACKNOWLEDGED: 0,
						COUNT_ACKNOWLEDGED: 0,
						COUNT_NOSCHEDULED: 0,
						TOTAL_PATIENT: 0,
						NOTES:'',
						APPOINTMENTS: [],
						PATIENTS: [],
						NOSCHEDULES: []
					});

					curDate.setMilliseconds(dayMilliseconds);
				}

				
				return temp;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});
			
		},
		Data_SMS: function(){
			return sms;
		},
		Load_SMS_Queue: function(){

			sms.isLoading = true;

			return $http.get( global.baseUrl+'schedules/sms-rescheduled', global.ajaxConfig) .then( function(response) {

				sms.data = [];

				angular.forEach( response.data, function(v,k){
					sms.data.push(Format_SMS(v));
				})

				sms.isLoading = false;
				return true;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            sms.isLoading = false;
				return false;
			});

		},
		Update_SMS_Queue: function(TOKEN, SMS){
			
			sms.isLoading = true;

			return $http.post( global.baseUrl+'schedules/submit-sms-rescheduled',{TOKEN: TOKEN, SMS: SMS}, global.ajaxConfig) .then( function(response) {

				sms.isLoading = false;
				return true;				
			}, 
			function(err){ 
            global.Alert( err.statusText, 'Error ' + err.status);
            sms.isLoading = false;
				return false;
			});
		}


	}
	
});
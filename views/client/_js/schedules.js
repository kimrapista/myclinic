'use strict';


app.controller('Schedules', function($scope, $mdDialog, $q, $timeout, global, SchedulesServices, SubClinicServices){


   global.page.title = 'Appointments';
   global.page.pageBackUrl = '';

   $scope.isLoaded = false;

   $scope.search = {
      monthDate: new Date(),
      isSubmit: false
   }

   $scope.opt = {
      gridView: true
   }


   $scope.Change_month = function(month){

      var tempMonth = $scope.search.monthDate;

      if( month == -1 ){
         tempMonth.setMonth( $scope.search.monthDate.getMonth() - 1 );
      }
      else{
         tempMonth.setMonth( $scope.search.monthDate.getMonth() + 1 );
      }

      $scope.search.monthDate = angular.copy(tempMonth);

      $scope.Submit_Search();
   }


   $scope.Submit_Search = function(){

      $scope.search.isSubmit = true;
      $scope.opt.animate = false;

      SchedulesServices.Load({
         monthDate: $scope.search.monthDate
      }).then(function(data){

         $scope.search.isSubmit = false;
      });
   }



   $scope.View_Day = function (Day) {
     
      $mdDialog.show({
         templateUrl: 'views/client/modal_schedule_today.html',
         clickOutsideToClose: true,
         fullscreen: true,
         escapeToClose: false,
         hasBackdrop : true,
         locals:{ Day: Day },
         controller: function($scope, $http, $mdDialog, $q, $filter, $location, Day, MeServices, SchedulesServices, SubClinicServices, Preview ){

            $scope.isLoaded = false;

            $scope.Me = function(){
               return MeServices.Data();
            }

            $scope.Day = function(){
               return Day;
            }

            $scope.Add_Edit = function (ID) {

               var tempData = $filter('filter')(SubClinicServices.Data(), {ISONLINE: true}, true);
               
               if( tempData.length == 0 ){
                  global.Alert('No clinics is enabled for online appointment. Please update your clinics.')
               }
               else{

                  $mdDialog.show({
                     templateUrl: 'views/client/modal_form_schedules.html',
                     clickOutsideToClose: false,
                     fullscreen: false,
                     escapeToClose: false,
                     multiple : true,
                     locals:{
                        ID: ID,
                        Day: Day
                     },
                     controller: function($scope, $http, $mdDialog, $filter, $q, ID, Day, global, MeServices, SchedulesServices, UsersServices, SubClinicServices){

                        $scope.isLoaded = false;

                        $scope.Me = function(){
                           return MeServices.Data();
                        }

                        $scope.FORM = {
                           TOKEN: MeServices.Data().TOKEN,
                           ID: ID,
                           USERID: ( MeServices.Data().isDoctor ?  MeServices.Data().ID : null ),
                           SUBCLINICID: null,
                           SDATETIME: Day.date,
                           REMARKS: '',
                           MAXPATIENT: 5,
                           isSubmit: false
                        };

                        $scope.FORM.SDATETIME.setSeconds(0);
                        $scope.FORM.SDATETIME.setMilliseconds(0);

                        $scope.Submit_Form = function(){

                           if( $scope.FORM.isSubmit ) return;

                           $scope.FORM.isSubmit = true;

                           $http.post( global.baseUrl +'schedules/submit-form', $scope.FORM , global.ajaxConfig ).then(function (response) {

                              $scope.FORM.isSubmit = false;

                              if( $.trim(response.data.err) != '' ){
                                 global.Alert(response.data.err);
                              }
                              else{

                                 SchedulesServices.Update(response.data.suc.SCHEDULE);

                                 global.Toast('SAVED');
                                 $mdDialog.hide();
                              }

                           },
                           function (err) {
                              global.Alert( err.statusText, 'Error ' + err.status);
                           });
                        }


                        $scope.SubClinics = function(){
                           return $filter('filter')(SubClinicServices.Data(), {ISONLINE: true}, true);
                        }


                        $scope.Clinic_Default_Time = function(){
                           
                           angular.forEach( SubClinicServices.Data(), function(v,k){
                              if( v.ID == $scope.FORM.SUBCLINICID ){
                                
                                 if( Day.date.getDay() == 0 && v.SUNDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.SUNDATE.getHours(), v.SUNDATE.getMinutes() ); 
                                 }
                                 else if( Day.date.getDay() == 1 && v.MONDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.MONDATE.getHours(), v.MONDATE.getMinutes() ); 
                                 }
                                 else if( Day.date.getDay() == 2 && v.TUEDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.TUEDATE.getHours(), v.TUEDATE.getMinutes() ); 
                                 }
                                 else if( Day.date.getDay() == 3 && v.WEDDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.WEDDATE.getHours(), v.WEDDATE.getMinutes() ); 
                                 }
                                 else if( Day.date.getDay() == 4 && v.THUDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.THUDATE.getHours(), v.THUDATE.getMinutes() ); 
                                 }
                                 else if( Day.date.getDay() == 5 && v.FRIDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.FRIDATE.getHours(), v.FRIDATE.getMinutes() ); 
                                 }
                                 else if( Day.date.getDay() == 6 && v.SATDATE != null ){
                                    $scope.FORM.SDATETIME = new Date( Day.date.getFullYear(), Day.date.getMonth(), Day.date.getDate(), v.SATDATE.getHours(), v.SATDATE.getMinutes() ); 
                                 }
                              }
                           })
                        }

                        $scope.Doctors = function(){

                           return $filter('filter')( UsersServices.Data(), {isDoctor: true, ACTIVE: true}, true );
                        }


                        $scope.Init = function(){


                           $q.all([
                              UsersServices.Reload(),
                              SubClinicServices.Reload()
                           ]).then(function(result){

                              if( $scope.FORM.ID > 0 ){

                                 SchedulesServices.Load_Form($scope.FORM.ID).then(function(data){

                                    $scope.FORM = data;
                                    $scope.FORM.TOKEN = MeServices.Data().TOKEN;
                                    $scope.FORM.isSubmit = false;

                                    $scope.isLoaded = true;
                                 });
                              }
                              else{
                                 $scope.isLoaded = true;
                              }

                           });
                        }

                        $scope.Close = function () {
                           $mdDialog.cancel();
                        };

                     }
                  }).then(function(answer) {

                  }, function(cancel) {

                  });
               }
            }


            $scope.Remove_Schedule = function(SCHEDULE){

               if( MeServices.Data().ID == SCHEDULE.USERID || MeServices.Data().isAssistant ){

                  var confirm = $mdDialog.confirm()
                  .title('Delete Schedule')
                  .textContent('Are you sure to delete this schedule?')
                  .ariaLabel('delete patient')
                  .multiple(true)
                  .ok('Yes')
                  .cancel('No');

                  $mdDialog.show(confirm).then(function() {

                     $http.post( global.baseUrl +'schedules/submit-remove',{
                        TOKEN: MeServices.Data().TOKEN,
                        ID: SCHEDULE.ID
                     }, global.ajaxConfig).then(function(response){

                        if( response.data.err != '' ){
                           global.Alert(response.data.err);
                        }
                        else{
                           SchedulesServices.Remove(SCHEDULE);
                           global.Toast('DELETED SCHEDULE');
                        }
                     },
                     function(err){
                        global.Alert( err.statusText, 'Error ' + err.status);
                     });

                  }, function() {

                  });
               }
               else{
                  global.Alert('Sorry only '+SCHEDULE.DOCTORNAME+' can delete this schedule or the assistant');
               }
            }


            $scope.Sched_Patient_Acknowledged = function(PATIENT){

               var confirm = $mdDialog.confirm()
               .title('Acknowledge')
               .textContent('Are you sure to acknowledge '+ PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME +' '+ PATIENT.MIDDLENAME+' ?')
               .ariaLabel('Acknowledge')
               .multiple(true)
               .ok('Yes')
               .cancel('No'); 

               $mdDialog.show(confirm).then(function() {

                  PATIENT.isAcknowledging = true;

                  $http.post( global.baseUrl +'schedules/submit-patient-acknowledged',{
                     TOKEN: MeServices.Data().TOKEN,
                     ID: PATIENT.ID
                  }, global.ajaxConfig).then(function(response){

                     PATIENT.isAcknowledging = false;

                     if( response.data.err != '' ){
                        global.Alert(response.data.err);
                     }
                     else{
                        
                        PATIENT.ACKNOWLEDGED = true;
                        
                        SchedulesServices.Update_Scheduled_Counts(Day);

                        if( response.data.suc.PATIENTID ){
                           global.Toast('PATIENT INFO IS EXIST AND ACKNOWLEDGED');
                        }
                        else{
                           global.Toast('ACKNOWLEDGED');
                        }
                     }
                  },
                  function(err){
                     global.Alert( err.statusText, 'Error ' + err.status);
                     PATIENT.isAcknowledging = false;
                  });

               }, function() {

               });
            }

            $scope.Sched_Patient_Cancelled = function(PATIENT){

               var confirm = $mdDialog.confirm()
               .title('Cancel Appointment')
               .textContent('Are you sure to cancel  '+ PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME +' '+ PATIENT.MIDDLENAME+' appointment?')
               .ariaLabel('Cancel Appointment')
               .multiple(true)
               .ok('Yes')
               .cancel('No');

               $mdDialog.show(confirm).then(function() {

                  PATIENT.isCancelling = true;

                  $http.post( global.baseUrl +'schedules/submit-patient-cancelled',{
                     TOKEN: MeServices.Data().TOKEN,
                     ID: PATIENT.ID
                  }, global.ajaxConfig).then(function(response){

                     PATIENT.isCancelling = false;

                     if( response.data.err != '' ){
                        global.Alert(response.data.err);
                     }
                     else{

                        PATIENT.CANCELLED = true;
                        SchedulesServices.Update_Scheduled_Counts(Day);

                        global.Toast('CANCELLED');
                     }
                  },
                  function(err){
                     global.Alert( err.statusText, 'Error ' + err.status);
                     PATIENT.isCancelling = false;
                  });
 
               }, function() {

               });
            }

            $scope.Sched_Patient_Recancelled = function(PATIENT){

               var confirm = $mdDialog.confirm()
               .title('Recancel Appointment')
               .textContent('Are you sure to re-cancel '+ PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME +' '+ PATIENT.MIDDLENAME+' appointment?')
               .ariaLabel('Recancel Appointment')
               .multiple(true)
               .ok('Yes')
               .cancel('No');

               $mdDialog.show(confirm).then(function() {

                  PATIENT.isCancelling = true;

                  $http.post( global.baseUrl +'schedules/submit-patient-recancelled',{
                     TOKEN: MeServices.Data().TOKEN,
                     ID: PATIENT.ID
                  }, global.ajaxConfig).then(function(response){

                     PATIENT.isCancelling = false;

                     if( response.data.err != '' ){
                        global.Alert(response.data.err);
                     }
                     else{

                        PATIENT.CANCELLED = false;
                        SchedulesServices.Update_Scheduled_Counts(Day);

                        global.Toast('RE-CANCELLED');
                     }
                  },
                  function(err){
                     global.Alert( err.statusText, 'Error ' + err.status);
                     PATIENT.isCancelling = false;
                  });
 
               }, function() {

               });
            }

            $scope.Sched_Patient_Blocked = function(PATIENT){

               var confirm = $mdDialog.confirm()
               .title('Block Mobile Number')
               .textContent('Are you sure to block  '+ PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME +' '+ PATIENT.MIDDLENAME+' mobile no?')
               .ariaLabel('Block Mobile Number')
               .multiple(true)
               .ok('Yes')
               .cancel('No');

               $mdDialog.show(confirm).then(function() {

                  PATIENT.isBlocking = true;

                  $http.post( global.baseUrl +'schedules/submit-patient-blocked',{
                     TOKEN: MeServices.Data().TOKEN,
                     ID: PATIENT.ID,
                     MOBILENO: PATIENT.MOBILENO
                  }, global.ajaxConfig).then(function(response){

                     PATIENT.isBlocking = false;

                     if( response.data.err != '' ){
                        global.Alert(response.data.err);
                     }
                     else{

                        PATIENT.CANCELLED = true;
                        PATIENT.BLOCKED = true;
                        SchedulesServices.Update_Scheduled_Counts(Day);

                        global.Toast('BLOCKED');
                     }
                  },
                  function(err){
                     global.Alert( err.statusText, 'Error ' + err.status);
                     PATIENT.isBlocking = false;
                  });

               }, function() {

               });
            }

            $scope.Sched_Patient_Unblocked = function(PATIENT){

               var confirm = $mdDialog.confirm()
               .title('Unblock Mobile Number')
               .textContent('Are you sure to unblock '+ PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME +' '+ PATIENT.MIDDLENAME+' mobile no?')
               .ariaLabel('Unblock Mobile Number')
               .multiple(true)
               .ok('Yes')
               .cancel('No');

               $mdDialog.show(confirm).then(function() {

                  PATIENT.isBlocking = true;

                  $http.post( global.baseUrl +'schedules/submit-patient-unblocked',{
                     TOKEN: MeServices.Data().TOKEN,
                     ID: PATIENT.ID,
                     MOBILENO: PATIENT.MOBILENO
                  }, global.ajaxConfig).then(function(response){

                     PATIENT.isBlocking = false;

                     if( response.data.err != '' ){
                        global.Alert(response.data.err);
                     }
                     else{

                        PATIENT.CANCELLED = false;
                        PATIENT.BLOCKED = false;
                        SchedulesServices.Update_Scheduled_Counts(Day);

                        global.Toast('UN-BLOCKED');
                     }
                  },
                  function(err){
                     global.Alert( err.statusText, 'Error ' + err.status);
                     PATIENT.isBlocking = false;
                  });

               }, function() {

               });
            }

            $scope.Preview_Lab = function(MRID){

               Preview.Laboratory(MRID);
            }
 
            $scope.Redirect_Patient = function(PATIENTID){
               $location.url('/patient/'+ PATIENTID +'/record');
               $mdDialog.cancel();
            }

            $scope.Preview_Record = function(MRID){
               Preview.Medical_Record(MRID);
            }

            $scope.Appoint_Report = function(){

               Preview.Report( global.baseUrl + 'appointments/appointment-report','Appointment Report',{DATEFROM: Day.date, DATETO: Day.date});
            }

            $scope.Open_Reschedule_Form = function(){
               $mdDialog.hide('reschedule');
            }

            
            $scope.Note_Change = function(NOTE){

               $http.post( global.baseUrl + 'schedules/submit-note', {
                  TOKEN: MeServices.Data().TOKEN,
                  USERID: MeServices.Data().ID,
                  NOTETIME: NOTE.NOTETIME,
                  REMARKS: NOTE.REMARKS
               }, global.ajaxConfig).then(function(response){


               }, function(err){
                  global.Alert( err.statusText, 'Error ' + err.status);
               });
            }


            $scope.Set_Patient_Scheduled = function(PATIENT,SCHEDULE){

               if( PATIENT.isSubmit ) return;
               PATIENT.isSubmit = true;

               var confirm = $mdDialog.confirm()
               .title('Set Schedule')
               .textContent('You select schedule time '+$filter('date')(SCHEDULE.SDATETIME,'hh:mm a')+' for '+PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME +'?')
               .ariaLabel('Set Schedule')
               .multiple(true)
               .ok('Yes')
               .cancel('No');

               $mdDialog.show(confirm).then(function() {

                  $http.post( global.baseUrl + 'schedules/submit-set-patient-scheduled',{
                     TOKEN: MeServices.Data().TOKEN,
                     ID: PATIENT.ID,
                     SCHEDULEID: SCHEDULE.ID
                  }, global.ajaxConfig).then(function(response){

                     PATIENT.isSubmit = false;
                     PATIENT.SCHEDULEID = SCHEDULE.ID;
                     
                     SchedulesServices.Add_Schedule_Patient(Day, PATIENT);
                     SchedulesServices.Remove_No_Schedule_Patient(Day, PATIENT);

                  }, function(err){
                     global.Alert( err.statusText, 'Error ' + err.status);
                     PATIENT.isSubmit = false;
                  });

               }, function() {
                  PATIENT.isSubmit = false;
               });
            }

            $scope.Init = function(){

               $q.all([
                  SchedulesServices.Load_Appointments_Day(Day),
                  SchedulesServices.Load_Schedule_Day_Patients(Day),
                  SchedulesServices.Load_No_Schedule_Patients(Day)
               ]).then(function(data){

                  if( Day.NOTES.length == 0 ){
                     SchedulesServices.Add_Note(Day);
                  }
               })
            }

            $scope.Close = function(){
               $mdDialog.cancel();
            }

         }
      }).then(function(answer) {

         if( answer == 'reschedule' ){
            $scope.Reschdule_Form(Day);
         }
      }, function(cancel) {

      });
   }


   $scope.Reschdule_Form = function(Day){
      
      $mdDialog.show({
         templateUrl: 'views/client/modal_form_reschedule.html',
         clickOutsideToClose: false,
         fullscreen: true,
         escapeToClose: false,
         hasBackdrop : true,
         locals:{ Day: angular.copy(Day) },
         controller: function($scope, $http, $mdDialog, $filter, Day, MeServices, SchedulesServices ){

            $scope.Day = Day;

            $scope.isLoaded = false;
            $scope.isSelected = false;
            $scope.isVacant = false;
            $scope.isFORMView = false;
            
            $scope.RESCHEDULED = [];
            $scope.FORM = [];


            $scope.View_Selected = function( APPOINTMENTCOUNT, PATIENTCOUNT, NOSCHEDULECOUNT, TOTAL_PATIENT){
               
               if( $scope.isSelected && $scope.isVacant ){
                  if( TOTAL_PATIENT == 0 || (APPOINTMENTCOUNT > 0 || PATIENTCOUNT > 0 || NOSCHEDULECOUNT > 0) ){

                     if( TOTAL_PATIENT == 0 && APPOINTMENTCOUNT == 0 && PATIENTCOUNT == 0 && NOSCHEDULECOUNT == 0){
                        return false;
                     }
                     else{
                        return true;
                     }
                  }
                  else{
                     return false;
                  }
               }
               else if( $scope.isSelected && ! $scope.isVacant ){
                  if( APPOINTMENTCOUNT > 0 || PATIENTCOUNT > 0 || NOSCHEDULECOUNT > 0 ){
                     return true;
                  }
                  else{
                     return false;
                  }
               }
               else if( ! $scope.isSelected && $scope.isVacant ){
                  if( TOTAL_PATIENT == 0 || (APPOINTMENTCOUNT > 0 || PATIENTCOUNT > 0 || NOSCHEDULECOUNT > 0) ){
                     return true;
                  }
                  else{
                     return false;
                  }
               }
               else{
                  return true;
               }
            }


            $scope.Select_Date = function(TRANSFER){

               var tempAppointments = [];
               var tempPatients = [];
               var tempNoSchedule = [];

               angular.forEach( Day.APPOINTMENTS, function(v,k){
                  if( v.SELECTED ){
                     v.SELECTED = false;
                     tempAppointments.push(v);
                  }
               });

               angular.forEach( Day.SCHEDULES, function(v,k){
                  angular.forEach( v.PATIENTS, function(v1,k1){
                     if( v1.SELECTED ){
                        v1.SELECTED = false;
                        tempPatients.push(v1);
                     }
                  })
               });

               angular.forEach( Day.NOSCHEDULES, function(v,k){
                  if( v.SELECTED ){
                     v.SELECTED = false;
                     tempNoSchedule.push(v);
                  }
               });

               
               if( tempAppointments.length > 0 || tempPatients.length > 0 || tempNoSchedule.length > 0 ){

                  angular.forEach( tempAppointments, function(v,k){

                     TRANSFER.APPOINTMENTS.push(v);

                     angular.forEach( Day.APPOINTMENTS, function(v1,k1){
                        if( v.ID == v1.ID )
                           Day.APPOINTMENTS.splice(k1,1);
                     });
                  });


                  angular.forEach( tempPatients, function(v,k){

                     TRANSFER.PATIENTS.push(v);

                     angular.forEach( Day.SCHEDULES, function(v1,k1){
                        angular.forEach( v1.PATIENTS, function(v2,k2){
                           if( v.ID == v2.ID )
                           Day.SCHEDULES[k1].PATIENTS.splice(k2,1);
                        })
                     });
                  });

                  angular.forEach( tempNoSchedule, function(v,k){

                     TRANSFER.NOSCHEDULES.push(v);

                     angular.forEach( Day.NOSCHEDULES, function(v1,k1){
                        if( v.ID == v1.ID )
                           Day.NOSCHEDULES.splice(k1,1);
                     });
                  });

               }
               else{
                  global.Alert('Please select patient and then click (+) to transfer');
               }
            }

            $scope.Return_Appointment = function( FORMDAY, APPOINTMENT){

               Day.APPOINTMENTS.push(APPOINTMENT);

               angular.forEach( FORMDAY.APPOINTMENTS, function(v,k){
                  if( v.ID == APPOINTMENT.ID ){
                     FORMDAY.APPOINTMENTS.splice(k,1);
                  }
               });   
            }

            $scope.Return_Schedule = function( FORMDAY, PATIENT){

               angular.forEach( Day.SCHEDULES , function(v,k){
                  if( v.ID == PATIENT.SCHEDULEID )
                     Day.SCHEDULES[k].PATIENTS.push(PATIENT);
               });

               angular.forEach( FORMDAY.PATIENTS, function(v,k){
                  if( v.ID == PATIENT.ID ){
                     FORMDAY.PATIENTS.splice(k,1);
                  }
               });   
            }


            $scope.Return_NoSchedule = function(FORMDAY, PATIENT){

               Day.NOSCHEDULES.push(PATIENT);

               angular.forEach( FORMDAY.NOSCHEDULES, function(v,k){
                  if( v.ID == PATIENT.ID ){
                     FORMDAY.NOSCHEDULES.splice(k,1);
                  }
               });  
            }


            $scope.Check_Before_SMS = function(){
               
               $scope.FORM = {
                  Day: [],
                  MESSAGE: "Good Day Mr/Ms [LASTNAME]. Your appointment with [CLINICNAME] has been rescheduled on [DATE].\n\nTo confirm this date, please text [CLINICMOBILE]. If we received no response, we expect to see you on the new schedule. Thank you.\n\nThis is a SYSTEM GENERATED MESSAGE, Please do not respond to this number.",
                  isSubmit: false,
                  messageError: false,
                  isMessage: false
               };

               angular.forEach( $scope.RESCHEDULED, function(v,k){

                  if(v.APPOINTMENTS.length > 0 || v.PATIENTS.length > 0 || v.NOSCHEDULES.length > 0 ){

                     angular.forEach( v.APPOINTMENTS, function(v1,k1){ 
                        v1.TITLE = 'RESCHEDULED APPOINTMENT';
                        v1.MESSAGE = ''; 
                     });

                     angular.forEach( v.PATIENTS, function(v1,k1){ 
                        v1.TITLE = 'RESCHEDULED ONLINE APPOINTMENT';
                        v1.MESSAGE = ''; 
                     });

                     angular.forEach( v.NOSCHEDULES, function(v1,k1){ 
                        v1.TITLE = 'RESCHEDULED ONLINE APPOINTMENT';
                        v1.MESSAGE = ''; 
                     });

                     $scope.FORM.Day.push(v);
                  }
               });

               if( $scope.FORM.Day.length > 0 ){

                  $scope.isFORMView = true;
                  $scope.SMS_Change();
               }
               else{
                  global.Alert("Sorry no patient to reschedule");
               }
            } 


            $scope.SMS_Change = function(){

               $scope.FORM.messageError = false;

               angular.forEach( $scope.FORM.Day, function(v,k){

                  angular.forEach( v.APPOINTMENTS, function(v1,k1){
                     v1.MESSAGE = ($scope.FORM.MESSAGE).replace('[NAME]', v1.LASTNAME+', '+v1.FIRSTNAME+' '+ (v1.MIDDLENAME.length > 0 ? v1.MIDDLENAME[0]+'.' : '' ) );
                     v1.MESSAGE = (v1.MESSAGE).replace('[LASTNAME]', v1.LASTNAME);
                     v1.MESSAGE = (v1.MESSAGE).replace('[DATE]', $filter('date')( v.date, 'M/d/y' ) );
                     v1.MESSAGE = (v1.MESSAGE).replace('[CLINICNAME]', MeServices.Data().CLINICNAME);
                     v1.MESSAGE = (v1.MESSAGE).replace('[CLINICMOBILE]', MeServices.Data().CLINICMOBILENO);
                  });

                  angular.forEach( v.PATIENTS, function(v1,k1){
                     v1.MESSAGE = ($scope.FORM.MESSAGE).replace('[NAME]', v1.LASTNAME+', '+v1.FIRSTNAME+' '+ (v1.MIDDLENAME.length > 0 ? v1.MIDDLENAME[0]+'.' : '' ) );
                     v1.MESSAGE = (v1.MESSAGE).replace('[LASTNAME]', v1.LASTNAME);
                     v1.MESSAGE = (v1.MESSAGE).replace('[DATE]', $filter('date')( v.date, 'M/d/y' ) );

                     v1.MESSAGE = (v1.MESSAGE).replace('[CLINICNAME]', MeServices.Data().CLINICNAME);
                     v1.MESSAGE = (v1.MESSAGE).replace('[CLINICMOBILE]', MeServices.Data().CLINICMOBILENO);
                  });


                  angular.forEach( v.NOSCHEDULES, function(v1,k1){
                     v1.MESSAGE = ($scope.FORM.MESSAGE).replace('[NAME]', v1.LASTNAME+', '+v1.FIRSTNAME+' '+ (v1.MIDDLENAME.length > 0 ? v1.MIDDLENAME[0]+'.' : '' ) );
                     v1.MESSAGE = (v1.MESSAGE).replace('[LASTNAME]', v1.LASTNAME);
                     v1.MESSAGE = (v1.MESSAGE).replace('[DATE]', $filter('date')( v.date, 'M/d/y' ) );

                     v1.MESSAGE = (v1.MESSAGE).replace('[CLINICNAME]', MeServices.Data().CLINICNAME);
                     v1.MESSAGE = (v1.MESSAGE).replace('[CLINICMOBILE]', MeServices.Data().CLINICMOBILENO);
                  });
               });
            }

            $scope.Submit_Form = function(){

               if( $scope.FORM.isSubmit ) return;

               $scope.FORM.isSubmit = true;
               $scope.FORM.isMessage = false;

               $http.post( global.baseUrl +'schedules/submit-reschedules', {
                  TOKEN: MeServices.Data().TOKEN,
                  DAY: $scope.FORM.Day
               }, global.ajaxConfig ).then(function (response) {

                  $scope.FORM.isSubmit = false;

                  if( response.data.err != ''){
                     global.Alert('response.data.err');
                  }
                  else{

                     global.Toast('RESCHEDULED!');
                     $mdDialog.hide('resetmonth');
                  }
               }, 
               function (err) { 
                     global.Alert( err.statusText, 'Error ' + err.status);
                     $scope.close();
               });
            }
            

            $scope.Init = function(){

               SchedulesServices.List_of_Dates(Day).then(function(data){

                  $scope.RESCHEDULED = data;

                  angular.forEach( Day.APPOINTMENTS, function(v,k){
                     v.SELECTED = false;
                  });

                  angular.forEach( Day.SCHEDULES, function(v,k){
                     angular.forEach( v.PATIENTS, function(v1,k1){
                        v1.SELECTED = false;
                     })
                  });

                  angular.forEach( Day.NOSCHEDULES, function(v,k){
                     v.SELECTED = false;
                  });

                  $scope.isLoaded = true;
               });
            }

            $scope.Close = function(){
               $mdDialog.cancel();
            }

         }
      }).then(function(answer) {


         if( answer == 'resetmonth' ){
            $scope.Submit_Search();


            var confirm = $mdDialog.confirm()
            .title('Rescheduled SMS')
            .textContent('Do you want to send the messages now?')
            .ariaLabel('Rescheduled SMS')
            .multiple(true)
            .ok('Yes')
            .cancel('Later');

            $mdDialog.show(confirm).then(function() {

               $scope.Queue_SMS();

            }, function() {

            });
         }

      }, function(cancel) {

      });
   }



   $scope.Queue_SMS = function(){

      $mdDialog.show({
         templateUrl: 'views/client/modal_rescheduled_queue_sms.html',
         clickOutsideToClose: false,
         fullscreen: false,
         escapeToClose: false,
         hasBackdrop : true,
         locals:{  },
         controller: function($scope, $http, $mdDialog, $q, global, MeServices, SchedulesServices ){

            $scope.Data = function(){
               return SchedulesServices.Data_SMS();
            }

            $scope.Submit_Form = function(){

               if( $scope.Data().isSubmit ) return;

               $scope.Data().isSubmit = true;
               
               var sendCount = 0;
               var errorCount = 0;
 
               angular.forEach( $scope.Data().data, function(v,k){

                  if( ! v.SEND ){

                     v.isSending = true;
                     v.isError = false;
                     v.errorMessage = '';

                     $http.post( global.baseUrl + 'sms/submit-sms-patient-rescheduled', {
                        ID: v.ID,
                        TITLE: v.TITLE,
                        MESSAGE: v.MESSAGE,
                        MOBILENO: v.MOBILENO
                     }, global.ajaxConfig).then(function(response){

                        v.isSending = false;

                        if( response.data.err != '' ){
                           v.isError = true;
                           v.errorMessage = response.data.err;
                           errorCount += 1;
                        }
                        else{
                           sendCount += 1;
                           v.SEND = true;
                        }

                        $scope.Submit_Check_Done(sendCount, errorCount);
                     
                     }, function(err){
                        v.isSending = false;
                        v.isError = true;
                        errorCount += 1;

                        $scope.Submit_Check_Done(sendCount, errorCount);
                     });
                  }
               });
            }

            $scope.Submit_Check_Done = function(sendCnt,errorCnt){

               if( sendCnt == $scope.Data().data.length ){

                  $scope.Data().isSubmit = false;
                  global.Toast('All SEND');
                  $mdDialog.cancel();

                  SchedulesServices.Update_SMS_Queue(
                     MeServices.Data().TOKEN,
                     $scope.Data().data
                  ).then(function(data){
                     //$scope.Init();
                  });
               }
               else if( (sendCnt + errorCnt) == $scope.Data().data.length ){
                  
                  $scope.Data().isSubmit = false;
                  global.Toast('Not all send. Please check the patient mobile no. or internet connection and send again.');

                  SchedulesServices.Update_SMS_Queue(
                     MeServices.Data().TOKEN,
                     $scope.Data().data
                  ).then(function(data){
                     //$scope.Init();
                  });
               }
            }

            $scope.Init = function(){
               SchedulesServices.Load_SMS_Queue();
            }

            $scope.Close = function(){
               $mdDialog.cancel();
            }
         }
      }).then(function(answer) {

      }, function(cancel) {

      });
   }


   $scope.Data = function(){
      return SchedulesServices.Data();
   }

   $scope.DataStatus = function(){
      return SchedulesServices.Status();
   }

   $scope.Calendar = function(){
      return SchedulesServices.Calendar();
   }


   $scope.Init = function(){

      $q.all([
         SubClinicServices.Reload()
      ]).then(function(result){

         $scope.Submit_Search();
         $scope.isLoaded = true;
      });
   }


});

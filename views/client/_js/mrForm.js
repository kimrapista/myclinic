'use strict';


app.controller('MRForm', function($scope, $http, $timeout, $filter, $routeParams, $location, $q, $mdDialog, global, MRFormServices, 
    MeServices, ClinicServices, SubClinicServices, MedicinesServices, InstructionsServices, ServicesServices, DiscountsServices, ProceduresServices, 
    HMOServices, LaboratoryServices, LabMonitoringServices, Preview, NotifyServices, ICDServices, RVSServices, UsersServices){ 


    $scope.Me = function(){
        return MeServices.Data();
    }

    global.page.title = $routeParams.P2 > 0 ? 'EDIT MR' : 'NEW MR';
    global.page.pageBackUrl = $routeParams.P1 > 0 ? '#!/patient/'+ $routeParams.P1 +'/record' : '#!/patients';

    $scope.isLoaded = false;
    $scope.Layout = global.layout;

    $scope.cancel = $routeParams.P1 > 0 ? '#!/patient/'+ $routeParams.P1 +'/record' : '#!/patients';

    $scope.FORM = [];

    $scope.isFiles = false;
    $scope.isFilesLoading = false;

    $scope.icd = {
        search: '',
        selected: [],
        edit: false,
        change: false
    }

    $scope.rvs = {
        search: '',
        selected: [],
        edit: false,
        change: false
    }

	var MRID = $routeParams.P2;
	var ALLOWED_TO_EDIT = true;

	// SET RECORD HOLDER WHEN PAGE OPEN
	if (MRID > 0) {
		$http.post(global.baseUrl + "medicals/set-holder",{ MRID: MRID, NAME: MeServices.Data().NAME, }, global.ajaxConfig,
		).then(
			function (response) {
				if (response.data.status == "LOCKED") {
					global.Alert(
						"This record is currently being edited by " + response.data.name
					);

					$location.url("/patient/" + $routeParams.P1 + "/record");
				}

				lockInterval = setInterval(function () {
					if (!ALLOWED_TO_EDIT || MRID <= 0) return;
					$http.post(global.baseUrl + "medicals/heartbeat-holder",{MRID: MRID},global.ajaxConfig);
				}, 15000);
			},
			function (err) {
				global.Alert(err.statusText, "Error " + err.status);
			},
		);
	}
	
	//CLEAR ON LEAVE THE PAGE
	$scope.$on("$destroy", function () {
		if (MRID > 0 && ALLOWED_TO_EDIT) {
			$http.post(global.baseUrl + "medicals/clear-holder",{MRID: MRID,},global.ajaxConfig);
		}
		if (lockInterval) {
			clearInterval(lockInterval);
		}
	});

	//(TAB CLOSE)
	window.addEventListener("beforeunload", function () {
		if (MRID > 0 && ALLOWED_TO_EDIT) { 
			navigator.sendBeacon( global.baseUrl + "medicals/clear-holder", JSON.stringify({ MRID: MRID }));
		}

	});

    $scope.Submit_Form = function(){

        if( $scope.FORM.isSubmit ) return;

        // update from User Account
        if( $scope.FORM.SPECIALISTID == 0  || $scope.FORM.SPECIALISTID == null ){
            $scope.FORM.SPECIALISTID = MeServices.Data().SPECIALISTID;
        }

        $scope.FORM.isSubmit = true;

        $scope.FORM.CHECKUPDATE = global.Date_String_Format($scope.FORM.CHECKUPDATE);
        $scope.FORM.APPOINTMENTDATE = global.Date_String_Format($scope.FORM.APPOINTMENTDATE);


        $http.post( global.baseUrl + 'medicals/submit-form/', $scope.FORM , global.ajaxConfig ).then(function (response) {

            $scope.FORM.isSubmit = false;

            if( $.trim(response.data) == 'RELOGIN'){
                global.Alert('Please try again');
            }
            else{

                if( $.trim(response.data.err) != '' ){
                    global.Alert(response.data.err);
                }
                else{  
                    
                    if( $scope.FORM.ID == 0 ){

                        $scope.FORM.ID = parseInt(response.data.suc.MRID);
                        $location.url('/patient/'+ $routeParams.P1 +'/'+ response.data.suc.MRID +'/medical-record');

                        // if( MeServices.Data().isAssistant ){                            
                        //     FireBaseServices.New_Logs({
                        //         BODY: 'Prepared medical record for '+ $scope.FORM.PATIENT.LASTNAME +', '+ $scope.FORM.PATIENT.FIRSTNAME,
                        //         MEDICALRECORDID: $scope.FORM.ID
                        //     });
                        // }
                    }
                    else{

                        global.page.title = 'EDIT MR';

                        if( response.data.suc.TOKEN )
                            $scope.FORM.TOKEN = response.data.suc.TOKEN;

                        $scope.FORM.ID = parseInt(response.data.suc.MRID);
                        
                        $scope.FORM.LABORATORIES = MRFormServices.Format_Laboratories(response.data.suc.LABORATORIES);

                        
                        if( $scope.FORM.LABORATORIES.length > 0 ){
                            angular.forEach( LaboratoryServices.Data(), function(v,k){
                                angular.forEach( $scope.FORM.LABORATORIES, function(v1,k1){
                                    if( v.ID == v1.LABORATORYID ){
                                        v1.REF = v;
                                    }
                                });  
                            });
                        }
                        
                        $scope.FORM.MRLABMONITORING = MRFormServices.Format_MRLabMonitoring(response.data.suc.MRLABMONITORING);
                        
                        if( $scope.FORM.MRLABMONITORING.length > 0 ){
                            angular.forEach( LabMonitoringServices.Data(), function(v,k){
                                angular.forEach( $scope.FORM.MRLABMONITORING, function(v1,k1){
                                    if( v.ID == v1.LABMONITORINGID ){
                                        v1.REF = v;
                                    }
                                });  
                            });
                        }
                        
                        $scope.FORM.MEDICINES = MRFormServices.Format_Medicines(response.data.suc.MEDICINES);
                        $scope.FORM.SERVICES = MRFormServices.Format_Services(response.data.suc.SERVICES);
                        $scope.FORM.DISCOUNTS = MRFormServices.Format_Discounts(response.data.suc.DISCOUNTS);

                    }

                    if( $scope.FORM.APPOINTMENT ){
                        MRFormServices.SMS_Form($scope.FORM.ID); 
                    }

                    NotifyServices.Update_Today_Patients($scope.FORM.PATIENT, $scope.FORM.ID);

                    global.Toast('SAVED');
                }
            }
        }, 
        function (err) { 
            global.Alert( err.statusText, 'Error ' + err.status);
            $scope.FORM.isSubmit = false;
        });
    }

    $scope.Reset_Appointment = function(){

        if ( $scope.FORM.APPOINTMENT ){ 
            $scope.FORM.APPOINTMENTDATE = new Date();
        }
        else{
            $scope.FORM.APPOINTMENTDATE = null;
            $scope.FORM.APPOINTMENTDESCRIPTION = '';
            $scope.FORM.APPOINTMENTSUBCLINICID = null;
        }
    }

    $scope.Calc_BMI = function(){

        var height = $scope.FORM.HEIGHT;
        var weight = $scope.FORM.WEIGHT;
        if( isNaN(parseFloat(height)) ){
            height = 0;
        }
        else{
            height = parseFloat(height);
        }

        if( isNaN(parseFloat(weight)) ){
            weight = 0;
        }
        else{
            weight = parseFloat(weight);
        }

        if( height > 0 ){
            $scope.FORM.BMI = ((weight / height / height) * 10000).toFixed(1);
        }
        else{
            $scope.FORM.BMI = 0;
        }
    }


    $scope.Calc_Pirani_Scoring = function(){

        $scope.FORM.MIDFOOT_SCORE_L = 0;
        $scope.FORM.MIDFOOT_SCORE_R = 0;        

        if( ! isNaN($scope.FORM.LATERAL_BORDER_L)){
            $scope.FORM.MIDFOOT_SCORE_L += $scope.FORM.LATERAL_BORDER_L;
        }

        if( ! isNaN($scope.FORM.MEDICAL_CREASE_L)){
            $scope.FORM.MIDFOOT_SCORE_L += $scope.FORM.MEDICAL_CREASE_L;
        }

        if( ! isNaN($scope.FORM.TALAR_HEAD_L)){
            $scope.FORM.MIDFOOT_SCORE_L += $scope.FORM.TALAR_HEAD_L;
        }

        if( ! isNaN($scope.FORM.LATERAL_BORDER_R)){
            $scope.FORM.MIDFOOT_SCORE_R += $scope.FORM.LATERAL_BORDER_R;
        }

        if( ! isNaN($scope.FORM.MEDICAL_CREASE_R)){
            $scope.FORM.MIDFOOT_SCORE_R += $scope.FORM.MEDICAL_CREASE_R;
        }

        if( ! isNaN($scope.FORM.TALAR_HEAD_R)){
            $scope.FORM.MIDFOOT_SCORE_R += $scope.FORM.TALAR_HEAD_R;
        }

        $scope.FORM.HINDFOOT_SCORE_L = 0;
        $scope.FORM.HINDFOOT_SCORE_R = 0;

        if( ! isNaN($scope.FORM.POSTERIOR_CREASE_L)){
            $scope.FORM.HINDFOOT_SCORE_L += $scope.FORM.POSTERIOR_CREASE_L;
        }

        if( ! isNaN($scope.FORM.EMPTY_HEEL_L)){
            $scope.FORM.HINDFOOT_SCORE_L += $scope.FORM.EMPTY_HEEL_L;
        }

        if( ! isNaN($scope.FORM.RIGID_EQUINUS_L)){
            $scope.FORM.HINDFOOT_SCORE_L += $scope.FORM.RIGID_EQUINUS_L;
        }

        if( ! isNaN($scope.FORM.POSTERIOR_CREASE_R)){
            $scope.FORM.HINDFOOT_SCORE_R += $scope.FORM.POSTERIOR_CREASE_R;
        }

        if( ! isNaN($scope.FORM.EMPTY_HEEL_R)){
            $scope.FORM.HINDFOOT_SCORE_R += $scope.FORM.EMPTY_HEEL_R;
        }

        if( ! isNaN($scope.FORM.RIGID_EQUINUS_R)){
            $scope.FORM.HINDFOOT_SCORE_R += $scope.FORM.RIGID_EQUINUS_R;
        }

        $scope.FORM.TOTAL_SCORE_L = $scope.FORM.MIDFOOT_SCORE_L +  $scope.FORM.HINDFOOT_SCORE_L;
        $scope.FORM.TOTAL_SCORE_R = $scope.FORM.MIDFOOT_SCORE_R +  $scope.FORM.HINDFOOT_SCORE_R;
    }

   

    

    $scope.SubClinics = function(){
        return SubClinicServices.Data();
    }

    $scope.SubClinicsStatus = function(){
        return SubClinicServices.Status();
    }

    $scope.Get_Lastest_Prescription = function(){

        var confirm = $mdDialog.confirm()
        .title('Confirmation')
        .textContent('Do you want to add the previous prescription?')
        .ariaLabel('Lastest Prescription')
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function() {

            $http.get( global.baseUrl + 'medicals/get-latest-prescription/' + $scope.FORM.PATIENT.ID +'/'+ $scope.FORM.ID , global.ajaxConfig).then( function(response){

                if ( response.data == 'RELOGIN' ){
                    global.Alert('Please try again');
                }
                else {  

                    if( response.data.length > 0 ){

                        var tempDetail = MRFormServices.Format_Medicines(response.data);

                        angular.forEach( tempDetail, function(v,k){
                            v.ID = 0;
                            $scope.FORM.MEDICINES.push(v);
                        });
                    }
                    else{
                        global.Alert('No prescription found.');
                    }
                }
            },
            function(err){
                global.Alert( err.statusText, 'Error ' + err.status);
            });

            
        }, function(err) {
            global.Alert( err.statusText, 'Error ' + err.status);
        });
    }

    $scope.Medicines_Filter = function(detail){

        var data = [];
        var deferred = $q.defer();

        $timeout(function () {

            if( detail.searchMeds != '' ){

                data = $filter('filter')( MedicinesServices.Data(), {NAME: detail.searchMeds});
            }
            else{

                data = $filter('filter')( MedicinesServices.Data(), {ID: detail.MEDICINEID});
            }
            data = $filter('limitTo')( data, 50,0);  

            deferred.resolve(data);

        }, 50, false);

        return deferred.promise;
    }
    $scope.Medicines_Selected = function(medInfo, detail){
        if( medInfo ){
            detail.MEDICINEID = medInfo.ID;
            detail.searchMeds = medInfo.NAME;
            detail.searchMedsPrev = medInfo.NAME;
        }
        else{
            if( detail.MEDICINEID > 0 )
                detail.searchMeds = detail.searchMedsPrev;
        }
    }
    $scope.Medicines = function(){
        return MedicinesServices.Data();
    }

    $scope.Durations = function(){
        return [{NAME: '1 Day'}, {NAME: '2 Days'}, {NAME: '3 Days'}, {NAME: '4 Days'}, {NAME: '5 Days'}, {NAME: '6 Days'}, {NAME: '7 Days'}, {NAME: '1 Week'}, {NAME: '2 Weeks'}, {NAME: '3 Weeks'}, {NAME: '1 Month'}];
    }

    $scope.Instructions = function(search){

        return $filter('filter')( InstructionsServices.Data(), {NAME: search, ACTIVE: true}); 
    }

    $scope.LabMonitoring = function(search){

        return $filter('filter')( LabMonitoringServices.Data(), {NAME: search, ISACTIVE: true}); 
    }

    $scope.LabMonitoringServices_Data = function(){
        return LabMonitoringServices.Data(); 
    }

    $scope.Add_Prescription = function(){
        $scope.FORM.MEDICINES.push({
            ID:0,
            MEDICINEID: 0, 
            FREQUENCY:'', 
            QUANTITY: 0,
            INSTRUCTION:'', 
            CANCELLED: false,
            searchMeds: ''
        });
    }


    $scope.Remove_Prescription = function(k){

        if ($scope.FORM.MEDICINES[k].ID > 0) { 
            $scope.FORM.MEDICINES[k].CANCELLED = !$scope.FORM.MEDICINES[k].CANCELLED; 
        }
        else{ 
            $scope.FORM.MEDICINES.splice(k,1); 
        }
    }

    $scope.Procedures = function(){
        return ProceduresServices.Data();
    }

    $scope.Procedures_Import_Description = function(){
        
        ProceduresServices.Data().forEach((v)=>{
            if(v.ID == $scope.FORM.PROCEDURESID){
                $scope.FORM.PROCEDURE_DONE = $scope.FORM.PROCEDURE_DONE +"\n"+ v.DESCRIPTION;
            }
        })
    }


    $scope.Services = function(){
        return ServicesServices.Data();
    }

    $scope.Add_Services = function(){

        $scope.FORM.SERVICES.push({
            ID:0, 
            SERVICEID:'', 
            QUANTITY:1, 
            UNITPRICE : 0,
            AMOUNT : 0,
            CANCELLED:false
        });
    }

    $scope.Remove_Services = function(k){

        if ($scope.FORM.SERVICES[k].ID > 0) { 
            $scope.FORM.SERVICES[k].CANCELLED = !$scope.FORM.SERVICES[k].CANCELLED; 
        }
        else{ 
            $scope.FORM.SERVICES.splice(k,1); 
        }

        $scope.Calculate_Services();
    }


    $scope.Lookup_Service = function(SERVICE){
        
        SERVICE.UNITPRICE = 0;

        angular.forEach( ServicesServices.Data(), function(v,k){
            if( v.ID == SERVICE.SERVICEID ){
                SERVICE.UNITPRICE = v.PRICE; 
            }
        });

        $scope.Calculate_Services();
    }


    $scope.Calculate_Services = function(){

        var total = 0;

        if( $scope.FORM != null ){

            $scope.FORM.GROSSAMOUNT = 0;

            angular.forEach( $scope.FORM.SERVICES, function(val,key){ 
                if( !val.CANCELLED){
                    val.AMOUNT =  parseFloat((val.UNITPRICE * val.QUANTITY).toFixed(2));
                    total += val.AMOUNT;
                }
            });

            $scope.FORM.GROSSAMOUNT = total;
        }

        $scope.Billing_Changed();
    }



    $scope.Discounts = function(){
        return DiscountsServices.Data();
    }

    $scope.Add_Discounts = function(){

        $scope.FORM.DISCOUNTS.push({
            ID:0, 
            DISCOUNTID:'',
            PERCENTAGE : false,
            PERCENT: 0, 
            AMOUNT: 0,
            CANCELLED: false
        });
    }



    $scope.Remove_Discounts = function(k){

        if ($scope.FORM.DISCOUNTS[k].ID > 0) { 
            $scope.FORM.DISCOUNTS[k].CANCELLED = !$scope.FORM.DISCOUNTS[k].CANCELLED; 
        }
        else{ 
            $scope.FORM.DISCOUNTS.splice(k,1); 
        }

        $scope.Calculate_Discount();
    }


    $scope.Lookup_Discount = function(DISCOUNT){

        DISCOUNT.PERCENTAGE = false;
        DISCOUNT.PERCENT = 0;
        DISCOUNT.AMOUNT = 0;

        angular.forEach( DiscountsServices.Data(), function(v,k){
            if( v.ID == DISCOUNT.DISCOUNTID ){
                DISCOUNT.PERCENTAGE = v.PERCENTAGE;
                DISCOUNT.PERCENT = v.AMOUNT;  
                DISCOUNT.AMOUNT = parseFloat(v.AMOUNT);
            }
        });

        $scope.Calculate_Discount();
    }


    $scope.Calculate_Discount = function(){

        var deduction = 0;

        if( $scope.FORM != null ){

            var gross = $scope.FORM.GROSSAMOUNT;

            $scope.FORM.DISCOUNTAMOUNT = 0;

            if( gross > 0 ){

                angular.forEach( $scope.FORM.DISCOUNTS, function(val,key){ 

                    if(isNaN(val.AMOUNT)) val.AMOUNT = 0;

                    if ( !val.CANCELLED ) {

                        if( val.PERCENTAGE )  val.AMOUNT = parseFloat((gross * (val.PERCENT/100)).toFixed(2));

                        deduction += parseFloat(val.AMOUNT); 
                    }
                });
            }

            $scope.FORM.DISCOUNTAMOUNT = deduction;
        }

        $scope.Billing_Changed();
    }


    $scope.HMO = function(){
        return HMOServices.Data();
    }


    $scope.Amount_As_Paid = function(){

        $scope.FORM.PAIDAMOUNT = $scope.FORM.AMOUNT;

        $scope.Billing_Changed();
    }

    $scope.Clear_Cheque = function(){

        var confirm = $mdDialog.confirm()
        .title('Cheque')
        .textContent('Are you sure to clear the Cheque Information?')
        .ariaLabel('Clear Cheque Info')
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function () {

            $scope.FORM.CHEQUEBANKNAME = '';
            $scope.FORM.CHEQUENO = '';
            $scope.FORM.CHEQUEDATE = null;
            $scope.FORM.CHEQUEAMOUNT = 0;
            $scope.FORM.CHEQUEACCNO = '';
            $scope.FORM.CHEQUEACCNAME = '';
            $scope.Billing_Changed();

        }, function () {
            
        });
    }

    $scope.Clear_Insurance = function(){

        var confirm = $mdDialog.confirm()
        .title('HMO')
        .textContent('Are you sure to clear the HMO Information?')
        .ariaLabel('Clear HMO Info')
        .ok('Yes')
        .cancel('No');

        $mdDialog.show(confirm).then(function () {

            $scope.FORM.HMOID = null;
            $scope.FORM.HMORECEIVED = false;
            $scope.FORM.HMODATE = null;
            $scope.FORM.HMOAMOUNT = 0;                
            $scope.FORM.HMOCHEQUENO = '';
            $scope.FORM.HMOCHEQUEDATE = null;
            $scope.Billing_Changed();

        }, function () {
            
        });
    }


    $scope.Toggle_HMO = function(){

        if( $scope.FORM != undefined ){
            if( ! $scope.FORM.HMOID ){
                $scope.FORM.HMORECEIVED = false;
                $scope.FORM.HMODATE = null;
                $scope.FORM.HMOAMOUNT = 0;                
                $scope.FORM.HMOCHEQUENO = '';
                $scope.FORM.HMOCHEQUEDATE = null;
            }
            else if(!$scope.FORM.HMORECEIVED){
                $scope.FORM.HMODATE = null;
                $scope.FORM.HMOAMOUNT = 0;                
                $scope.FORM.HMOCHEQUENO = '';
                $scope.FORM.HMOCHEQUEDATE = null;
            }
        }

        $scope.Billing_Changed();
    }


    $scope.Toggle_PhilHealth = function(){

        if( $scope.FORM.PHILHEALTH ){
            if( ! $scope.FORM.PHILHEALTHRECEIVED ){
                $scope.FORM.PHILHEALTHRECEIVED = false;
                $scope.FORM.PHILHEALTHCHEQUENO = '';
                $scope.FORM.PHILHEALTHCHEQUEDATE = null;
                $scope.FORM.PHILHEALTHAMOUNT = 0;                
            }
        }
        else{
            $scope.FORM.PHILHEALTHRECEIVED = false;
            $scope.FORM.PHILHEALTHCHEQUENO = '';
            $scope.FORM.PHILHEALTHCHEQUEDATE = null;
            $scope.FORM.PHILHEALTHAMOUNT = 0;
        }

        $scope.Billing_Changed();
    }


    $scope.Billing_Changed = function(){

        if( $scope.FORM != undefined ){

            $scope.FORM.NETPAYABLES = $scope.FORM.GROSSAMOUNT - $scope.FORM.DISCOUNTAMOUNT;
            $scope.FORM.AMOUNTDUE = $scope.FORM.NETPAYABLES - ($scope.FORM.PAIDAMOUNT + $scope.FORM.HMOAMOUNT + $scope.FORM.CHEQUEAMOUNT + $scope.FORM.PHILHEALTHAMOUNT);

            $scope.FORM.AMOUNTCHANGE = $scope.FORM.AMOUNT - $scope.FORM.PAIDAMOUNT;

            // if( $scope.FORM.AMOUNTDUE >= 0 ){

            //     $scope.FORM.AMOUNTDUE -= $scope.FORM.AMOUNT;

            //     if( $scope.FORM.AMOUNTDUE > 0 ){
            //         $scope.FORM.PAIDAMOUNT = $scope.FORM.AMOUNT;
            //         $scope.FORM.AMOUNTCHANGE = 0;
            //     }
            //     else{
            //         // POSITIVE BCUZ AMOUNTDUE CAN BE NEGATIVE
            //         $scope.FORM.PAIDAMOUNT = $scope.FORM.AMOUNT + $scope.FORM.AMOUNTDUE;                    
            //         $scope.FORM.AMOUNTCHANGE = $scope.FORM.AMOUNT - $scope.FORM.PAIDAMOUNT;
            //         $scope.FORM.AMOUNTDUE = 0;
            //     }                    
            // }
            // else{                
            //     $scope.FORM.AMOUNTDUE = 0;
            //     $scope.FORM.AMOUNTCHANGE = 0;
            //     $scope.FORM.PAIDAMOUNT = 0;
            // }            
        }
    }


    $scope.Fully_Paid = function(){

        // FOR AUTO FILL AMOUNT
        if( $scope.FORM.PAYMODE == 'CASH' ){
            $scope.FORM.AMOUNT = $scope.FORM.NETPAYABLES;
        }
        else if( $scope.FORM.PAYMODE == 'CHARGE' ){
            $scope.FORM.HMOAMOUNT = $scope.FORM.NETPAYABLES;
        }
        else if( $scope.FORM.PAYMODE == 'CHEQUE'  ){TEMPLATERESULT
            $scope.FORM.CHEQUEAMOUNT = $scope.FORM.NETPAYABLES;
        }

        $scope.Billing_Changed();
    }


    $scope.Laboratory = function(){
        return LaboratoryServices.Data();
    }

    $scope.Add_Laboratory = function( LABORATORY ){

        $scope.FORM.LABORATORIES.push({
            ID: 0,
            LABORATORYID : LABORATORY.ID,
            TEMPLATERESULT: LABORATORY.TEMPLATE, 
            CANCELLED: false,
            REF: LABORATORY
        });
    }

    $scope.Remove_Laboratory = function(key, LABORATORY ){

        if( LABORATORY.ID > 0 ){
            LABORATORY.CANCELLED = !LABORATORY.CANCELLED;
        }
        else{
            $scope.FORM.LABORATORIES.splice(key, 1);
        }
    }

   


    $scope.MyClinic = function(){
        return ClinicServices.Data();
    }

    $scope.Images = function(){
        return MRFormServices.Images();
    }


    $scope.Copy_CO_Morbidities = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Copy the previous CO-Morbidities?')
		.ariaLabel('CO-Morbidities')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			if( $scope.FORM.PREVIOUS != null ){
				$scope.FORM.COMORBIDITIES = $scope.FORM.PREVIOUS.COMORBIDITIES;
			}
			else{
				global.Alert('No previous record.');
			}
			
		}, function() {
			
		});
	}


	$scope.Copy_Findings = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Copy the previous findings?')
		.ariaLabel('Findings')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			if( $scope.FORM.PREVIOUS != null ){
				$scope.FORM.FINDINGS = $scope.FORM.PREVIOUS.FINDINGS;
			}
			else{
				global.Alert('No previous record.');
			}
			
		}, function() {
			
		});
	}

	$scope.Copy_Diagnosis = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Copy the previous Diagnosis?')
		.ariaLabel('Diagnosis')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			if( $scope.FORM.PREVIOUS != null ){
				$scope.FORM.DIAGNOSIS = $scope.FORM.PREVIOUS.DIAGNOSIS;
			}
			else{
				global.Alert('No previous record.');
			}
			
		}, function() {
			
		});
	}


	$scope.Copy_Procedure = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Copy the previous Procedures done?')
		.ariaLabel('Procedure')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			if( $scope.FORM.PREVIOUS != null ){
				$scope.FORM.PROCEDURE_DONE = $scope.FORM.PREVIOUS.PROCEDURE_DONE;
			}
			else{
				global.Alert('No previous record.');
			}
			
		}, function() {
			
		});
	}


    
	$scope.Copy_Instruction = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Copy the previous Instruction?')
		.ariaLabel('Instruction')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			if( $scope.FORM.PREVIOUS != null ){
				$scope.FORM.INSTRUCTION = $scope.FORM.PREVIOUS.INSTRUCTION;
			}
			else{
				global.Alert('No previous record.');
			}
			
		}, function() {
			
		});
	}

	$scope.Copy_Medication = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Copy the previous Medication?')
		.ariaLabel('Medication')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			if( $scope.FORM.PREVIOUS != null ){
				$scope.FORM.MEDICATION = $scope.FORM.PREVIOUS.MEDICATION;
			}
			else{
				global.Alert('No previous record.');
			}
			
		}, function() {
			
		});
    }
    

    $scope.Report_View = function( REPORTTYPE ){

        var url = '';
        var title = '';

        if( REPORTTYPE == 'PRESCRIPTION' ){
            url = global.baseUrl + 'medicals/report/' + $scope.FORM.ID + '/medical-prescription';
            title = 'Prescription';
        }
        else if( REPORTTYPE == 'CERTIFICATE' ){
            url = global.baseUrl + 'medicals/report/' + $scope.FORM.ID + '/medical-certificate';
            title = 'Medical Certificate';
        }
        else if( REPORTTYPE == 'REFERRAL' ){
            url = global.baseUrl + 'medicals/report/' + $scope.FORM.ID + '/referral-letter';
            title = 'Referral Letter';
        }
        else if( REPORTTYPE == 'CLEARANCE' ){
            url = global.baseUrl + 'medicals/report/' + $scope.FORM.ID + '/clearance-letter';
            title = 'Clearance Letter';
        }
        else if( REPORTTYPE == 'INSTRUCTION' ){
            url = global.baseUrl + 'medicals/report/' + $scope.FORM.ID + '/instruction-letter';
            title = 'Instruction';
        }
        
        Preview.Report(url, title);
    }
	
	$scope.Physical_Exam = function () {
		return ["The patient is physically FIT TO WORK", "Medical Assistance", 'The patient is advised to rest for'];
	};

	$scope.Selected_Physical_Exam = function (PHYSICALEXAM) {
		if ($scope.FORM.REMARKS == undefined) $scope.FORM.REMARKS = " ";

		$scope.FORM.REMARKS = $scope.FORM.REMARKS + PHYSICALEXAM + " ";
	};

    $scope.Email_Prescription = function(){

        $http.post( global.baseUrl +'medicals/email-prescription',{TOKEN: $scope.FORM.TOKEN}, global.ajaxConfig).then(function(response){

            if( response.data.err != '' ){
                global.Alert(response.data.err);
            }
            else{
                global.Toast('PRESCRIPTION SEND');
            }

        },function(err){
            global.Alert( err.statusText, 'Error ' + err.status);
        });
    }

        

    $scope.Email_Certificate = function(){

        $http.post( global.baseUrl +'medicals/email-certificate',{TOKEN: $scope.FORM.TOKEN}, global.ajaxConfig).then(function(response){

            if( response.data.err != '' ){
                global.Alert(response.data.err);
            }
            else{
                global.Toast('CERTIFICATE SEND');
            }

        },function(err){
            global.Alert( err.statusText, 'Error ' + err.status);
        });
    }


    $scope.Init_Lab_Option = function(LAB) {

        if( LAB.isFiles == undefined ) LAB.isFiles = false;
        LAB.isFiles = !LAB.isFiles;

        $scope.Dz_Lab_Option = {
            url : global.baseUrl + 'medicals/submit-upload-laboratory/' + $scope.FORM.ID +'/' + LAB.ID,
            paramName : 'file',
            maxFilesize : '2',
            parallelUploads : 1,
            acceptedFiles : 'image/*,.pdf',
            dictDefaultMessage : 'Click to add or drop photos to upload',
            addRemoveLinks : true
        };


        $scope.Dz_Lab_Callback={
            'addedfile' : function(file){
                //$scope.newFile = file;
            },
            'success' : function(file, xhr){
    
                if( JSON.parse(xhr) ) {
                    var newFile = JSON.parse(xhr);
                                        
                    angular.forEach($scope.FORM.LABORATORIES, function(v,k){
                        if( v.ID == newFile.ID){
                            v.IMAGEPATH = newFile.IMAGEPATH;
                            v.FILENAME = newFile.FILENAME;
                            v.EXTENSION = newFile.EXTENSION;

                            v.isPDF = MRFormServices.Is_PDF_File(v.EXTENSION);
                            v.isImage = MRFormServices.Is_Image_File(v.EXTENSION);
                            
                            v.isFiles = false;
                        }
                    })
                }
            },
            'error' : function(file, xhr){
                console.error(xhr);
            },
            'queuecomplete': function(){
                LAB.isFiles = false;
            }
        }
    }


    $scope.Lab_View = function(LAB) {

		$mdDialog.show({
			templateUrl: 'views/client/modal_view_files.html',
			fullscreen: true,
            clickOutsideToClose: true,
            locals:{
                LAB:{
                    FILENAME: LAB.FILENAME,
                    IMAGEPATH: LAB.IMAGEPATH
                }
            },
			controller: function($scope,$mdDialog, LAB){

                $scope.FILE = LAB;

                $scope.Close = function(){
                    $mdDialog.cancel();
                }
            }
        })
		.then(function(answer) {

		}, function() {

		});
    };


    
    $scope.Toggle_Files = function(){

        $scope.isFiles = ! $scope.isFiles;

        var errorUpload = false;

        $scope.dzMethods = {};
        
        $scope.dzOptions = {
            url : global.baseUrl + 'medicals/submit-upload/' + $scope.FORM.ID,
            paramName : 'file',
            maxFilesize : '4',
            parallelUploads : 5,
            acceptedFiles : 'image/*,.pdf',
            dictDefaultMessage : 'Click to add or drop photos to upload',
            addRemoveLinks : true
        }; 
    
        $scope.dzCallbacks = {
            'addedfile' : function(file){
                //$scope.newFile = file;
            },
            'success' : function(file, xhr){
    
                if( JSON.parse(xhr) ) {

                    var newFile = JSON.parse(xhr);
                    MRFormServices.Append_Image(newFile);                    
                }

                $scope.dzMethods.removeFile(file);
            },
            'error' : function(file, xhr){
                console.error(xhr);
                errorUpload = true;
            },
            'queuecomplete': function(){
                if( ! errorUpload ){
                    $scope.isFiles = false;
                    global.Toast('All Uploaded');
                }
            }
        };
    }
 
    $scope.File_View = function(FILE) {

		$mdDialog.show({
			templateUrl: 'views/client/modal_view_files.html',
			fullscreen: true,
            clickOutsideToClose: true,
            locals:{
                FILE
            },
			controller: function($scope,$mdDialog, FILE){

                $scope.FILE = FILE;

                $scope.Close = function(){
                    $mdDialog.cancel();
                }
            }
        })
		.then(function(answer) {

		}, function() {

		});
    };


    $scope.Search_ICD = function(){

        if( $scope.icd.search != '' ){
            var temp  = $filter('filter')( ICDServices.Data(), $scope.icd.search );

            return $filter('limitTo')( temp, 50 );
        }
        else{
            return $filter('limitTo')( ICDServices.Data(), 50 );
        }
    }

    $scope.Selected_ICD = function(item){
        
        if( item ){
            $scope.icd.selected = item;
            $scope.icd.edit = false;
            $scope.FORM.ICD_CODE = item.ITEMCODE;            
        }
        else{
            $scope.icd.search = '';
            $scope.icd.selected = null;
            $scope.icd.edit = true;
            $scope.FORM.ICD_CODE = ''; 
        }
    }

    $scope.Clear_ICD = function(){

        if( $scope.icd.search == ''){
            $scope.FORM.ICD_CODE = ''; 
        }
    }


    $scope.Doctor_List = function(){

        return UsersServices.Data().filter(v => v.isDoctor);
    }

    
    //------P--------------------


    $scope.Search_RVS = function(){

        if( $scope.rvs.search != '' ){
            var temp  = $filter('filter')( RVSServices.Data(), $scope.rvs.search );

            return $filter('limitTo')( temp, 50 );
        }
        else{
            return $filter('limitTo')( RVSServices.Data(), 50 );
        }
    }

    $scope.Selected_RVS = function(item){
        
        if( item ){
            $scope.rvs.selected = item;
            $scope.rvs.edit = false;
            $scope.FORM.RVS_CODE = item.ITEMCODE;            
        }
        else{
            $scope.rvs.search = '';
            $scope.rvs.selected = null;
            $scope.rvs.edit = true;
            $scope.FORM.RVS_CODE = ''; 
        }
    }

    $scope.Clear_RVS = function(){
        if( $scope.rvs.search == ''){
            $scope.FORM.RVS_CODE = ''; 
        }
    }

    // ----------------

    $scope.Change_Layout = function (VALUE){
        global.layout.value = VALUE;
        global.Change_Layout();
    }

    $scope.Print_Bluetooth = function(){

        var url = 'https://' + window.location.host +'/report-mini-mr-billing/'+ $scope.FORM.ID + '/' +  MeServices.Data().ID;
                        
        var link = 'my.bluetoothprint.scheme://' + url;
            
        document.location = link;
        console.log(url);
    }
    
    $scope.Init = function(){

        ClinicServices.Reload().then(function(data){});

        MRFormServices.Form($routeParams.P1, $routeParams.P2).then(function(data){

            $scope.FORM = data;

            if( $scope.FORM.SUBCLINICID == 0 || $scope.FORM.SUBCLINICID == null )
                $scope.FORM.SUBCLINICID = $scope.Me().SUBCLINICID;
            
            if( data ){

                $q.all([
                    SubClinicServices.Reload(),
                    LaboratoryServices.Reload(),
                    LabMonitoringServices.Reload(),
                    MedicinesServices.Reload(),
                    InstructionsServices.Reload(),
                    ServicesServices.Reload(),
                    DiscountsServices.Reload(),
                    HMOServices.Reload(),
                    ICDServices.Reload(),
                    RVSServices.Reload(),
                    ProceduresServices.Reload(),
                    UsersServices.Reload()
                ]).then(function(result){

                    if( SubClinicServices.Data().length == 0 ){
                        global.Alert('Please add sub-clinic first');
                        $location.url('/myclinic');
                        return;
                    }

                    // if New Form 
                    if( $scope.FORM.ID == 0){
                        
                        // add auto add services
                        angular.forEach( ServicesServices.Data(), function(v,k){
                            if( v.AUTOADD ){
        
                                $scope.Add_Services();
                                var key = $scope.FORM.SERVICES.length - 1;
        
                                $scope.FORM.SERVICES[key].SERVICEID = v.ID;
                                $scope.Lookup_Service($scope.FORM.SERVICES[key]);
                            }                    
                        });
                    }
                    else{

                        // icd apply if have
                        if( $scope.FORM.ICD_CODE ){
                            $scope.icd.search = $scope.FORM.ICD_CODE;
                            $scope.Selected_ICD( $filter('filter')( ICDServices.Data(),{ITEMCODE: $scope.FORM.ICD_CODE}, true)[0]);
                        }
                        else{
                            $scope.Selected_ICD();
                        }

                        // rvs apply if have
                        if( $scope.FORM.RVS_CODE ){
                            $scope.rvs.search = $scope.FORM.RVS_CODE;
                            $scope.Selected_RVS( $filter('filter')( RVSServices.Data(),{ITEMCODE: $scope.FORM.RVS_CODE}, true)[0]);
                        }
                        else{
                            $scope.Selected_RVS();
                        }                       
                    }


                    // if detail laboratories is exist apply the reference
                    if( $scope.FORM.LABORATORIES.length > 0 ){
                        angular.forEach( LaboratoryServices.Data(), function(v,k){
                            angular.forEach( $scope.FORM.LABORATORIES, function(v1,k1){
                                if( v.ID == v1.LABORATORYID ){
                                    v1.REF = v;
                                }
                            });  
                        });
                    }

                    
                    // auto add lab monitoring if not found

                    LabMonitoringServices.Data().forEach((v)=>{

                        if( $scope.FORM.MRLABMONITORING.filter((v1) => v1.LABMONITORINGID === v.ID).length == 0 ){

                            $scope.FORM.MRLABMONITORING.push({
                                ID: 0,
                                MEDICALRECORDID: $scope.FORM.ID,
                                LABMONITORINGID: v.ID,
                                LABVALUE: '',
                                CANCELLED: false
                            })
                        }
                    })

                    // regroup per medical record
                    $scope.FORM.GROUP_LAB_MONITORING = [];

                    LabMonitoringServices.Data().forEach((v)=>{

                        if( $scope.FORM.GROUP_LAB_MONITORING.filter((v1) => v1.GROUPNAME === v.GROUPNAME).length == 0 ){
                            $scope.FORM.GROUP_LAB_MONITORING.push({
                                GROUPNAME: v.GROUPNAME,
                                DATA: [v]
                            })
                        }
                        else{
                            $scope.FORM.GROUP_LAB_MONITORING.forEach((v1)=>{
                                if( v1.GROUPNAME == v.GROUPNAME){
                                    v1.DATA.push(v);
                                }
                            })
                        }
                    })

                    var tempLabMonitoring = [];

                    $scope.FORM.PREV_MRLABMONITORING.forEach((v)=>{
                        if( tempLabMonitoring.filter((v1) => v1.MEDICALRECORDID === v.MEDICALRECORDID).length == 0 ){

                            tempLabMonitoring.push({
                                MEDICALRECORDID: v.MEDICALRECORDID,
                                CHECKUPDATE: v.CHECKUPDATE,
                                DATA: [v]
                            })
                        }
                        else{
                            tempLabMonitoring.forEach((v1)=>{
                                if( v1.MEDICALRECORDID === v.MEDICALRECORDID){
                                    v1.DATA.push(v);
                                }
                            });
                        }
                    })
                    
                    $scope.FORM.PREV_MRLABMONITORING = tempLabMonitoring;
                    
                    // USER PRIVILEGE PER RECORD
                    if( MeServices.Data().isAssistant ){

                        if( MeServices.Data().AUTHORIZATION && $scope.FORM.ID == 0 ){
        
                            $scope.FORM.READONLY = false;
                        }
                        else if( MeServices.Data().EDITMR && $scope.FORM.ID > 0 ){
        
                            $scope.FORM.READONLY = false;
                        }
                        else if( ! MeServices.Data().AUTHORIZATION && $scope.FORM.ID == 0 ){
        
                            global.Alert("Sorry your not authorized to create medical record.");
                            $scope.FORM.READONLY = true;
                            $location.url('/patient/'+ $routeParams.P1 + '/record');
                        }
                        else if( ! MeServices.Data().EDITMR && $scope.FORM.ID > 0 ){
        
                            global.Alert("Sorry you are not allowed to edit the record.");
                            $scope.FORM.READONLY = true;
                            $location.url('/patient/'+ $routeParams.P1 + '/record');
                        }
                        else{
                            $scope.FORM.READONLY = true;
                        }
                    }
                    else {   
        
                        if( $scope.FORM.CREATEDBY == 0 || $scope.FORM.CREATEDBY == null){
                            $scope.FORM.CREATEDBY = MeServices.Data().ID;
                        }
        
                        if( MeServices.Data().POSITION == 'BRANCH ADMINISTRATOR' ){
                            $scope.FORM.READONLY = false;
                        }
                        else if( $scope.FORM.CREATEDBY == MeServices.Data().ID ){
        
                            if( $scope.FORM.LASTMR == 0 ){
                                $scope.FORM.READONLY = false;
                            }
                            else if( $scope.FORM.ID == $scope.FORM.LASTMR ){
                                $scope.FORM.READONLY = false;
                            }
                            else{
                                $scope.FORM.READONLY = true;
                            }
                        }
                        else{
                            $scope.FORM.READONLY = true;  
                        }
                    }


                    if( $scope.FORM.ID > 0 ){
                        MRFormServices.Form_Images($scope.FORM.ID).then(function(data){ 
                            $scope.isLoaded = true;
                        });
                    }
                    else{
                        $scope.isLoaded = true;
                    }
        
                    $scope.Billing_Changed();        
                    
                });
            }

        });
    }
});

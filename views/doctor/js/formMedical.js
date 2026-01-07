"use strict";
// DOCTOR

app.controller('FormMedical',function ($scope, $http, $timeout, $filter, $rootScope, $routeParams, $mdDialog, $location, $q, $log, $mdSidenav, global){


	$scope.opt = {
		isLoaded : false,
		formUrl : '',
		submitUrl : global.baseUrl + 'medicals/submit-form/',
		cancelUrl : '#!/patients/record/'+ $routeParams.P1,
		uploadUrl : global.baseUrl + 'medicals/submit-upload/',
		getLatestPrescriptionUrl : global.baseUrl + 'medicals/get-latest-prescription/',
		isSubmit : false,
		uploadView : false,
		prescriptionUrl : '',
		medicalCertificateUrl : '',
		referralLetterUrl : '',
		clearanceLetterUrl: '',
		searchICD : '',
		editICD : false,
		searchRVS : '',
		editRVS : false,
		viewSales : global.USER.SALES
	}

	$scope.LIST = {
		SERVICES : [],
		DISCOUNTS : [],
		MEDICINES: [],
		LABORATORY : [],
		INSTRUCTIONS: [],
		SUBCLINICS: [],
		HMO: [],
		DURATIONS: [{NAME: '1 Day'}, {NAME: '2 Days'}, {NAME: '3 Days'}, {NAME: '4 Days'}, {NAME: '5 Days'}, {NAME: '6 Days'}, {NAME: '7 Days'}, {NAME: '1 Week'}, {NAME: '2 Weeks'}, {NAME: '3 Weeks'}, {NAME: '1 Month'}]
	};


	if( $routeParams.P2 == 'new' ){
		$scope.opt.formUrl = global.baseUrl +'medicals/form-data/' + $routeParams.P1 +'/0';
		$rootScope.$emit("Title_Global",{ TITLE : 'NEW MEDICAL RECORD', BACKURL: '#!/patients/record/'+ $routeParams.P1 });
	}
	else{
		$scope.opt.formUrl = global.baseUrl +'medicals/form-data/' + $routeParams.P1 +'/' + $routeParams.P2;
		$scope.opt.uploadUrl = global.baseUrl + 'medicals/submit-upload/' + $routeParams.P2;
		$scope.opt.prescriptionUrl =  global.baseUrl + 'medicals/report/' + $routeParams.P2 + '/medical-prescription';
		$scope.opt.medicalCertificateUrl =  global.baseUrl + 'medicals/report/' + $routeParams.P2 + '/medical-certificate';
		$scope.opt.referralLetterUrl =  global.baseUrl + 'medicals/report/' + $routeParams.P2 + '/referral-letter';
		$scope.opt.clearanceLetterUrl =  global.baseUrl + 'medicals/report/' + $routeParams.P2 + '/clearance-letter';
		$rootScope.$emit("Title_Global",{ TITLE : 'EDIT MEDICAL RECORD', BACKURL: '#!/patients/record/'+ $routeParams.P1 });
	}

	$scope.dzOptions = {
		url : $scope.opt.uploadUrl,
		paramName : 'file',
		maxFilesize : '10',
		parallelUploads : 5,
		acceptedFiles : 'image/*,.pdf',
		addRemoveLinks : true
	};


	$scope.dzCallbacks = {
		'addedfile' : function(file){
			//$scope.newFile = file;
		},

		'success' : function(file, xhr){

			if( JSON.parse(xhr) ) {

				var newFile = JSON.parse(xhr);
				newFile.CANCELLED = newFile.CANCELLED == 'Y' ? true : false;
				newFile.EXT = $scope.File_Is_Image(newFile.EXTENSION);

				$scope.$apply(function(){
					$scope.f.IMAGES.push(newFile);
				});

				$scope.opt.uploadView = false;
				global.Toast('Uploaded File.');
			}
		}
	};


	$scope.Toggle_Upload = function(ev) {

		$scope.opt.uploadView = ! $scope.opt.uploadView;
	};



	$scope.Load_Form = function(){

		$scope.opt.isSubmit = true;

		$http.get($scope.opt.formUrl, global.ajaxConfig).then( function(response)  {	

			if( response.data == 'RELOGIN'){
				global.Relogin();
			}
			else {

				if( response.data.TOKEN != undefined ){

					$scope.f = [];
					$scope.f = response.data;

					$scope.f.PATIENT.DOB = global.Date($scope.f.PATIENT.DOB);

					$scope.f.CHECKUPDATE =  global.Date($scope.f.CHECKUPDATE);
					$scope.f.APPOINTMENTDATE = global.Date($scope.f.APPOINTMENTDATE);
					$scope.f.HMODATE = global.Date($scope.f.HMODATE);

					if( $scope.f.LMP != null )
						$scope.f.LMP = global.Date($scope.f.LMP);

					if( $scope.f.CONFINEMENT_DATE_FROM != null )
						$scope.f.CONFINEMENT_DATE_FROM = global.Date($scope.f.CONFINEMENT_DATE_FROM);

					if( $scope.f.CONFINEMENT_DATE_TO != null )
						$scope.f.CONFINEMENT_DATE_TO = global.Date($scope.f.CONFINEMENT_DATE_TO);

					$scope.f.CASERATEID = $scope.f.CASERATEID == null ? 0 : parseInt($scope.f.CASERATEID);

					$scope.f.HMOAMOUNT = parseFloat($scope.f.HMOAMOUNT);
					$scope.f.AMOUNT = $scope.f.AMOUNT == null ? 0 : parseFloat($scope.f.AMOUNT);
					$scope.f.AMOUNTCHANGE = parseFloat($scope.f.AMOUNTCHANGE);
					$scope.f.PAIDAMOUNT = parseFloat($scope.f.PAIDAMOUNT);

					$scope.f.APPOINTMENT = $scope.f.APPOINTMENT == 'Y' ? true : false;
					$scope.f.CANCELLED = $scope.f.CANCELLED == 'Y' ? true : false;
					$scope.f.HMORECEIVED = $scope.f.HMORECEIVED == 'Y' ? true : false;

					$scope.f.PAYMODE = $scope.f.PAYMODE == null ? 'CASH' : ($scope.f.PAYMODE == '' ? 'CASH' : $scope.f.PAYMODE);

					if( $scope.f.SPECIALISTID == null || $scope.f.SPECIALISTID == '' || $scope.f.SPECIALISTID == 0 )
						$scope.f.SPECIALISTID = global.USER.SPECIALISTID;

					if( $scope.f.SUBCLINICID == 0 || $scope.f.SUBCLINICID == null )
						$scope.f.SUBCLINICID = global.USER.SUBCLINICID;

					$scope.Calc_BMI();

					$scope.opt.editICD = false
					$scope.opt.editRVS = false;

					$scope.HMO();

					angular.forEach($scope.f.SERVICES,function(val,key){ 
						val.QUANTITY = parseInt(val.QUANTITY);
						val.UNITPRICE = parseFloat(val.UNITPRICE);
						val.AMOUNT = parseFloat(val.AMOUNT);
						val.CANCELLED = val.CANCELLED == 'Y' ? true : false;

						if (val.UNITPRICE < 1) $scope.Lookup_Service(val);
					});

					angular.forEach($scope.f.DISCOUNTS,function(val,key){ 
						val.AMOUNT = parseFloat(val.AMOUNT);
						val.PERCENT = parseFloat(val.PERCENT);
						val.PERCENTAGE = val.PERCENTAGE == 'Y' ? true : false;
						val.CANCELLED = val.CANCELLED == 'Y' ? true : false;
					});

					angular.forEach($scope.f.DISEASES,function(val,key){ 
						val.view = val.view == 'Y' ? true : false;
						val.CANCELLED = val.CANCELLED == 'Y' ? true : false;
					});


					angular.forEach($scope.f.MEDICINES,function(val,key){ 
						val.search = '';
						val.QUANTITY = parseInt(val.QUANTITY);
						val.CANCELLED = val.CANCELLED == 'Y' ? true : false;
					});

					angular.forEach($scope.f.LABORATORIES,function(val,key){ 
						val.CANCELLED = val.CANCELLED == 'Y' ? true : false;

						val.REF = $filter('filter')( $scope.LIST.LABORATORY,function(v1,k1){ if( v1.ID == val.LABORATORYID) return v1; })[0];
					});

					angular.forEach($scope.f.IMAGES,function(val,key){ 
						val.CANCELLED = val.CANCELLED == 'Y' ? true : false;
						val.EXT = $scope.File_Is_Image(val.EXTENSION);
					});



					angular.forEach($scope.LIST.SERVICES,function(v,k){
						if( $scope.f.ID == 0 && v.AUTOADD ){
							$scope.Add_Services();
							$scope.f.SERVICES[$scope.f.SERVICES.length - 1].SERVICEID = v.ID;
							$scope.Lookup_Service( $scope.f.SERVICES[$scope.f.SERVICES.length - 1] );
						}
					});


					$scope.Calculate_Services();
					$scope.Calculate_Discount();
				}
				else {

					global.Toast(response.data.RESPONSE);
					$location.url('/patients/record/'+ $routeParams.P1);
				}

				$scope.opt.isSubmit = false;
			}

			$scope.remove = false;
			$scope.opt.isLoaded = true;

		}, 
		function(response){ 
			global.Relogin();
			$scope.opt.isLoaded = true; 
		});
		//
	}



	$scope.Calc_BMI = function(){

		var height = $scope.f.HEIGHT;
		var weight = $scope.f.WEIGHT;


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
			$scope.f.BMI = ((weight / height / height) * 10000).toFixed(1);
		}
		else{
			$scope.f.BMI = 0;
		}
	}


	$scope.Submit_Form = function(){

		$scope.opt.isSubmit = true;

		$http.post($scope.opt.submitUrl ,$scope.f, global.ajaxConfig).then( function(response){

			$scope.opt.isSubmit = false;

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM');
			}
			else if( response.data.err != undefined ) {

				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {

					if( $scope.f.ID == 0){
						$location.url('/patients/'+ $routeParams.P1 +'/medical-record/'+ response.data.suc.MRID);
					}
					else{
						$scope.Load_Form();	
					}

					global.Toast('SAVED');
				}
			}
			else{
				global.Relogin('FORM');
			}

		},
		function(response){
			global.Relogin('FORM');
			$scope.opt.isSubmit = false;
		});

	}



	$scope.Search_ICD = function(query) {

		if( query != '' ){

			var deferred = $q.defer();

			$timeout(function () { 

				deferred.resolve( $filter('filter')(global.ICD,query) ); 

			}, Math.random() * 1000, false);

			return deferred.promise;
		}
		else{

			return $filter('limitTo')(global.ICD, 200, 0);
		}
	}

	$scope.Selected_ICD = function(ITEM){

		if( ITEM != undefined){
			if( ITEM.ITEMCODE != undefined){
				$scope.f.ICD_CODE = ITEM.ITEMCODE;
				$scope.f.ICD_DESCRIPTION = ITEM.ITEMDESCRIPTION;
				$scope.opt.editICD = false;
			}
		}
	}

	$scope.Clear_ICD = function(){
		$scope.f.ICD_CODE = '';
		$scope.f.ICD_DESCRIPTION =  '';
	}



	$scope.Search_RVS = function(query) {

		if( query != '' ){

			var deferred = $q.defer();

			$timeout(function () { 

				deferred.resolve( $filter('filter')(global.RVS, query) ); 

			}, Math.random() * 1000, false);

			return deferred.promise;
		}
		else{

			return $filter('limitTo')(global.RVS, 200, 0);
		}
	}


	$scope.Selected_RVS = function(ITEM){

		if( ITEM != undefined){
			if( ITEM.ITEMCODE != undefined){
				$scope.f.RVS_CODE = ITEM.ITEMCODE;
				$scope.f.RVS_DESCRIPTION = ITEM.ITEMDESCRIPTION;
				$scope.opt.editRVS = false;
			}
		}
	}

	$scope.Clear_RVS = function(){
		$scope.f.RVS_CODE = '';
		$scope.f.RVS_DESCRIPTION =  '';
	}


	$scope.Add_Laboratory = function( LABORATORY ){

		var data = $filter('filter')($scope.f.LABORATORIES, function(v,k){ if(v.LABORATORYID == LABORATORY.ID) return v; })[0];

		if( data == undefined ){
			$scope.f.LABORATORIES.push({
				ID: 0,
				LABORATORYID : LABORATORY.ID,
				TEMPLATERESULT: LABORATORY.TEMPLATE, 
				CANCELLED: false,
				REF: LABORATORY
			});
		} else {
			global.Alert(LABORATORY.NAME + ' template is already added.');
		}
	}

	$scope.Remove_Laboratory = function(k){
		if ($scope.f.LABORATORIES[k].ID > 0) { $scope.f.LABORATORIES[k].CANCELLED = !$scope.f.LABORATORIES[k].CANCELLED; }
		else{ $scope.f.LABORATORIES.splice(k,1); }
	}

	$scope.Add_Prescription = function(){
		$scope.f.MEDICINES.push({
			ID:0,
			MEDICINEID:'', 
			FREQUENCY:'', 
			QUANTITY: 0,
			INSTRUCTION:'', 
			CANCELLED: false,
			search: ''
		});
	}

	$scope.Remove_Prescription = function(k){
		if ($scope.f.MEDICINES[k].ID > 0) { $scope.f.MEDICINES[k].CANCELLED = !$scope.f.MEDICINES[k].CANCELLED; }
		else{ $scope.f.MEDICINES.splice(k,1); }
	}

	


	$scope.Add_Services = function(){

		$scope.f.SERVICES.push({
			ID:0, 
			SERVICEID:'', 
			QUANTITY:1, 
			UNITPRICE : 0,
			AMOUNT : 0,
			CANCELLED:false
		});
	}

	$scope.Remove_Services = function(k){

		if ($scope.f.SERVICES[k].ID > 0) { $scope.f.SERVICES[k].CANCELLED = !$scope.f.SERVICES[k].CANCELLED; }
		else{ $scope.f.SERVICES.splice(k,1); }

		$scope.Calculate_Services();
		$scope.Calculate_Discount();
	}


	$scope.Lookup_Service = function(SERVICE){

		var data = $filter('filter')($scope.LIST.SERVICES,function(v,k,a){
			if( v.ID == SERVICE.SERVICEID ) return v;
		});

		if ( data != undefined ) {
			SERVICE.UNITPRICE = data[0].PRICE;	
		} else{
			SERVICE.UNITPRICE = 0;
		}

		SERVICE.AMOUNT =  parseFloat((SERVICE.UNITPRICE * SERVICE.QUANTITY).toFixed(2));

		$scope.Calculate_Services();
	}



	$scope.Add_Discounts = function(){

		$scope.f.DISCOUNTS.push({
			ID:0, 
			DISCOUNTID:'',
			PERCENTAGE : false,
			PERCENT: 0, 
			AMOUNT: 0,
			CANCELLED: false
		});
	}



	$scope.Remove_Discounts = function(k){

		if ($scope.f.DISCOUNTS[k].ID > 0) { $scope.f.DISCOUNTS[k].CANCELLED = !$scope.f.DISCOUNTS[k].CANCELLED; }
		else{ $scope.f.DISCOUNTS.splice(k,1); }

		$scope.Calculate_Discount();
	}


	$scope.Lookup_Discount = function(DISCOUNT){

		var data = $filter('filter')($scope.LIST.DISCOUNTS,function(v,k,a){
			if( v.ID == DISCOUNT.DISCOUNTID ) return v;
		});

		if ( data != undefined ) {

			DISCOUNT.PERCENTAGE = data[0].PERCENTAGE;
			DISCOUNT.PERCENT = data[0].AMOUNT;	
			DISCOUNT.AMOUNT = parseFloat(data[0].AMOUNT);

		} else{
			DISCOUNT.PERCENTAGE = false;
			DISCOUNT.PERCENT = 0;
			DISCOUNT.AMOUNT =0;
		}

		$scope.Calculate_Discount();
	}



	$scope.Calculate_Services = function(){

		var total = 0;

		if( $scope.f != null ){

			$scope.f.GROSSAMOUNT = 0;

			angular.forEach( $scope.f.SERVICES, function(val,key){ 
				if( !val.CANCELLED){

					val.AMOUNT = val.UNITPRICE * val.QUANTITY;
					total += val.AMOUNT;
				}
			});

			$scope.f.GROSSAMOUNT = total;
		}

		$scope.Billing_Changed();
		return total;
	}




	$scope.Calculate_Discount = function(){

		var deduction = 0;

		if( $scope.f != null ){

			var gross = $scope.f.GROSSAMOUNT;

			$scope.f.DISCOUNTAMOUNT = 0;

			if( gross > 0 ){

				angular.forEach($scope.f.DISCOUNTS,function(val,key){ 

					if(isNaN(val.AMOUNT)) val.AMOUNT = 0;

					if ( !val.CANCELLED ) {

						if( val.PERCENTAGE )  val.AMOUNT = parseFloat((gross * (val.PERCENT/100)).toFixed(2));

						deduction += parseFloat(val.AMOUNT); 
					}
				});
			}

			$scope.f.DISCOUNTAMOUNT = deduction;
		}

		$scope.Billing_Changed();

		return deduction;
	}


	$scope.HMO = function(){

		if( $scope.f != undefined ){

			if( $scope.f.PAYMODE == 'CASH' ){

				$scope.f.HMOAMOUNT = 0;
				$scope.f.HMORECEIVED = false;
				$scope.f.HMODATE = new Date();
				$scope.f.HMOID = '';
			}
		}

		$scope.Billing_Changed();
	}



	$scope.Billing_Changed = function(){

		var change = 0;

		if( $scope.f != undefined ){

			

			$scope.f.NETPAYABLES = $scope.f.GROSSAMOUNT - $scope.f.DISCOUNTAMOUNT;

			if( $scope.f.NETPAYABLES == 0 && $scope.f.AMOUNT > 0 )
				$scope.f.NETPAYABLES = $scope.f.AMOUNT;

			if( ($scope.f.AMOUNT + $scope.f.HMOAMOUNT )  >= $scope.f.NETPAYABLES ){

				change = $scope.f.AMOUNT - $scope.f.NETPAYABLES;
				change += $scope.f.HMOAMOUNT;
			}

			$scope.f.AMOUNTCHANGE = change;
			$scope.f.PAIDAMOUNT = $scope.f.AMOUNT - $scope.f.AMOUNTCHANGE;


		}

		return change;
	}





	$scope.Reset_Appointment = function(){

		if ( $scope.f.APPOINTMENT ){ 
			$scope.f.APPOINTMENTDATE = new Date();
		}
		else{
			$scope.f.APPOINTMENTDATE = null;
			$scope.f.APPOINTMENTDESCRIPTION = '';
			$scope.f.APPOINTMENTSUBCLINICID = null;
		}
	}


	$scope.File_Is_Image = function(ext){

		ext = ext == null ? '' : ext;

		ext = ext.toUpperCase(); 

		if( ext == '.JPEG' || ext == '.JPG'  || ext == '.PNG' || ext == '.BMP' || ext == '.TIFF'){
			return 'IMG';
		}
		else if( ext == '.XLS' || ext == '.XLSX'){
			return 'EXC';
		}
		else if( ext == '.PDF'){
			return 'PDF';
		}
		else{
			return 'OTH';
		}
	}

	$scope.File_View = function(ev,FILE) {
		$mdDialog.show({
			template: `
			<md-dialog aria-label="`+FILE.FILENAME+`" class="dialog-view-image">
			<md-toolbar>
			<div class="md-toolbar-tools">
			<h2 class="md-title">`+FILE.FILENAME+`</h2>
			<span flex></span>
			<md-button class="md-icon-button" ng-click="Dialog_Close()">
			<md-icon class="material-icons" aria-label="Close dialog">close</md-icon>
			</md-button>
			</div>
			</md-toolbar>
			<md-dialog-content>
			<iframe src="`+FILE.IMAGEPATH+`" scrolling="auto" ></iframe>
			</md-dialog-content>
			<md-dialog-actions layout="row"  style="display:none;">
			<span flex></span>
			<md-button ng-click="Dialog_Close()">
			Close
			</md-button>
			</md-dialog-actions>
			</md-dialog>
			`,
			controller: 'Extra',
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};


	$scope.Prescription_Report = function(ev) {
	
		$mdDialog.show({
			templateUrl: 'views/doctor/modal_prescription.html',
			locals:{
				PRESCRIPTIONURL: $scope.opt.prescriptionUrl
			},
			controller: function($scope, $mdDialog, PRESCRIPTIONURL, global){
				console.log(PRESCRIPTIONURL);
				$scope.URL = PRESCRIPTIONURL;

				$scope.Dialog_Close = function() {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};


	$scope.Medical_Certificate_Report = function(ev) {

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_medical_certificate.html',
			locals:{
				CERTIFICATEURL: $scope.opt.medicalCertificateUrl
			},
			controller: function($scope, $mdDialog, CERTIFICATEURL, global){

				$scope.URL = CERTIFICATEURL;

				$scope.Dialog_Close = function() {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};



	$scope.Referral_Report = function(ev) {

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_referral_letter.html',
			locals:{
				REFERRALURL: $scope.opt.referralLetterUrl
			},
			controller: function($scope, $mdDialog, REFERRALURL, global){

				$scope.URL = REFERRALURL;

				$scope.Dialog_Close = function() {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};


	$scope.Clearance_Report = function(ev) {

		$mdDialog.show({
			templateUrl: 'views/doctor/modal_clearance_letter.html',
			locals:{
				CLEARANCEURL: $scope.opt.clearanceLetterUrl
			},
			controller: function($scope, $mdDialog, CLEARANCEURL, global){

				$scope.URL = CLEARANCEURL;

				$scope.Dialog_Close = function() {
					$mdDialog.cancel();
				};
			},
			parent: angular.element(document.body),
			targetEvent: ev,
			fullscreen: true,
			clickOutsideToClose: true
		})
		.then(function(answer) {

		}, function() {

		});
	};




	$scope.Check_Select_Value = function(value){

		if( value != undefined ){
			if( isNaN(value) ){
				if( value == '' ){
					return null;
				}	
			} else {
				return parseInt(value);
			}
		} 

		return value;
	}


	$scope.New_LabTemplate = function(){

		$scope.formLab = {
			submitUrl : global.baseUrl +'settings/laboratory/submit-form',
			TOKEN : $scope.f.TOKENLAB,
			NAME: '',
			TEMPLATE: ''
		}

		$scope.toggleRight('labtemplate-sb');
	}


	$scope.toggleRight = function (sidepanel) {

		$mdSidenav(sidepanel)
		.toggle()
		.then(function () {
			$log.debug("toggle right is done");
		});
	}


	$scope.Submit_Laboratory = function(){

		if( $scope.opt.isSubmit ) return;

		$scope.opt.isSubmit = true;

		$http.post($scope.formLab.submitUrl, $scope.formLab, global.ajaxConfig).then( function(response){

			if( response.data == 'RELOGIN'){
				global.Relogin('FORM1');
			}
			else{

				if( response.data.err != '' ){
					global.Toast(response.data.err);
				}
				else {

					global.Toast('SAVED');
					$scope.toggleRight('labtemplate-sb');

					if( response.data.suc.NEWID != undefined ){
						$scope.formLab.ID = response.data.suc.NEWID;
						$scope.LIST.LABORATORY.push($scope.formLab);
						$scope.Add_Laboratory( $scope.LIST.LABORATORY[$scope.LIST.LABORATORY.length - 1]);
						global.Set_Laboratory($scope.formLab);
					}
				}
			}

			$scope.opt.isSubmit = false;
		},
		function(response){
			global.Relogin('FORM1');
			$scope.opt.isSubmit = false;
		});
	}


	$scope.Get_Lastest_Prescription = function(event){

		var confirm = $mdDialog.confirm()
		.title('Confirmation')
		.textContent('Do you want to add the previous prescription?')
		.ariaLabel('Lastest Prescription')
		.targetEvent(event)
		.ok('Yes')
		.cancel('No');

		$mdDialog.show(confirm).then(function() {

			$http.get($scope.opt.getLatestPrescriptionUrl+ $scope.f.PATIENT.ID +'/'+ $scope.f.ID , global.ajaxConfig).then( function(response){

				if ( response.data == 'RELOGIN' ){
					global.Relogin('PRESCRIPTION');
				}
				else {	

					if( response.data.length > 0 ){
						angular.forEach( response.data, function(v,k){
							$scope.f.MEDICINES.push({
								ID:0,
								MEDICINEID: v.MEDICINEID, 
								FREQUENCY: v.FREQUENCY, 
								QUANTITY:  parseInt(v.QUANTITY),
								INSTRUCTION: v.INSTRUCTION, 
								CANCELLED: false,
								search: ''
							});
						});
					}
					else{
						global.Alert('No prescription found.');
					}
				}
			},
			function(response){
				global.Relogin('PRESCRIPTION');
			});

			
		}, function() {
			
		});
	}


	$scope.Instruction_Items = function(search){

		var deferred = $q.defer();

		$timeout(function () { 
			
			var data = $filter('filter')( $scope.LIST.INSTRUCTIONS, {ACTIVE: true}); 

			data = $filter('filter')( data, search);
	
			deferred.resolve(data);

		}, Math.random() * 1000, false);

		return deferred.promise;

	}



	$rootScope.$emit("Load_Masterlist");

	$scope.LIST.SERVICES = global.SERVICES;
	$scope.LIST.DISCOUNTS = global.DISCOUNTS;
	$scope.LIST.MEDICINES = global.MEDICINES;
	$scope.LIST.LABORATORY = global.LABORATORY;
	$scope.LIST.INSTRUCTIONS = global.INSTRUCTIONS;
	$scope.LIST.SUBCLINICS = global.SUBCLINICS;
	$scope.LIST.HMO = global.HMO;


	$scope.Load_Form();


	var dList = $rootScope.$on('RELOGIN_LOAD', function (event, data) {
		$scope.Load_Form();
	});

	var dList1 = $rootScope.$on('RELOGIN_FORM', function (event, data) {
		$scope.Submit_Form();
	});

	var dList2 = $rootScope.$on('RELOGIN_FORM1', function (event, data) {
		$scope.Submit_Laboratory();
	});


	$scope.$on('$destroy', function() {
		dList();
		dList1();
		dList2();
	});


});




app.factory('MRFormServices',function ($http, $q, $filter, $timeout, $interval, $location, global) {

	var images = [];

	function Format(detail){

		detail.ID = parseInt(detail.ID);
		detail.PROCEDURESID = parseInt(detail.PROCEDURESID);

		// if( detail.CHECKUPDATE != null ){
		// 	if ( !angular.isDate(detail.CHECKUPDATE) )
		// 		detail.CHECKUPDATE =  global.Date(detail.CHECKUPDATE);
		// }
		detail.CHECKUPDATE= global.FormatDateTime(detail.CHECKUPDATE, null);

		if ( typeof detail.ANNUAL != 'boolean' )
			detail.ANNUAL = detail.ANNUAL == 'Y' ? true : false;

		if ( typeof detail.EXTERNAL_EYE != 'boolean' )
			detail.EXTERNAL_EYE = detail.EXTERNAL_EYE == 'Y' ? true : false;

		if ( typeof detail.GLAUCOMA != 'boolean' )
			detail.GLAUCOMA = detail.GLAUCOMA == 'Y' ? true : false;

		if ( typeof detail.HEADACHE != 'boolean' )
			detail.HEADACHE = detail.HEADACHE == 'Y' ? true : false;

		if ( typeof detail.PITY != 'boolean' )
			detail.PITY = detail.PITY == 'Y' ? true : false;

		if ( typeof detail.RETINA != 'boolean' )
			detail.RETINA = detail.RETINA == 'Y' ? true : false;

		if ( typeof detail.CATARACT != 'boolean' )
			detail.CATARACT = detail.CATARACT == 'Y' ? true : false;

		if ( typeof detail.FB != 'boolean' )
			detail.FB = detail.FB == 'Y' ? true : false;

		if ( typeof detail.LASER != 'boolean' )
			detail.LASER = detail.LASER == 'Y' ? true : false;

		if ( typeof detail.REFRACTION != 'boolean' )
			detail.REFRACTION = detail.REFRACTION == 'Y' ? true : false;

		if ( typeof detail.APPOINTMENT != 'boolean' )
			detail.APPOINTMENT = detail.APPOINTMENT == 'Y' ? true : false;

		// if( detail.APPOINTMENT ){

		// 	if( detail.APPOINTMENTDATE != null ){
		// 		if ( !angular.isDate(detail.APPOINTMENTDATE) )
		// 			detail.APPOINTMENTDATE =  global.Date(detail.APPOINTMENTDATE);
		// 	}
		// }
		detail.APPOINTMENTDATE= global.FormatDateTime(detail.APPOINTMENTDATE, null);

		if( detail.LMP != null ){
			if ( !angular.isDate(detail.LMP) )
				detail.LMP =  global.Date(detail.LMP);
		}

		detail.CASERATEID = detail.CASERATEID == null ? 0 : parseInt(detail.CASERATEID);


		if ( typeof detail.HMORECEIVED != 'boolean' )
			detail.HMORECEIVED = detail.HMORECEIVED == 'Y' ? true : false;


		if( detail.HMODATE != null ){
			if ( !angular.isDate(detail.HMODATE) )
				detail.HMODATE =  global.Date(detail.HMODATE);
		}


		if( detail.CHEQUEDATE != null ){
			if ( !angular.isDate(detail.CHEQUEDATE) )
				detail.CHEQUEDATE =  global.Date(detail.CHEQUEDATE);
		}


		if ( typeof detail.PHILHEALTH != 'boolean' )
			detail.PHILHEALTH = detail.PHILHEALTH == 'Y' ? true : false;

		if ( typeof detail.	PHILHEALTHRECEIVED != 'boolean' )
			detail.	PHILHEALTHRECEIVED = detail.	PHILHEALTHRECEIVED == 'Y' ? true : false;

		if( detail.PHILHEALTHRECEIVED ){
			if ( !angular.isDate(detail.PHILHEALTHCHEQUEDATE) )
				detail.PHILHEALTHCHEQUEDATE =  global.Date(detail.PHILHEALTHCHEQUEDATE);
		}


		if( detail.CONFINEMENT_DATE_FROM != null ){
			if ( !angular.isDate(detail.CONFINEMENT_DATE_FROM) )
				detail.CONFINEMENT_DATE_FROM =  global.Date(detail.CONFINEMENT_DATE_FROM);
		}

		if( detail.CONFINEMENT_DATE_TO != null ){
			if ( !angular.isDate(detail.CONFINEMENT_DATE_TO) )
				detail.CONFINEMENT_DATE_TO =  global.Date(detail.CONFINEMENT_DATE_TO);
		}

		detail.HEIGHT = parseFloat(detail.HEIGHT);
		detail.WEIGHT = parseFloat(detail.WEIGHT);
		detail.BMI = parseFloat(detail.BMI);

		detail.LATERAL_BORDER_L = parseInt(detail.LATERAL_BORDER_L);
		detail.LATERAL_BORDER_R = parseInt(detail.LATERAL_BORDER_R);

		detail.MEDICAL_CREASE_L = parseInt(detail.MEDICAL_CREASE_L);
		detail.MEDICAL_CREASE_R = parseInt(detail.MEDICAL_CREASE_R);
		
		detail.TALAR_HEAD_L = parseInt(detail.TALAR_HEAD_L);
		detail.TALAR_HEAD_R = parseInt(detail.TALAR_HEAD_R);
		
		detail.MIDFOOT_SCORE_L = parseInt(detail.MIDFOOT_SCORE_L);
		detail.MIDFOOT_SCORE_R = parseInt(detail.MIDFOOT_SCORE_R);
		
		detail.POSTERIOR_CREASE_L = parseInt(detail.POSTERIOR_CREASE_L);
		detail.POSTERIOR_CREASE_R = parseInt(detail.POSTERIOR_CREASE_R);
		
		detail.EMPTY_HEEL_L = parseInt(detail.EMPTY_HEEL_L);
		detail.EMPTY_HEEL_R = parseInt(detail.EMPTY_HEEL_R);
		
		detail.RIGID_EQUINUS_L = parseInt(detail.RIGID_EQUINUS_L);
		detail.RIGID_EQUINUS_R = parseInt(detail.RIGID_EQUINUS_R);
		
		detail.HINDFOOT_SCORE_L = parseInt(detail.HINDFOOT_SCORE_L);
		detail.HINDFOOT_SCORE_R = parseInt(detail.HINDFOOT_SCORE_R);

		detail.TOTAL_SCORE_L = parseInt(detail.TOTAL_SCORE_L);
		detail.TOTAL_SCORE_R = parseInt(detail.TOTAL_SCORE_R);
		
		detail.HMOAMOUNT = detail.HMOAMOUNT == null ? 0 : parseFloat(detail.HMOAMOUNT);
		detail.PHILHEALTHAMOUNT = detail.PHILHEALTHAMOUNT == null ? 0 : parseFloat(detail.PHILHEALTHAMOUNT);
		detail.CHEQUEAMOUNT = detail.CHEQUEAMOUNT == null ? 0 :  parseFloat(detail.CHEQUEAMOUNT);
		detail.GROSSAMOUNT = parseFloat(detail.GROSSAMOUNT);
		detail.DISCOUNTAMOUNT = parseFloat(detail.DISCOUNTAMOUNT);
		detail.AMOUNT = parseFloat(detail.AMOUNT);
		detail.AMOUNTCHANGE = parseFloat(detail.AMOUNTCHANGE);
		detail.NETPAYABLES = parseFloat(detail.NETPAYABLES);
		detail.PAIDAMOUNT = parseFloat(detail.PAIDAMOUNT);


		detail.LABORATORIES = Format_Laboratories(detail.LABORATORIES);
		detail.MRLABMONITORING = Format_MRLabMonitoring(detail.MRLABMONITORING);
		detail.PREV_MRLABMONITORING = Format_Prev_MRLabMonitoring(detail.PREV_MRLABMONITORING);
		detail.MEDICINES = Format_Medicines(detail.MEDICINES);
		detail.SERVICES = Format_Services(detail.SERVICES);
		detail.DISCOUNTS = Format_Discounts(detail.DISCOUNTS);

		if( detail.PREVIOUS ){ 
			detail.PREVIOUS.CHECKUPDATE =  global.Date(detail.PREVIOUS.CHECKUPDATE);
			detail.PREVIOUS.WEIGHT 		= detail.PREVIOUS.WEIGHT ? parseFloat(detail.PREVIOUS.WEIGHT) : null;
			detail.PREVIOUS.HEIGHT 		= detail.PREVIOUS.HEIGHT ? parseFloat(detail.PREVIOUS.HEIGHT) : null;							
			detail.PREVIOUS.BMI 		= detail.PREVIOUS.BMI ? parseFloat(detail.PREVIOUS.BMI) : 0;	
		}
		 
		if( detail.PATIENT.PICTURE == '' || detail.PATIENT.PICTURE == null)
			detail.PATIENT.PICTURE = global.baseUrl + 'assets/css/images/patient_default.png';

		if( detail.CREATEDBY == null ){
			detail.CREATEDBY  = 0;
		}
		else{
			detail.CREATEDBY = parseInt(detail.CREATEDBY);
		}

		detail.LASTMR = parseInt(detail.LASTMR);
		detail.READONLY = false;

		detail.isSubmit = false;

		return detail;
	}


	function Format_Laboratories(detail){

		angular.forEach( detail, function(v,k){ 
			v.ID = parseInt(v.ID);
			v.MEDICALRECORDID = parseInt(v.MEDICALRECORDID);
			v.LABMONITORINGID = parseInt(v.LABMONITORINGID);

			v.IMAGEPATH = global.baseUrl + v.IMAGEPATH;

			v.isPDF = Is_PDF_File(v.EXTENSION);
            v.isImage = Is_Image_File(v.EXTENSION);

			v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
		});
		
		return detail;
	}
	
	
	function Format_MRLabMonitoring(detail){
		
		angular.forEach( detail, function(v,k){ 
			v.ID = parseInt(v.ID);
			v.MEDICALRECORDID = parseInt(v.MEDICALRECORDID);
			v.LABMONITORINGID = parseInt(v.LABMONITORINGID);
			v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
		});
		
		return detail;
	}
	
	function Format_Prev_MRLabMonitoring(detail){
		
		angular.forEach( detail, function(v,k){ 
			v.ID = parseInt(v.ID);
			v.MEDICALRECORDID = parseInt(v.MEDICALRECORDID);
			v.LABMONITORINGID = parseInt(v.LABMONITORINGID);

			if ( !angular.isDate(v.CHECKUPDATE) )
				v.CHECKUPDATE =  global.Date(v.CHECKUPDATE);

			v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
		});

		return detail;
	}

	function Format_Medicines(detail){

		angular.forEach( detail, function(v,k){
			v.ID = parseInt(v.ID);
			v.MEDICINEID = parseInt(v.MEDICINEID);
			v.QUANTITY = parseFloat(v.QUANTITY);

			if ( typeof v.CANCELLED != 'boolean' )
				v.CANCELLED = v.CANCELLED == 'Y' ? true : false;

			// accidentally erase 
			v.searchMedsPrev = v.searchMeds;
		});

		return detail;
	}

	function Format_Services(detail){

		angular.forEach( detail, function(v,k){
			v.ID = parseInt(v.ID);
			v.SERVICEID = parseInt(v.SERVICEID);
			v.QUANTITY = parseInt(v.QUANTITY);
			v.UNITPRICE = parseFloat(v.UNITPRICE);
			v.AMOUNT = parseFloat(v.AMOUNT);

			if ( typeof v.CANCELLED != 'boolean' )
				v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
		});

		return detail;
	}


	function Format_Discounts(detail){

		angular.forEach( detail, function(v,k){
			v.ID = parseInt(v.ID);
			v.DISCOUNTID = parseInt(v.DISCOUNTID);

			if ( typeof v.PERCENTAGE != 'boolean' )
				v.PERCENTAGE = v.PERCENTAGE == 'Y' ? true : false;

			v.PERCENT = parseFloat(v.PERCENT);
			v.AMOUNT = parseFloat(v.AMOUNT);

			if ( typeof v.CANCELLED != 'boolean' )
				v.CANCELLED = v.CANCELLED == 'Y' ? true : false;
		});

		return detail;
	}

	function Is_PDF_File(EXTENSION){
		if( EXTENSION == '.pdf'){ return true; }
		else{ return false; }
	}

	function Is_Image_File(EXTENSION){
		if( EXTENSION == '.jpeg' || EXTENSION == '.jpg' || EXTENSION == '.png'){ return true; }
		else{ return false; }
	}
	
	return {
		Form: function(PATIENTID,ID){
 
			return $http.get( global.baseUrl + 'medicals/form-data/' + PATIENTID +'/'+ ID, global.ajaxConfig) .then( function(response) {

				// if deleted or other clinic
				if( response.data.error ){
					global.Alert(response.data.error);
					$location.url('/patient/'+ PATIENTID+ '/record');
					return false;
				}

				return Format(response.data);
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});
		},
		Format_Laboratories: function(detail){

			return Format_Laboratories(detail);
		},
		Format_MRLabMonitoring: function(detail){

			return Format_MRLabMonitoring(detail);
		},
		Format_Medicines: function(detail){

			return Format_Medicines(detail);
		},
		Format_Services: function(detail){

			return Format_Services(detail);
		},
		Format_Discounts: function(detail){

			return Format_Discounts(detail);
		},
		Form_Images: function(MRID){

			return $http.get( global.baseUrl + 'medicals/mr-images/' + MRID, global.ajaxConfig) .then( function(response) {
				images = [];

				angular.forEach( response.data, function(v,k){
					v.IMAGEPATH = global.baseUrl + v.IMAGEPATH;

					v.isPDF = false;
					v.isImage = false;

					if( v.EXTENSION == '.pdf'){ v.isPDF = true; }
					else if( v.EXTENSION == '.jpeg' || v.EXTENSION == '.jpg' || v.EXTENSION == '.png'){ v.isImage = true }

					images.push(v);
				});

				return true;
			}, 
			function(err){ 
				global.Alert( err.statusText, 'Error ' + err.status);
				return false;
			});
		},
		Images: function(){
			return images;
		},
		Append_Image: function(image){
			image.IMAGEPATH = global.baseUrl + image.IMAGEPATH; 

			image.isPDF = Is_PDF_File(image.EXTENSION);
			image.isImage = Is_Image_File(image.EXTENSION);

			images.push(image);
		},
		SMS_Form: function(MRID){

			return $http.post( global.baseUrl + 'sms/submit-sms-patient-appointment', {MRID: MRID}, global.ajaxConfig) .then( function(response) {

				return true;
			}, 
			function(err){ 
				global.Alert('SMS failed. Please save again');
				return false;
			});
		},
		Is_PDF_File(EXTENSION){
			return Is_PDF_File(EXTENSION);
		},
		Is_Image_File(EXTENSION){
			return Is_Image_File(EXTENSION);
		}
	}
	
});
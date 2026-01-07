'use strict';

var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize', 'ngRoute', 'thatisuday.dropzone']);


app.config(function($mdThemingProvider) {

	$mdThemingProvider.theme('default').dark()
	.primaryPalette('teal')
	.accentPalette('green')
	.warnPalette('amber');
 }); 


app.config(function ($routeProvider, $locationProvider) {

	$routeProvider

	.when("/dashboard", {
		templateUrl: "views/client/page_dashboard.html"
	})

	.when("/myclinic", {
		templateUrl: "views/client/page_clinic.html"
	})
	.when("/myclinic/edit", {
		templateUrl: "views/client/page_form_clinic.html"
	})


	.when("/patients", {
		templateUrl: "views/client/page_patients.html"
	})
	.when("/patient/:P1/record", {
      templateUrl: "views/client/page_patient_record.html"
   })
   .when("/patient/new", {
      templateUrl: "views/client/page_form_patient.html"
   })
   .when("/patient/:P1/edit", {
      templateUrl: "views/client/page_form_patient.html"
	})
	 
	.when("/patient/:PID/video-call", {
      templateUrl: "views/client/page_patient_vc.html"
   })

   .when("/patient/:P1/:P2/medical-record", {
      templateUrl: "views/client/page_form_medical_record.html"
   })

	.when("/medical-records", {
		templateUrl: "views/client/page_medical_records.html"
	})

	.when("/appointments", {
		templateUrl: "views/client/page_schedules.html"
	})

	.when("/sales", {
		templateUrl: "views/client/page_sales.html"
	})

	

	.when("/settings/users", {
		templateUrl: "views/client/page_users.html"
	})
	.when("/settings/services", {
		templateUrl: "views/client/page_services.html"
	})
	.when("/settings/discounts", {
		templateUrl: "views/client/page_discounts.html"
	})
	.when("/settings/hmo", {
		templateUrl: "views/client/page_hmo.html"
	})
	.when("/settings/medicines", {
		templateUrl: "views/client/page_medicines.html"
	})
	.when("/settings/instructions", {
		templateUrl: "views/client/page_instructions.html"
	})
	.when("/settings/lab-template", {
		templateUrl: "views/client/page_laboratory.html"
	})

	.when("/settings/procedures", {
		templateUrl: "views/client/page_procedures.html"
	})

	.when("/error404", {
		templateUrl: "views/content_error.html",
		controller: function(global){
			global.page.title = 'Error 404';
   		global.page.pageBackUrl = '';
		}
	})
	.otherwise("/dashboard");
 
});






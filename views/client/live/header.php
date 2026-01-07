<?php $version = '?v='.date('mdyHi',time()); ?>





<!DOCTYPE html>

<html lang="en" ng-app="myApplication">



<head>



	<title>MyClinic System</title>

   <meta charset="utf-8">

   

   <meta name="title" content="MyClinic System">

	<meta name="description" content="Clinic system for physicians that store electronic record and provide online appointment.">

	<meta name="keywords" content="clinic system, clinic, system, online, appointment, online appointment, electronic record">

   <meta name="copyright" content="Cerebro Diagnostic System" />

   <meta name="author" content="Cerebro Diagnostic System - AL" />

   

   <meta name="language" content="English">

   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

   <meta name="robots"  content="index, follow" />

   <meta name="revisit-after" content="1 days">



	<meta http-equiv="cache-control" content="no-cache"/>

   <meta http-equiv="Pragma" content="no-cache" />

	<meta http-equiv="expires" content="-1"/>

	

	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">

	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">

   



	<!-- nunito fonts -->

	<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet">



	<!-- openlayers mam -->

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/openlayers/ol.css'); ?>">

   <script src="<?php echo base_url('assets/openlayers/ol.js'); ?>"></script>

	<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>



	<!-- jitsi webrtc -->

	<script src='https://meet.jit.si/external_api.js'></script>



	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">



	

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">

	<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>"> -->

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/dropzone/min/basic.min.css'); ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/printjs/print.min.css'); ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/animate.css'); ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/croppie/croppie.css'); ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/pdfjs/pdf.css').$version; ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$version; ?>">



	

	<script src="<?php echo base_url('assets/jquery/jquery-3.2.1.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/mdl/material.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/chartjs/Chart.bundle.min.js') ?>"></script>

	<script src="<?php echo base_url('assets/dropzone/min/dropzone.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/pdfjs/pdf.js'); ?>"></script>

	<script src="<?php echo base_url('assets/printjs/print.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/is/is.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/exif/exif.js'); ?>"></script>

	<script src="<?php echo base_url('assets/croppie/croppie.js'); ?>"></script>

	<script src="<?php echo base_url('node_modules/signature_pad/dist/signature_pad.js'); ?>"></script>



	<script src="<?php echo base_url('assets/angularjs/angular.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular-animate.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular-aria.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular-messages.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular-sanitize.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular-route.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular-material.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjsdz/ng-dropzone.min.js'); ?>"></script>



	<script>

		var startUp = {

			baseUrl : '<?php echo base_url(); ?>'

		}

	</script>

</head>







<body ng-cloak>



	<div ng-controller="Navigation" ng-init="Init();" class="mdl-layout mdl-js-layout {{Page.drawer ? 'mdl-layout--fixed-drawer' : ''}} mdl-layout--fixed-header"  >



		<header md-colors="{background: 'teal-600'}" class="mdl-layout__header ">

			<div class="mdl-layout__header-row {{Page.pageBackUrl ? 'navi-back-show' : ''}}">

				

				<md-button ng-show="Page.pageBackUrl" ng-href="{{Page.pageBackUrl}}" class="navi-back">

					<md-icon class="material-icons">arrow_back_ios</md-icon>

				</md-button>



				<span ng-bind="Page.title" class="mdl-layout-title"></span>



				<div class="mdl-layout-spacer"></div>



				<nav class="mdl-navigation">

					<div class="mdl-navigation__link">



						<md-menu-bar>

							<md-menu md-position-mode="right bottom">

								<md-button ng-click="$mdMenu.open($event);" class="md-mini">

									<div layout="row">

										<md-icon class="material-icons">people</md-icon>

										<span ng-bind="Notify().APPOINTMENTS.length + Notify().SCHEDULES.length + Notify().NEWPATIENTS.length + Notify().REVISITS.length"></span>

									</div>										

								</md-button>

								<md-menu-content class="notify-top-content scroll">



									<div md-colors="{color:'grey-400'}" class="p-3">Today</div>



									<div ng-show="Notify().isLoadingPatients" class="loading-form">

										<div class="sk-folding-cube">

											<div class="sk-cube1 sk-cube"></div>

											<div class="sk-cube2 sk-cube"></div>

											<div class="sk-cube4 sk-cube"></div>

											<div class="sk-cube3 sk-cube"></div>

										</div>

									</div>



									<div class="top-right mt-2 mr-2">

										<md-button ng-click="Refresh_Today_Patients();" class="md-mini" md-prevent-menu-close="">

											<md-icon class="material-icons">refresh</md-icon>

										</md-button>

									</div>





									<md-subheader md-colors="{color:'primary-300'}">Follow up</md-subheader>



									<div ng-repeat="(key,A) in Notify().APPOINTMENTS | orderBy: ['LASTNAME','FIRSTNAME']" layout="row" layout-align="space-between center" class="notify-row-data">

										<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+A.MIDDLENAME"></span>



										<div layout="row" layout-align="end center" class="ml-3">

											<md-button ng-show="A.MRID" ng-click="Preview_MR(A.MRID);" class="md-mini md-primary">

												<md-icon class="material-icons">find_in_page</md-icon>

											</md-button>

											<md-button ng-href="#!/patient/{{A.ID}}/record" class="md-mini md-primary">

												<md-icon class="material-icons">folder_shared</md-icon>

											</md-button>

										</div>

									</div>



									

									<md-subheader md-colors="{color:'primary-300'}">Online Appointment</md-subheader>



									<div ng-repeat="(key,A) in Notify().SCHEDULES | orderBy: ['LASTNAME','FIRSTNAME']" layout="row" layout-align="space-between center" class="notify-row-data">

										<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+A.MIDDLENAME"></span>



										<div layout="row" layout-align="end center" class="ml-3">

											<md-button ng-show="A.MRID" ng-click="Preview_MR(A.MRID);" class="md-mini md-primary">

												<md-icon class="material-icons">find_in_page</md-icon>

											</md-button>

											<md-button ng-href="#!/patient/{{A.ID}}/record" class="md-mini md-primary">

												<md-icon class="material-icons">folder_shared</md-icon>

											</md-button>

										</div>

									</div>



									<md-subheader md-colors="{color:'primary-300'}">New Registered</md-subheader>



									<div ng-repeat="(key,A) in Notify().NEWPATIENTS | orderBy: ['LASTNAME','FIRSTNAME']" layout="row" layout-align="space-between center" class="notify-row-data">

										<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+A.MIDDLENAME"></span>



										<div layout="row" layout-align="end center" class="ml-3">

											<md-button ng-show="A.MRID" ng-click="Preview_MR(A.MRID);" class="md-mini md-primary">

												<md-icon class="material-icons">find_in_page</md-icon>

											</md-button>

											<md-button ng-href="#!/patient/{{A.ID}}/record" class="md-mini md-primary">

												<md-icon class="material-icons">folder_shared</md-icon>

											</md-button>

										</div>

									</div>



									<md-subheader md-colors="{color:'primary-300'}">Revisit</md-subheader>



									<div ng-repeat="(key,A) in Notify().REVISITS | orderBy: ['LASTNAME','FIRSTNAME']" layout="row" layout-align="space-between center" class="notify-row-data">

										<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+A.MIDDLENAME"></span>



										<div layout="row" layout-align="end center" class="ml-3">

											<md-button ng-show="A.MRID" ng-click="Preview_MR(A.MRID);" class="md-mini md-primary">

												<md-icon class="material-icons">find_in_page</md-icon>

											</md-button>

											<md-button ng-href="#!/patient/{{A.ID}}/record" class="md-mini md-primary">

												<md-icon class="material-icons">folder_shared</md-icon>

											</md-button>

										</div>

									</div>

								</md-menu-content>

							</md-menu>

						</md-menu-bar>





					</div>



					<div class="mdl-navigation__link" style="position:relative;">

						<div class="user-panel-row">

							<button ng-click="toggleRight('user-sb');" type="button" class="user-btn">

								<img ng-src="{{Me().AVATAR}}" class="user-img">

								<div class="user-name">

									<span ng-bind="Me().NAME"></span>	

									<br>

									<small ng-bind="Me().JOBTITLE"></small>

								</div>

							</button> 

						</div>

						<div ng-show="(! Me().SPECIALISTID && Me().isDoctor)" class="top-right">

							<span md-colors="{background: 'warn-300'}" class="icon-notify">

								<md-icon md-colors="{color: 'grey-800'}" class="material-icons">priority_high</md-icon>

							</span>

						</div>

					</div>

				</nav>



			</div>

		</header>





		<!-- user panel -->

		<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="user-sb">

			<md-content class="user-panel-sb md-padding">



				<div layout="column" layout-align="center center" style="position:relative;">



					<div class="user-img-wrapper">

						<img ng-src="{{Me().AVATAR}}" class="user-img">

						<div class="bottom-right">

							<md-button ng-click="Change_Profile_Picture();" class="md-primary md-fab img-btn">

								<md-tooltip md-direction="top">change Profile picture</md-tooltip>

								<md-icon class="material-icons sm">edit</md-icon>

							</md-button>

						</div>

					</div>



					<p ng-bind="Me().NAME" class="m-0"></p>

					<span ng-bind="Me().JOBTITLE" class="text-muted"></span>

					<small ng-bind="Me().POSITION" class="text-muted" ></small>

				</div> 

 

				<div class="user-menus" layout="row" layout-wrap="" layout-align="space-between start">



					<div style="position:relative;">

						<md-button ng-click="My_Account();" class="md-primary">

							<div layout="column" layout-align="center center" class="text-center h-100">

								<span>My Account</span>

							</div>

						</md-button>

						<div ng-show="(! Me().SPECIALISTID && Me().isDoctor)" class="top-right">

							<span md-colors="{background: 'warn-300'}" class="icon-notify">

								<md-icon md-colors="{color: 'grey-800'}" class="material-icons">priority_high</md-icon>

							</span>

						</div>

					</div>

										

					<md-button ng-click="Change_Username();" class="md-primary">

						<div layout="column" layout-align="center center" class="text-center h-100">

							<span>Change Username</span>

						</div>

					</md-button>



					<md-button ng-click="Change_Password();" class="md-primary">

						<div layout="column" layout-align="center center" class="text-center h-100">

							<span>Change Password</span>

						</div>

					</md-button>





					<div ng-show="Me().isDoctor" style="position:relative;">

						<md-button ng-click="Online_Profile();" class="md-primary">

							<div layout="column" layout-align="center center" class="text-center h-100">

								<span>Online Profile</span>

							</div>

						</md-button>

					</div>





					<md-button ng-click="View_Changelog()" class="md-primary">

						<div layout="column" layout-align="center center" class="text-center h-100">

							<span>Advisory & Change log</span>

						</div>

					</md-button>



					<md-button ng-click="Page.drawer = !Page.drawer;Toggle_Drawer();" class="md-primary">

						<div layout="column" layout-align="center center" class="text-center h-100">

							<span>Menu</span>		

							<span ng-show="Page.drawer">ON</span>

							<span ng-hide="Page.drawer">OFF</span>

						</div>

					</md-button>



					<md-button href="{{Page.signoutUrl}}"class="md-warn">

						<div layout="column" layout-align="center center" class="text-center h-100">

							<span>Sign Out</span>

						</div>

					</md-button>



				</div>



			</md-content>

		</md-sidenav>





		<!-- changelog panel -->

		<md-sidenav class="md-sidenav-right md-whiteframe-4dp md-sidebar-md" md-component-id="changelog-sb">

			<md-toolbar>

				<div class="md-toolbar-tools">

					<h1 class="md-toolbar-tools">Advisory & Changelog</h1>

					<span flex></span>

					<md-button hide-gt-xs="" class="md-icon-button" ng-click="toggleRight('changelog-sb')">

						<md-icon class="material-icons" aria-label="Close dialog">close</md-icon>

					</md-button>

				</div>

			</md-toolbar>

			<div ng-show="Changelog().isLoading" class="loading-form">

				<div class="sk-folding-cube">

					<div class="sk-cube1 sk-cube"></div>

					<div class="sk-cube2 sk-cube"></div>

					<div class="sk-cube4 sk-cube"></div>

					<div class="sk-cube3 sk-cube"></div>

				</div>

			</div>

			<md-content class="md-padding">



				<div ng-repeat="(key,A) in Changelog().datas | orderBy: ['-POSTDATE']" class="advisory-message"> 

					<h4 ng-bind="A.TITLE" md-colors="{color:'primary-300'}" class="mb-0 {{key > 0 ? 'mt-5' : 'mt-0' }}"></h4>

					<p ng-bind="A.POSTDATE | date: 'MMM d, y'" md-colors="{color:'grey-300'}" class="small"></p>

					<pre ng-bind-html="A.BODY"></pre>

				</div>



			</md-content>

		</md-sidenav>





		<div class="mdl-layout__drawer signed" >

			<div class="menus-wrapper scroll"> 

				<div class="drawer_logo">

					<img src="<?php echo base_url('assets/css/images/logo_green.png'); ?>">

				</div>

				<nav class="mdl-navigation">

					<a href="#!/dashboard" class="mdl-navigation__link" >Dashboard</a>

					<a href="#!/myclinic" ng-show="Me().isAdmin" class="mdl-navigation__link" >My Clinic</a>

					<a href="#!/patients" class="mdl-navigation__link" >Patients</a>

					<a href="#!/medical-records" class="mdl-navigation__link" >Medical Records</a>

					<a href="#!/appointments" class="mdl-navigation__link" >Appointments</a>

					<!-- <a href="#!/sms" class="mdl-navigation__link" ng-show="Me().isAdmin" >SMS</a> -->

					<a href="#!/sales" class="mdl-navigation__link" ng-show="Me().SALES">Sales</a>

					<div class="group-title" >Settings</div> 

					<div class="subgroup" >

						<a href="#!/settings/users" ng-show="Me().isAdmin" class="mdl-navigation__link" >Users</a>

						<a href="#!/settings/services" ng-show="Me().isAdmin && Me().SALES" class="mdl-navigation__link" >Services</a>

						<a href="#!/settings/discounts" ng-show="Me().isAdmin && Me().SALES" class="mdl-navigation__link" >Discounts</a>

						<a href="#!/settings/hmo" ng-show="Me().SALES" class="mdl-navigation__link" >HMO</a>

						<a href="#!/settings/medicines" class="mdl-navigation__link" >Medicines</a>

						<a href="#!/settings/instructions"  class="mdl-navigation__link" >Instructions</a>

						<a href="#!/settings/lab-template" class="mdl-navigation__link" >Lab Template</a>

					</div>

				</nav>

			</div>

		</div>





		<div ng-hide="Me().ID > 0" class="loading-form">

			<div class="sk-folding-cube">

				<div class="sk-cube1 sk-cube"></div>

				<div class="sk-cube2 sk-cube"></div>

				<div class="sk-cube4 sk-cube"></div>

				<div class="sk-cube3 sk-cube"></div>

			</div>

		</div>





		<main class="mdl-layout__content scroll">

			<div ng-view></div>

		</main>



	</div>





	<!-- global -->

	<script src="<?php echo base_url('views/client/_js/app.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/global.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/pdf.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/p2p.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/map.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/socketIO.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/me.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/notify.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/changelog.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/navigation.js').$version; ?>"></script>

	 



	<!-- client --> 

	



	<script src="<?php echo base_url('views/client/_services/icd.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/rvs.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_services/specialist.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/hospitals.services.js').$version; ?>"></script>

	

	<script src="<?php echo base_url('views/client/_js/dashboard.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/dashboard.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/clinic.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_js/clinicForm.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/clinic.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/subClinic.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/patients.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/patients.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/patientVC.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/patientForm.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/patientForm.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/patientRecord.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/patientRecord.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/mrForm.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/mrForm.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/medicalRecords.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/medicalRecords.services.js').$version; ?>"></script>





	<script src="<?php echo base_url('views/client/_js/schedules.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/schedules.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/sales.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/sales.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/users.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/users.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/services.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/discounts.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/discounts.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/hmo.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/hmo.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/medicines.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/medicines.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/instructions.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/instructions.services.js').$version; ?>"></script>



	<script src="<?php echo base_url('views/client/_js/laboratory.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/client/_services/laboratory.services.js').$version; ?>"></script>



	</body>

</html>


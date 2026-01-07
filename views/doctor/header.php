<?php $_SESSION['ver'] = '?'.date('mdyH',time()); ?>
<!-- doctor -->
<!DOCTYPE html>
<html lang="en" ng-app="myApplication">

<head>

	<title>CLINIC SYSTEM</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name='viewport' content='width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no' />
	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">
	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/bootstrap-grid.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/dropzone/min/basic.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$_SESSION['ver']; ?>">


	<script src="<?php echo base_url('assets/jquery/jquery-2.1.1.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/chartjs/Chart.bundle.min.js') ?>"></script>
	<script src="<?php echo base_url('assets/mdl/material.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/dropzone/min/dropzone.min.js'); ?>"></script>

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
			baseUrl : '<?php echo base_url(); ?>',
			NAME : '<?php echo $this->session->name; ?>',
			LEVEL : '<?php echo $this->session->position; ?>',
			JOBTITLE : '<?php echo $this->session->jobtitle; ?>',
			LICENSENO : '<?php echo $this->session->LICENSENO; ?>',
			PTR : '<?php echo $this->session->PTR; ?>',
			SPECIALISTID : '<?php echo $this->session->SPECIALISTID; ?>',
			SUBCLINICID : '<?php echo $this->session->SUBCLINICID; ?>',
			SALES : '<?php echo $this->session->SALES; ?>',
			BLAST : '<?php echo $this->session->BLAST; ?>'
		}
	</script>
</head>



<body ng-cloak>

	<div ng-controller="Navigation" class="mdl-layout mdl-js-layout {{opt.drawer ? 'mdl-layout--fixed-drawer' : ''}} mdl-layout--fixed-header"  >

		<header md-colors="{background: 'primary-500'}" class="mdl-layout__header ">
			<div class="mdl-layout__header-row {{search.pageBackUrl ? 'navi-back-show' : ''}}">
				
				<md-button ng-show="search.pageBackUrl" href="{{search.pageBackUrl}}" class="md-fab md-mini md-primary navi-back no-shadow">
					<md-icon class="material-icons">arrow_back_ios</md-icon>
				</md-button>

				<span ng-bind="opt.title" class="mdl-layout-title"></span>

				<div class="mdl-layout-spacer"></div>

				<div ng-if="search.view" hide="" show-gt-sm="" class="top-search">
					<form ng-submit="Search_Global()" role="form">
						<input ng-model="search.text" ng-if="search.viewText" placeholder="{{search.placeholder}}">
						<input ng-model="search.dateFrom" type="date" ng-if="search.viewDateFrom" placeholder="Date From" ng-required="search.viewDateFrom">
						<input ng-model="search.dateTo" type="date" ng-if="search.viewDateTo" placeholder="Date To" ng-required="search.viewDateTo">
						<md-button type="submit" ng-disabled="search.isSearching" class="md-icon-button md-primary">
							<md-icon ng-hide="search.isSearching" class="material-icons">search</md-icon> 
							<md-progress-circular ng-show="search.isSearching" md-mode="indeterminate" md-diameter="23"></md-progress-circular>
						</md-button>
					</form>
				</div>

				<md-button ng-show="search.view" ng-click="toggleRight('search-sb')" class="md-fab md-mini md-primary no-shadow" hide-gt-sm="" >
					<md-icon ng-hide="search.isSearching" ng-class="material-icons">search</md-icon>
					<md-progress-circular ng-show="search.isSearching" md-mode="indeterminate" md-diameter="23" class="md-accent"></md-progress-circular>
				</md-button>

				<md-button ng-click="toggleRight('advisory-sb');Noti_Clear();" class="md-fab md-mini md-primary no-shadow">
					<md-icon class="material-icons {{opt.advisoryNotiVisible ? 'mdl-badge mdl-badge--overlap' : '' }} " data-badge="!">settings</md-icon>
				</md-button>
			</div>
		</header>
		
		
		<!-- search panel -->
		<md-sidenav class="md-sidenav-right md-whiteframe-2dp" md-component-id="search-sb">
			<md-toolbar>
				<h1 class="md-toolbar-tools">Search</h1>
			</md-toolbar>
			<md-content >
				<form  ng-submit="Search_Global()" role="form" class="p-3"  >

					<md-input-container ng-show="search.viewText" class="md-block">
						<label>{{search.placeholder}}</label>
						<input ng-model="search.text">
					</md-input-container>

					<div layout="row">
						<md-input-container ng-show="search.viewDateFrom" flex="">
							<label>Date From</label>
							<input ng-model="search.dateFrom" type="date" ng-required="search.viewDateFrom">
						</md-input-container>

						<md-input-container ng-show="search.viewDateTo" flex="">
							<label>Date To</label>
							<input ng-model="search.dateTo" type="date" ng-required="search.viewDateTo">
						</md-input-container>
					</div>

					<div layout="column" >
						<md-button  ng-click="toggleRight('search-sb');" type="submit" class="md-raised md-primary">
							<md-icon class="material-icons">search</md-icon>
						</md-button>
					</div>
				</form>
			</md-content>
		</md-sidenav>


		<!-- advisory panel -->
		<md-sidenav class="md-sidenav-right md-whiteframe-2dp md-sidebar-md" md-component-id="advisory-sb">
			<md-toolbar layout="row" layout-align="center center">
				<h1 class="md-toolbar-tools" flex="">Advisory & Updates</h1>
				<md-button  ng-click="toggleRight('advisory-sb');" class="md-icon-button">
					<md-icon class="material-icons">close</md-icon>
				</md-button>
			</md-toolbar>
			<md-content>
				
				<div class="px-3">
					<md-switch ng-model="opt.drawer" ng-click="Toggle_Drawer();" aria-label="Drawer" class="md-primary">Fixed Menu Panel</md-switch>
				</div>

				<md-divider></md-divider>

				<div ng-repeat="A in Advisory | orderBy: ['-POSTDATE']" class="advisory-message">
					<h5 ng-bind="A.TITLE" class="mt-0"></h5>	
					<pre ng-bind-html="A.BODY" class="text-muteds"></pre>
					<div class="text-right">
						<small ng-bind="A.POSTDATE|date:'MM/dd/y hh:mm a'" class="text-muted"></small>	
					</div> 
				</div>

				<div ng-show="Advisory.length == 0" class="p-3 text-muted">
					<h6>No advisory or updates for now.</h6>
				</div>

			</md-content>
		</md-sidenav>


		<div class="mdl-layout__drawer scroll" >

			<div  class="user-panel">
				<div layout="column" layout-align="center center">
					<img src="{{opt.USER.AVATAR}}">
					<span ng-bind="opt.USER.NAME"></span>
					<small ng-bind="opt.USER.JOBTITLE"></small>
				</div>
				<md-menu md-position-mode="target-right target">
					<md-button class="md-icon-button md-primary" ng-click="$mdMenu.open($event)" >
						<md-icon class="material-icons {{opt.userWarning ? 'mdl-badge mdl-badge--overlap' : '' }} " data-badge="!">more_vert</md-icon>
					</md-button>
					<md-menu-content>
						<md-menu-item >
							<md-button href="#!/account" >Account</md-button>
						</md-menu-item>
					</md-menu-content>
				</md-menu>
			</div>
			<nav class="mdl-navigation">
				<a href="#!/dashboard" class="mdl-navigation__link" >Dashboard</a>
				<a href="#!/clinic" class="mdl-navigation__link" >Clinic</a>
				<a href="#!/patients" class="mdl-navigation__link" >Patients</a>
				<a href="#!/medical-records" class="mdl-navigation__link" >Medical Records</a>
				<a href="#!/appointments" class="mdl-navigation__link" >Appointments</a>
				<a href="#!/sales" class="mdl-navigation__link" ng-show="opt.USER.SALES">Sales</a>
				<!-- <a href="#!/sms" class="mdl-navigation__link" ng-show="opt.USER.LEVEL == 'BRANCH ADMINISTRATOR'" >SMS</a> -->
				<div class="group-title" >Settings</div>
				<div class="subgroup" >
					<a href="#!/settings/users" ng-show="opt.USER.LEVEL == 'BRANCH ADMINISTRATOR'" class="mdl-navigation__link" >Users</a>
					<a href="#!/settings/services" ng-show="opt.USER.SALES" class="mdl-navigation__link" >Services</a>
					<a href="#!/settings/discounts" ng-show="opt.USER.SALES" class="mdl-navigation__link" >Discounts</a>
					<a href="#!/settings/hmo" ng-show="opt.USER.SALES" class="mdl-navigation__link" >HMO</a>
					<a href="#!/settings/prescriptions" class="mdl-navigation__link" >Prescriptions</a>
					<a href="#!/settings/instructions"  class="mdl-navigation__link" >Instructions</a>
					<a href="#!/settings/labtemplate" class="mdl-navigation__link" >Lab Template</a>
				</div>
				<a href="{{opt.signoutUrl}}" class="mdl-navigation__link" >Sign Out</a>
			</nav>
		</div>
		<main class="mdl-layout__content scroll">

			<div ng-view ng-class="opt.class"></div>

		</main>
	</div>


	<script src="<?php echo base_url('views/doctor/js/app.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/global.js').$_SESSION['ver']; ?>"></script>


	<script src="<?php echo base_url('views/doctor/js/pageDashboard.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageClinic.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formClinic.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formSubClinic.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pagePatients.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formPatient.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pagePatient.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formMedical.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageMedical.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageAppointment.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageSales.js').$_SESSION['ver']; ?>"></script>

	<script src="<?php echo base_url('views/doctor/js/pageSMS.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formSMS.js').$_SESSION['ver']; ?>"></script>

 
	<script src="<?php echo base_url('views/doctor/js/pageUsers.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formUser.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageServices.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formService.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageDiscounts.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formDiscount.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageHmo.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formHmo.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pagePrescriptions.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formPrescription.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageInstructions.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formInstruction.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/pageLaboratory.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/doctor/js/formLaboratory.js').$_SESSION['ver']; ?>"></script>



	<!-- for all user page -->
	<script src="<?php echo base_url('views/pageAccount.js').$_SESSION['ver']; ?>"></script>
	<script src="<?php echo base_url('views/pageRelogin.js').$_SESSION['ver']; ?>"></script>

</body>
</html>

<?php $version = '?v='.date('mdyHis',time()); ?>


<!DOCTYPE html>
<html lang="en" ng-app="myApplication">

<head>

	<title>MyClinic System</title>
   <meta charset="utf-8">
   
   <meta name="title" content="MyClinic System">
	<meta name="description" content="Clinic system for physicians that store electronic record and provide online appointment.">
	<meta name="keywords" content="clinic system, clinic, system, online, appointment, online appointment, electronic record">
   <meta name="copyright" content="Cerebro Diagnostic System" />
   <meta name="author" content="Lorenzo Al Lopez" />
   
   <meta name="language" content="English">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta name="robots"  content="index, follow" />
   <meta name="revisit-after" content="1 days">

	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="86400"/>
	
	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">
   <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet">

	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
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

<header md-colors="{background: 'primary-500'}" class="mdl-layout__header ">
	<div class="mdl-layout__header-row {{Page.pageBackUrl ? 'navi-back-show' : ''}}">
		
		<md-button ng-show="Page.pageBackUrl" ng-href="{{Page.pageBackUrl}}" class="md-fab md-mini md-primary navi-back">
			<md-icon class="material-icons">arrow_back_ios</md-icon>
		</md-button>

		<span ng-bind="Page.title" class="mdl-layout-title"></span>

				<div class="mdl-layout-spacer"></div>

				<nav class="mdl-navigation">
					<div class="mdl-navigation__link">
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
						<div ng-show="(! Me().SPECIALISTID && Me().isDoctor) || (! Me().LINK && Me().isDoctor)" class="top-right">
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


	

		<div class="mdl-layout__drawer signed" >
			<div class="menus-wrapper scroll">
				<div class="drawer_logo">
					<img src="<?php echo base_url('assets/css/images/logo.png'); ?>">
				</div>
				<nav class="mdl-navigation">
					<a href="#!/dashboard" class="mdl-navigation__link" >Dashboard</a>
					<a href="#!/clinics" class="mdl-navigation__link" >Clinics</a>
					<a href="#!/advisory" class="mdl-navigation__link" >Advisory</a>
					<a href="#!/users"  class="mdl-navigation__link" >Users</a>
				</nav>
			</div>
		</div>

		<main class="mdl-layout__content scroll">
			<div ng-view class="slidexx fadexxx"></div>
		</main>

		<div ng-hide="Me().ID > 0" class="loading-form">
			<div class="sk-folding-cube">
				<div class="sk-cube1 sk-cube"></div>
				<div class="sk-cube2 sk-cube"></div>
				<div class="sk-cube4 sk-cube"></div>
				<div class="sk-cube3 sk-cube"></div>
			</div>
		</div>

	</div>

	<!-- global -->
	<script src="<?php echo base_url('views/admin/_js/app.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/global.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/pdf.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/webSocket.js').$version; ?>"></script>	
	<script src="<?php echo base_url('views/p2p.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/map.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/me.services.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/notify.services.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/changelog.services.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/navigation.js').$version; ?>"></script>

	<!-- admin --> 
	<script src="<?php echo base_url('views/admin/_js/dashboard.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/admin/_services/dashboard.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/admin/_services/specialist.services.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/admin/_services/hospitals.services.js').$version; ?>"></script>
	
	<script src="<?php echo base_url('views/admin/_js/clinics.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/admin/_services/clinics.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/admin/_js/advisory.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/admin/_services/advisory.services.js').$version; ?>"></script>

	<script src="<?php echo base_url('views/admin/_js/users.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/admin/_services/users.services.js').$version; ?>"></script>

	<!-- client -->
	<script src="<?php echo base_url('views/client/_services/subClinic.services.js').$version; ?>"></script>

	</body>
</html>

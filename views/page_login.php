<?php $version= '?v='.date('mdyH',time()); ?>

<!DOCTYPE html>
<html lang="en" ng-app="myApplication">

<head>

	<title>MyClinic System - sign in</title> 
   <meta charset="utf-8">
   
   <meta name="title" content="MyClinic System - sign in">
	<meta name="description" content="Clinic system for physicians that store electronic record and provide online appointment.">
	<meta name="keywords" content="clinic system, clinic, system, online, appointment, online appointment, electronic record">
   <meta name="copyright" content="Cerebro Diagnostic System" />
   <meta name="author" content="Lorenzo Al Lopez" />
   
   <meta name="language" content="English">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta name="robots"  content="index, follow" />
   <meta name="revisit-after" content="1 days">

	<meta http-equiv="cache-control" content="no-cache"/>
   <meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1"/>
	
	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">
   <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$version; ?>">
	
	
	<script src="<?php echo base_url('assets/jquery/jquery-2.1.1.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/mdl/material.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/angularjs/angular.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-animate.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-aria.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-messages.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-sanitize.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-material.min.js'); ?>"></script>


	<script src="<?php echo base_url('views/login.js').$version; ?>"></script>
	<script src="<?php echo base_url('views/global.js').$version; ?>"></script> 

</head>

<body ng-cloak>
	<section ng-controller="Login" ng-init="Init('<?php echo base_url(); ?>','<?php echo $TOKEN; ?>')" layout="row" layout-align="center center" class="login-wrapper">
		

	
		<md-card id="signin"  flex class="card-login" >
			<md-card-header>
				<md-card-avatar>
					<img src="<?php echo base_url('assets/css/images/logo.png'); ?>"/>
				</md-card-avatar>
				<md-card-header-text>
					<span class="md-title">MyClinic System</span>
					<span class="md-subhead">You are in safe hand</span>
				</md-card-header-text>
			</md-card-header>
			<md-card-content>
				
				<form name="Form" ng-submit="Submit_Form()" class="">

					<md-input-container class="md-block">
						<label>Username</label>
						<input ng-model="form.USERNAME" type="text" required>
					</md-input-container>

					<md-input-container class="md-block">
						<label>Password</label>
						<input ng-model="form.PASSWORD" type="password" required>
					</md-input-container>
					
					<div layout="column">
						<md-button ng-disabled="isSubmit" type="submit" class="md-raised md-primary">
							<span ng-hide="isSubmit">Sign In</span>
							<md-progress-circular ng-show="isSubmit" md-mode="indeterminate" md-diameter="30" class="mx-auto"></md-progress-circular>
						</md-button>	
					</div>
				</form>

				<div layout="column" class="text-center mt-4">

					<md-button ng-href="{{homeUrl}}" type="submit" class="my-3 md-primary">
						<span>Home</span>
					</md-button>	

					<small class="text-muted">Please always update your browser for better user experience.</small>
				</div>

			</md-card-content>
		</md-card>
		
		
		

	</section>
</body>
</html>

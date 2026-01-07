<?php $version= '?v='.date('mdyHi',time()); ?>

<!DOCTYPE html>
<html lang="en" ng-app="myApplication">
<head>
   <title>MyClinic System - 404</title>
	<meta name="AUTHOR" content="Cerebro Diagnostic System">
	<meta name="DESCRIPTION" content="MyClinic - Cerebro Diagnostic System">
	<META NAME="KEYWORDS" CONTENT="MY CLINIC">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name='viewport' content='width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no' />
	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">
   <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet">
   

   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/animate.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$version; ?>">


   <script src="<?php echo base_url('assets/jquery/jquery-3.2.1.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/mdl/material.min.js'); ?>"></script>
	
	<script src="<?php echo base_url('assets/angularjs/angular.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-animate.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-aria.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-messages.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-sanitize.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-route.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-material.min.js'); ?>"></script>
   
   <script>
      var baseUrl = '<?php echo base_url(); ?>';
      var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize']); 

      app.config(function($mdThemingProvider) {
         $mdThemingProvider.theme('default').dark()
         .primaryPalette('teal')
         .accentPalette('green')
         .warnPalette('amber');
      }); 
   </script>

   
</head>

<body class="landing" ng-clock>
   
   <section class="section-error" >
      <article layout="column" layout-align="center center">
         <h1>404</h1>
         <h4 class="mt-0">Invalid Page !</h4>
         <p class="text-center">You trying to access the invalid url or the page is already removed.</p>
         <p class="text-center">If you think this is a server error, please contact the <a href="mailto:admin@cerebrodiagnostics.com">Cerebro Diagnostic System</a>.</p>
         <md-button href="<?php echo base_url(); ?>" class="md-primary">
            Back to home
         </md-button>
      </article>
   </section>
</body>
</html>


 
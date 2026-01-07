<?php $version= '?v='.date('mdyHi',time()); ?>

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
   <link href="https://fonts.googleapis.com/css?family=Nunito:300,300i,400,400i,600,600i,700,700i&display=swap" rel="stylesheet"> 
   
  
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>"> 
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/animate.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$version; ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/landing.css').$version; ?>">


   <script src="<?php echo base_url('assets/jquery/jquery-3.2.1.min.js'); ?>"></script>
   <script src="<?php echo base_url('assets/mdl/material.min.js'); ?>"></script>

   <script src='https://meet.jit.si/external_api.js'></script>
   	
	<script src="<?php echo base_url('assets/angularjs/angular.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-animate.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-aria.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-messages.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-sanitize.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-material.min.js'); ?>"></script>
   
   <script>
      var baseUrl = '<?php echo base_url(); ?>';
      var DOCTORJS = JSON.parse('<?php echo json_encode($data); ?>');

      var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize']); 
   
   </script>

   <script src="<?php echo base_url('views/global.js').$version; ?>"></script>   
   <script src="<?php echo base_url('views/jitsi.js').$version; ?>"></script>
  
      
</head>

<body ng-controller="Jitsi" ng-init="Init();" class="jitsi">

   <div id="conf_div"></div>

   <section ng-hide="FORM.isStart" layout="row" layout-align="center center">
      <md-card>
         <md-card-content>

            <div class="doctor-info">
               <img ng-src="{{DOCTOR.AVATAR}}" alt="">
               <p ng-bind="DOCTOR.NAME" class="mb-0"></p>
               <span ng-bind="DOCTOR.SPECIALTY"></span>
            </div>

            <form ng-submit="Submit_Check_Patient()">

               <h6 class="text-center">Patient Info</h6>

               <md-input-container class="md-block">
                  <input ng-model="FORM.LASTNAME" name="LASTNAME" placeholder="Lastname" required>
                  <div ng-messages="formParent.LASTNAME.$error" role="alert" md-auto-hide="false">
                     <div ng-message="required">This is required.</div>
                  </div>
               </md-input-container>

               <md-input-container class="md-block">
                  <input ng-model="FORM.DOB" name="DOB" type="date" placeholder="Date of Birth" required>
                  <div ng-messages="formParent.DOB.$error" role="alert" md-auto-hide="false">
                     <div ng-message="required">This is required.</div>
                  </div>
               </md-input-container>

               <div layout="column" class="mt-3">
                  <md-button ng-disabled="FORM.isSubmit || formParent.$invalid"  type="submit" class="md-raised md-primary">
                     <span ng-hide="FORM.isSubmit">Enter</span>
                     <md-progress-circular ng-show="FORM.isSubmit" md-mode="indeterminate" md-diameter="20" class="mx-auto"></md-progress-circular>
                  </md-button>

                  <md-button ng-href="{{backUrl}}"  type="submit" class="md-primary mt-3">
                     <span>Back</span>
                  </md-button>
               </div>

            </form>
         </md-card-content>
      </md-card>
   </section>
    
</body>
</html>


 
<?php $version= '?v='.date('mdyH',time()); ?>

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
   <meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1"/>
	
	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
	<link rel="icon" href="<?php echo base_url('assets/css/images/logo.png'); ?>" type="image/gif" sizes="16x16">
   <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet">
   


   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/animate.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$version; ?>">
   

   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/openlayers/ol.css'); ?>">
   <script src="<?php echo base_url('assets/openlayers/ol.js'); ?>"></script>
   <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
   
   
   <script src="<?php echo base_url('assets/jquery/jquery-3.2.1.min.js'); ?>"></script>
   <script src="<?php echo base_url('assets/mdl/material.min.js'); ?>"></script>
   
	<script src="<?php echo base_url('assets/angularjs/angular.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-animate.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-aria.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-messages.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-sanitize.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/angularjs/angular-material.min.js'); ?>"></script>

   <script>
      var baseUrl = '<?php echo base_url(); ?>';
      var app = angular.module('myApplication', ['ngMaterial', 'ngMessages', 'ngSanitize']); 
   </script>

   <script src="<?php echo base_url('views/global.js').$version; ?>"></script>
   <script src="<?php echo base_url('views/map.js').$version; ?>"></script>

   
   <script src="<?php echo base_url('views/landing.js').$version; ?>"></script>
   <script src="<?php echo base_url('views/landing.services.js').$version; ?>"></script>
   
</head>

<body ng-controller="Landing" class="landing"  ng-init="Init('<?php echo $TOKEN; ?>');" ng-clock>

   <div class="mdl-layout mdl-js-layout landing-navigation mdl-layout--fixed-header">
      <header class="mdl-layout__header mdl-layout__header--transparent" md-colors="{background:'grey-900'}">
         <div class="mdl-layout__header-row">
            <!-- Title -->
            <span hide="" show-gt-xs="" class="mdl-layout-title">Cerebro Diagnostic System</span>
            <span hide-gt-xs="" class="mdl-layout-title">Cerebro</span>

            <!-- Add spacer, to align navigation to the right -->
            <div class="mdl-layout-spacer"></div>

            <!-- Navigation show-gt-sm="" -->
            <div layout="row" layout-align="end center" hide="" >
               <md-button ng-click="Scrolling('highlightsSection');" class="md-primary">We</md-button>
               <md-button ng-click="Scrolling('searchSection');" class="md-primary">Doctors</md-button>
               <md-button ng-click="Scrolling('aboutSection');" class="md-primary">About us</md-button>
               <md-button ng-click="Scrolling('registerSection');" class="md-primary">Register</md-button>
            </div>

            <md-button href="<?php echo base_url('signin'); ?>" class="md-primary">Sign In</md-button>
         </div>
      </header>

      <div class="mdl-layout__drawer" >
         <div class="landing_logo">
            <img src="<?php echo base_url('assets/css/images/cerebro_banner.png'); ?>">
         </div>
         <nav class="mdl-navigation" hide="">
            <a ng-click="Scrolling('highlightsSection');" href="#" class="mdl-navigation__link">We</a>
            <a ng-click="Scrolling('searchSection');" href="#" class="mdl-navigation__link">Doctors</a>
            <a ng-click="Scrolling('aboutSection');" href="#" class="mdl-navigation__link">About us</a>
            <a ng-click="Scrolling('registerSection');" href="#" class="mdl-navigation__link">Register</a>   
         </nav>
      </div>
      <main class="mdl-layout__content">

         <!-- highlights -->
         <section id="highlightsSection" class="highlights-section" hide="" style="display:none;">
            <article class="container-lg" layout="row" layout-align="space-between center">

               <div class="highlight-info">
                  <h1 class="animated fadeInLeft">We keep</h1>
                  <h2 class="animated fadeInLeft">your record</h2>
                  <h3 class="animated fadeInLeft">for your safety</h3>
               </div>
               <div class="highlight-img">
                  <div class="highlight-img-wrapper">
                     <img src="<?php echo base_url('assets/css/images/background/desktop.png'); ?>" class="img1 animated fadeInRight" alt="Cerebro Diagnostic System Dashboard">
                     <img src="<?php echo base_url('assets/css/images/background/mobile.png'); ?>" class="img2 animated fadeInUp" alt="Cerebro Diagnostic System Dashboard">
                  </div>
               </div>
            </article>
         </section>

         
         <!-- search doctor section -->
         <section id="searchSection" class="search-section" >

            <div>
               <div id="mapPopup"></div>
               <div id="map"></div>
            </div>
            
            <article layout="column" layout-align="space-between start" class="container-lg">
               

               <div class="search-panel">
                  <div class="search-doctor">
                     <form ng-submit="Search_Doctor(true);" layout="row">
                        <input id="search" ng-model="search.NAME"  ng-disabled="Data().isSearching"  type="text" placeholder="Search doctor">   
                        <button type="submit">
                           <md-icon ng-hide="Data().isSearching" class="material-icons md-primary">search</md-icon>
                           <md-progress-circular ng-show="Data().isSearching" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                        </button>
                        <button ng-click="Back_Home();" type="button">
                           <md-icon class="material-icons" md-colors="{color:'red-500'}">my_location</md-icon>
                        </button>
                     </form>
                  </div>
                  <div class="search-results">
                     <div ng-repeat="(key,A) in Data().DOCTORS" class="doctor-info" layout="row">
                        <div>
                           <img ng-src="{{A.AVATAR}}" >
                        </div>
                        <div flex>
                           <p ng-bind="A.NAME" class="doctor-name"></p>
                           <span ng-bind="A.SPECIALTY" md-colors="{color:'grey-700'}"></span>
                        </div>
                        <div ng-show="A.REMAINING > 0" layout="column" layout-align="center center">
                           <h6 ng-bind="A.REMAINING" md-colors="{color: 'primary-500'}" class="m-0"></h6>
                           <small>available</small>
                        </div>
                        <div class="options" layout="row" layout-align="center stretch">
                           <md-button ng-click="Doctor_Location_Focus(A)"  class="md-primary md-raised">
                              <md-icon class="material-icons">person_pin_circle</md-icon>   
                           </md-button>
                           <md-button ng-click="View_Profile(A)" class="md-primary md-raised">
                              <md-icon class="material-icons">forward</md-icon>   
                           </md-button>
                        </div>
                     </div>
                  </div>
               </div>

            </article>
         </section>

         <section id="aboutSection" hide="">
            <article>
                  about
            </article>
         </section>

         <section id="registerSection" hide="">
            <article>
               register
            </article>
         </section>
    
      </main>
   </div>

  

   

   
</body>
</html>


 
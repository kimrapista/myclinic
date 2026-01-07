<?php 
$version= '?v='.date('mdyH',time()); 

   if( isset($data->NAME) ){
      if( is_null($data->AVATAR) || empty($data->AVATAR) ){
         $data->AVATAR = base_url('assets/css/images/logo.png');
      }
      else {
         $data->AVATAR = base_url($data->AVATAR);
      }
   }
   else{
      $data = (object)array(
         'NAME' => 'MyClinic System',
         'SPECIALTY' => '',
         'LINK' => '',
         'MOTTO' => '',
         'AVATAR' => base_url('assets/css/images/logo.png'),
      );
   }
?>

<!DOCTYPE html>
<html lang="en" ng-app="myApplication"> 
<head>

   <title><?php echo $data->NAME.' | '.$data->SPECIALTY;  ?></title>
   <meta charset="utf-8">
   <meta name="google-site-verification" content="5pp81Bu46Lp-M0yvmYAl9p_LwKJEb_hS6T82_7sc00o" />
   <meta name="title" content="<?php echo $data->NAME.' | '.$data->SPECIALTY; ?>">
	<meta name="description" content="<?php echo $data->MOTTO; ?>">
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
	<link rel="icon" href="<?php echo $data->AVATAR ?>" type="image/gif" sizes="16x16">
   <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet">
   
  
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/angularjs/angular-material.min.css'); ?>"> 
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/mdl/material.min.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/material-icons/mi_style.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/animate.css'); ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css').$version; ?>">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/landing.css').$version; ?>">


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
   
   <script src="<?php echo base_url('views/onlineAppoint.js').$version; ?>"></script>
   <script src="<?php echo base_url('views/onlineAppoint.services.js').$version; ?>"></script>
  
      
</head>

<body ng-controller="OnlineAppoint" ng-init="Init('<?php echo $TOKEN; ?>');" class="landing">

   <div ng-hide="isLoaded"  class="loading-white">
		<div class="sk-folding-cube">
			<div class="sk-cube1 sk-cube"></div>
			<div class="sk-cube2 sk-cube"></div>
			<div class="sk-cube4 sk-cube"></div>
			<div class="sk-cube3 sk-cube"></div>
		</div>
   </div>
  
   <section class="profile-section" style="background-image:url({{Data().DOCTOR.BACKGROUNDIMG}})" >
      <article layout="column" layout-gt-xs="row" layout-align-gt-xs="space-around start">
         
         <md-card class="profile animated fadeInUp" flex="shrink">
            <md-card-content >

               <div class="top-left">
                  <md-button href="<?php echo base_url(); ?>" class="md-primary md-mini">
                     <md-icon class="material-icons">arrow_back_ios</md-icon>
                  </md-button>
               </div>

               <div class="top-right">
                  <md-button ng-href="{{vcUrl}}" class="md-primary md-mini">
                     <md-tooltip md-direction="top">Video Call</md-tooltip>
                     <md-icon class="material-icons">videocam</md-icon>
                  </md-button>
               </div>

               <div class="profile-header">
                  <img ng-src="{{Data().DOCTOR.AVATAR}}" alt="{{Data().DOCTOR.NAME}}">
                  <div class="profile-detail" layout="column">
                     <p ng-bind="Data().DOCTOR.NAME" class="name"></p>
                     <span ng-bind="Data().DOCTOR.SPECIALTY" md-colors="{color:'grey-700'}"></span>
                  </div>
               </div>

               <pre ng-bind="Data().DOCTOR.MOTTO" class="m-0 text-center"></pre>
            </md-card-content>

            <md-tabs md-dynamic-heightx md-border-bottom md-center-tabs>
               <md-tab label="Clinic Info"> 
                  <md-card-content>
                     <label>Clinic Name</label>
                     <p ng-bind="Data().DOCTOR.CLINIC.CLINICNAME" class="mb-0"></p>
                     <p ng-bind="Data().DOCTOR.CLINIC.CLINICSUBNAME" class="mb-0"></p>
                     <p ng-bind="Data().DOCTOR.CLINIC.CLINICSUBNAME1" ></p>

                     <label>Tel #</label>
                     <p ng-bind="Data().DOCTOR.CLINIC.CONTACTNO"></p>

                     <label>Mobile #</label>
                     <p ng-bind="Data().DOCTOR.CLINIC.MOBILENO"></p>

                     <label>Email</label>
                     <p ng-bind="Data().DOCTOR.CLINIC.EMAIL"></p>

                  </md-card-content>
               </md-tab>
               <md-tab label="LOCATIONS"> 
                  <md-card-content class="p-0">
                     <div  class="table-unresponsive">
                        <table>
                           <tr ng-repeat="(key,A) in Data().DOCTOR.SUBCLINIC">
                              <td><md-icon class="material-icons md-primary">location_on</md-icon></td>
                              <td><span ng-bind="A.NAME+', '+A.LOCATION"></span></td>
                           </tr>
                        </table>
                     </div>
                  </md-card-content>
               </md-tab>
               <md-tab label="Members"> 
                  <md-card-content>
                        <table class="w-100">
                           <tr ng-repeat="(key,A) in Data().DOCTOR.MEMBERS" class="profile-member">
                              <td>
                                 <img ng-src="{{A.AVATAR}}" alt="">
                              </td>
                              <td>
                                 <div ng-bind="A.NAME"></div>
                                 <div ng-bind="A.SPECIALTY" class="small"></div>
                                 <div ng-bind="A.JOBTITLE" class="mb-3 small"></div>
                              </td>
                              <td>
                                 <md-button ng-if="A.LINK" class="md-primary md-mini m-0">
                                    <md-icon class="material-icons">forward</md-icon>
                                 </md-button>
                              </td>
                           </tr>
                        </table>
                  </md-card-content>
               </md-tab>
            </md-tabs>


            <!-- extra options -->
            <div layout="row" layout-align="space-between center" >
               <md-button ng-click="Resend_Code();" class="md-primary">Resend Code</md-button>
               <md-button ng-click="Verify_Code();" class="md-primary">Verify Code</md-button>
            </div>
         </md-card>

         

         <div flex="grow">
         
            <md-card ng-repeat="(key, A) in Data().SCHEDULES | orderBy: ['date']" class="animated fadeInUp" style="animation-delay: {{key*50}}ms;">
               <md-card-content>
                  <h5 ng-bind="A.date | date: 'EEEE MMMM d, y'" class="m-0"></h5>
               </md-card-content>
               <div class="table-unresponsive">
                  <table>
                     <thead>
                        <tr>
                           <th>Time</th>
                           <th>Location</th>
                           <th>Status</th>
                           <th></th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr ng-repeat="(key1, B) in A.data | orderBy: ['SDATETIME']">
                           <td ng-bind="B.SDATETIME | date: 'hh:mm a'" class="text-nowrap"></td>
                           <td ng-bind="B.HOSPITALNAME+', '+ B.LOCATION"></td>
                           <td class="text-center">
                              <div ng-show="B.MAXPATIENT > B.TOTAL_ACKNOWLEDGED">
                                 <div layout="row" layout-align="space-between center">
                                    <span class="pr-2">Remain: </span>
                                    <span ng-bind="(B.MAXPATIENT - B.TOTAL_ACKNOWLEDGED)"></span>
                                 </div>
                                 <div layout="row" layout-align="space-between center">
                                    <span class="pr-2" >Queuing:</span>
                                    <span ng-bind="B.TOTAL_UNVERIFIED"></span>
                                 </div>
                              </div>
                              <span ng-show="B.MAXPATIENT <= B.TOTAL_ACKNOWLEDGED" md-colors="{color:'red-500'}">FULL</span>
                           </td>
                           <td class="action">
                              <md-button ng-click="Pre_Appoint(B)" ng-disabled="B.MAXPATIENT <= B.TOTAL_ACKNOWLEDGED" class="md-primary md-mini">
                                 <md-icon class="material-icons">add_circle_outline</md-icon>
                              </md-button>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </md-card>

            <div ng-if="Data().SCHEDULES.length == 0">
               <md-card  class="animated fadeInUp">
                  <md-card-content class="text-center ">
                     <h6>No available appointment</h6>
                  </md-card-content>
               </md-card>
            </div>
            
         </div>

         
      </article>
   </section>

   
</body>
</html>


 
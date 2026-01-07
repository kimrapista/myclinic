<?php

defined('BASEPATH') OR exit('No direct script access allowed');



/*

| -------------------------------------------------------------------------

| URI ROUTING

| -------------------------------------------------------------------------

| This file lets you re-map URI requests to specific controller functions.

|

| Typically there is a one-to-one relationship between a URL string

| and its corresponding controller class/method. The segments in a

| URL normally follow this pattern:

|

|	example.com/class/method/id/

|

| In some instances, however, you may want to remap this relationship

| so that a different class/function is called than the one

| corresponding to the URL.

|

| Please see the user guide for complete details:

|

|	https://codeigniter.com/user_guide/general/routing.html

|

| -------------------------------------------------------------------------

| RESERVED ROUTES

| -------------------------------------------------------------------------

|

| There are three reserved routes:

|

|	$route['default_controller'] = 'welcome';

|

| This route indicates which controller class should be loaded if the

| URI contains no data. In the above example, the "welcome" class

| would be loaded.

|

|	$route['404_override'] = 'errors/page_missing';

|

| This route will tell the Router which controller/method to use if those

| provided in the URL cannot be matched to a valid route.

|

|	$route['translate_uri_dashes'] = FALSE;

|

| This is not exactly a route, but allows you to automatically route

| controller and method names that contain dashes. '-' isn't a valid

| class or method name character, so it requires translation.

| When you set this option to TRUE, it will replace ALL dashes in the

| controller and method URI segments.

|

| Examples:	my-controller/index	-> my_controller/index

|		my-controller/my-method	-> my_controller/my_method

*/

$route['default_controller'] = 'c_main';

$route['404_override'] = '';


//$route['translate_uri_dashes'] = FALSE;



$route['advisory/(:any)/(:any)'] = 'advisory/index/$1/$2';

$route['advisory/(:any)'] = 'advisory/index/$1';

$route['advisory'] = 'advisory/index';

 





$route['landing/(:any)/(:any)/(:any)'] = 'landing/index/$1/$2/$3';

$route['landing/(:any)/(:any)'] = 'landing/index/$1/$2';

$route['landing/(:any)'] = 'landing/index/$1';

$route['landing'] = 'landing/index';



$route['sms/(:any)/(:any)'] = 'sms/index/$1/$2'; 

$route['sms/(:any)'] = 'sms/index/$1';

$route['sms'] = 'sms/index';





$route['schedules/(:any)/(:any)/(:any)'] = 'schedules/index/$1/$2/$3';

$route['schedules/(:any)/(:any)'] = 'schedules/index/$1/$2';

$route['schedules/(:any)'] = 'schedules/index/$1';

$route['schedules'] = 'schedules/index';


$route['doh/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'doh/index/$1/$2/$3/$4/$5';

$route['doh/(:any)/(:any)/(:any)/(:any)'] = 'doh/index/$1/$2/$3/$4';

$route['doh/(:any)/(:any)/(:any)'] = 'doh/index/$1/$2/$3';

$route['doh/(:any)/(:any)'] = 'doh/index/$1/$2';

$route['doh/(:any)'] = 'doh/index/$1';

$route['doh'] = 'doh/index'; 





$route['dashboard/(:any)/(:any)'] = 'dashboard/index/$1/$2';

$route['dashboard/(:any)'] = 'dashboard/index/$1';

$route['dashboard'] = 'dashboard/index';





$route['notify/(:any)/(:any)/(:any)'] = 'notify/index/$1/$2/$3';

$route['notify/(:any)/(:any)'] = 'notify/index/$1/$2';

$route['notify/(:any)'] = 'notify/index/$1';

$route['notify'] = 'notify/index';





$route['hospitals/(:any)/(:any)'] = 'hospitals/index/$1/$2';

$route['hospitals/(:any)'] = 'hospitals/index/$1';

$route['hospitals'] = 'hospitals/index';





$route['clinics/(:any)/(:any)/(:any)'] = 'clinics/index/$1/$2/$3';

$route['clinics/(:any)/(:any)'] = 'clinics/index/$1/$2';

$route['clinics/(:any)'] = 'clinics/index/$1';

$route['clinics'] = 'clinics/index';





$route['account/(:any)/(:any)/(:any)'] = 'account/index/$1/$2/$3';

$route['account/(:any)/(:any)'] = 'account/index/$1/$2';

$route['account/(:any)'] = 'account/index/$1';

$route['account'] = 'account/index';





$route['patients/(:any)/(:any)/(:any)'] = 'patients/index/$1/$2/$3';

$route['patients/(:any)/(:any)'] = 'patients/index/$1/$2';

$route['patients/(:any)'] = 'patients/index/$1';

$route['patients'] = 'patients/index';



$route['medicals/(:any)/(:any)/(:any)/(:any)'] = 'medicals/index/$1/$2/$3/$4';

$route['medicals/(:any)/(:any)/(:any)'] = 'medicals/index/$1/$2/$3';

$route['medicals/(:any)/(:any)'] = 'medicals/index/$1/$2';

$route['medicals/(:any)'] 	= 'medicals/index/$1';

$route['medicals'] = 'medicals/index';





$route['sales/(:any)/(:any)/(:any)'] = 'sales/index/$1/$2/$3';

$route['sales/(:any)/(:any)'] = 'sales/index/$1/$2';

$route['sales/(:any)'] = 'sales/index/$1';

$route['sales'] = 'sales/index';



$route['appointments/(:any)/(:any)'] = 'appointments/index/$1/$2';

$route['appointments/(:any)'] = 'appointments/index/$1';

$route['appointments'] 	= 'appointments/index';





$route['settings/(:any)/(:any)/(:any)'] = 'settings/index/$1/$2/$3';

$route['settings/(:any)/(:any)'] = 'settings/index/$1/$2';

$route['settings/(:any)'] 	= 'settings/index/$1';

$route['settings'] = 'settings/index';



$route['migrate/(:any)/(:any)/(:any)'] = 'migrate/index/$1/$2/$3';
$route['migrate/(:any)/(:any)'] = 'migrate/index/$1/$2';
$route['migrate/(:any)'] 	= 'migrate/index/$1';
$route['migrate'] = 'migrate/index';



$route['(.*)/(.*)/(.*)/(.*)/(.*)'] 	= 'c_main/index/$1/$2/$3/$4/$5';

$route['(.*)/(.*)/(.*)/(.*)'] = 'c_main/index/$1/$2/$3/$4';

$route['(.*)/(.*)/(.*)'] = 'c_main/index/$1/$2/$3';

$route['(.*)/(.*)'] 	= 'c_main/index/$1/$2';

$route['(.*)'] = 'c_main/index/$1';

 
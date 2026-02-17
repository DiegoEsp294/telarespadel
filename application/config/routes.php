<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The left-hand side
| represents the URI requested, and the right-hand side represents
| the path to the controller.
|
| Please refer to the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'Home';
|	- This route indicates which controller class should be
|	  invoked when there is no URI segments – e.g. just your domain
|
|	$route['404_override'] = 'my404';
|	- This route will tell the Router which controller/method to use if those
|	  provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|	- This is not exactly a route, but allows you to automatically route
|	  dashes in URLs to underscores in your controller function names.
|	  When you set this to TRUE, it will replace ALL dashes in the URI
|	  with underscores, e.g: blog-comments -> blog_comments
|
| -------------------------------------------------------------------------
| EXAMPLE ROUTES
| -------------------------------------------------------------------------
|
|	$route['blogs'] = 'blog';
|	$route['blog/joe'] = 'blog/users/Joe';
|	$route['blog/(:any)'] = 'blog/view/$1';
|	$route['image/(:num)'] = 'pic/index/$1';
|	$route['map/(:hash)'] = 'map/index/$1';
|	etc.
|
| -------------------------------------------------------------------------
| RESERVED CHARS IN ROUTING
| -------------------------------------------------------------------------
|
| Characters that are permitted within the brackets:
| -._~%!$&'()*+,;=:@
|
*/
$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

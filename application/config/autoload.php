<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| This file specifies which systems should be loaded by default.
|
| In order to keep the framework as light-weight as possible only the
| absolute minimal resources are loaded by default. But you can
| specify which modules/libraries you'd like loaded.
|
| -------------------------------------------------------------------
| Auto-load Packges
| -------------------------------------------------------------------
| Prototype:
|
|  $autoload['packages'] = array(APPPATH.'third_party', '/usr/local/shared');
|
*/
$autoload['packages'] = array();

/*
| -------------------------------------------------------------------
| Auto-load Libraries
| -------------------------------------------------------------------
| These are the classes located in system/libraries/ or your
| application/libraries/ directory.
|
| Prototype:
|
|  $autoload['libraries'] = array('email', 'session');
|
| You can also supply an alternative library name to be assigned
| in the controller:
|
|  $autoload['libraries'] = array('email' => 'mailer');
|
*/
$autoload['libraries'] = array('database', 'session');

/*
| -------------------------------------------------------------------
| Auto-load Drivers
| -------------------------------------------------------------------
| These classes are located in system/libraries/ or in your
| application/libraries/ directory, but are also placed inside in
| subdirectories when the driver can not be found in the base
| libraries/ directory name.
|
| Prototype:
|
|  $autoload['drivers'] = array('cache');
|
*/
$autoload['drivers'] = array();

/*
| -------------------------------------------------------------------
| Auto-load Helper Files
| -------------------------------------------------------------------
| Prototype:
|
|  $autoload['helper'] = array('url', 'file');
|
*/
$autoload['helper'] = array('url', 'form', 'html');

/*
| -------------------------------------------------------------------
| Auto-load Config files
| -------------------------------------------------------------------
| Prototype:
|
|  $autoload['config'] = array('config');
|
*/
$autoload['config'] = array();

/*
| -------------------------------------------------------------------
| Auto-load Language files
| -------------------------------------------------------------------
| Prototype:
|
|  $autoload['language'] = array('lang1', 'lang2');
|
*/
$autoload['language'] = array();

/*
| -------------------------------------------------------------------
| Auto-load Models
| -------------------------------------------------------------------
| Prototype:
|
|  $autoload['model'] = array('first_model', 'second_model');
|
*/
$autoload['model'] = array();

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete details visit:
|	https://codeigniter.com/userguide3/database/configuration.html
|
| -------------------------------------------------------------------
| ACTIVE GROUP
| -------------------------------------------------------------------
|
| $active_group = 'default';
| This is the name of the 'database connection group' that you intend
| to use for this site.
|
*/
$active_group = 'default';

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete details visit:
|	https://codeigniter.com/userguide3/database/configuration.html
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database driver. eg: mysql.
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the Query Builder class.
|	['pconnect'] TRUE/FALSE - many databases store the connection open
|				 until the end of the script execution. If your database
|				 stays open too long, some firewalls might disconnect you if you
|				 exceed a this connection limit. 'persistent' connections
|				 only apply to databases that support them
|	['db_debug'] TRUE/FALSE - If database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files are stored
|	['char_set'] the character set used in communicating with the database
|	['dbcollat'] the character collation used in communicating with the database
|	               (only used in some databases - MySQL, PostgreSQL)
|	['swap_pre'] a default table prefix that should be swapped with the dbprefix
|	['encrypt']  whether or not to use encrypted connections.
|	['compress'] whether or not to use client compression (MySQL only)
|	['ssl_key']    used when encrypting the connection to validate the server cert.
|	['ssl_cert']   used when encrypting the connection to validate the server cert.
|	['ssl_ca']     used when encrypting the connection to validate the server cert.
|	['ssl_capath'] used when encrypting the connection to validate the server cert.
|	['ssl_cipher'] used when encrypting the connection to validate the server cert.
|	['ssl_verify'] used when encrypting the connection to validate the server cert.
|	['stricton']   forcing 'Strict' SQL, this option causes DB to require an
|	               index or primary key on all tables before allowing an insert
|	               or update. It is recommended for production applications.
|
*/
$db['default'] = array(
    'dsn'      => '',
    'hostname' => getenv('DB_HOST'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'database' => getenv('DB_NAME'),
    'dbdriver' => 'postgre',
    'port'     => 5432,
    'pconnect' => FALSE,
    'db_debug' => TRUE,
    'char_set' => 'utf8',
);


// Alternate database for development/testing
// Uncomment to use
/*
$db['test'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'torneos_telares_test',
	'dbdriver' => 'mysqli',
	'dbprefix' => 'test_',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
*/

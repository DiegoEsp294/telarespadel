<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * the circumstances. Using the environment, for example, you
 * can have your development and production servers configured
 * differently. Setup the environment with "development", "testing"
 * or "production"
 *
 */
	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(-1);
			ini_set('display_errors', 1);
		break;

		case 'testing':
		case 'production':
			ini_set('display_errors', 0);
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
		break;

		default:
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'The application environment is not set correctly.';
			exit(1); // EXIT_ERROR
	}
}

/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" directory.
 * Set the path if it is not in the same directory as this file.
 *
 */
	$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * directory than the default one you can set its name here. Typically,
 * you will have basename($system_path) == basename($application_path).
 *
 */
	$application_path = 'application';

/*
 *---------------------------------------------------------------
 * VIEW DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view directory out of the application
 * directory, set the path to it here. The leave the trailing slash off.
 *
 */

// If the system_path has not been set, set it now. This allows for a front
// controller to be placed anywhere within the system folder or built upon,
// provided it shares a common /index.php file with the rest of the structure.
if (is_dir($system_path) === FALSE)
{
	if (is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.$system_path) === TRUE)
	{
		$system_path = dirname(__FILE__).DIRECTORY_SEPARATOR.$system_path;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo '<h2>⚠️ Error: Carpeta "system" no encontrada</h2>';
		echo '<p>Debes descargar CodeIgniter 3 desde: <a href="https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip" target="_blank">https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip</a></p>';
		echo '<p>Luego copia la carpeta <strong>system</strong> a esta ruta:</p>';
		echo '<pre>'.dirname(__FILE__).'\\system\\</pre>';
		exit(3); // EXIT_CONFIG
	}
}

// Path to the "application" folder
if (is_dir($application_path) === FALSE)
{
	if (@is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.$application_path) === TRUE)
	{
		$application_path = dirname(__FILE__).DIRECTORY_SEPARATOR.$application_path;
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo '<h2>⚠️ Error: Carpeta "application" no encontrada</h2>';
		echo '<p>Verifica que la carpeta <strong>application</strong> exista en: '.dirname(__FILE__).'</p>';
		exit(3); // EXIT_CONFIG
	}
}

define('FCPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('SYSDIR', basename($system_path) . DIRECTORY_SEPARATOR);
define('APPPATH', $application_path . DIRECTORY_SEPARATOR);

// Allow for a trailing slash in the config paths
if (substr($system_path, -1) === DIRECTORY_SEPARATOR)
{
	$system_path = substr($system_path, 0, -1);
}

if (substr($application_path, -1) === DIRECTORY_SEPARATOR)
{
	$application_path = substr($application_path, 0, -1);
}

// Ensure there's a trailing slash (added in the future)
$system_path .= DIRECTORY_SEPARATOR;
$application_path .= DIRECTORY_SEPARATOR;

// Is the system path correct?
if ( ! is_file($system_path.'core/CodeIgniter.php'))
{
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	echo '<h2>⚠️ Error: CodeIgniter no está instalado</h2>';
	echo '<p>Debes descargar CodeIgniter 3 desde: <a href="https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip" target="_blank">https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip</a></p>';
	echo '<p>Después de descargar, extrae el archivo y copia la carpeta <strong>system</strong> a:</p>';
	echo '<pre>'.$system_path.'</pre>';
	echo '<p>Una vez copiada, recarga esta página.</p>';
	exit(3); // EXIT_CONFIG
}

/*
 *---------------------------------------------------------------
 * DEFINE APPLICATION CONSTANTS
 *---------------------------------------------------------------
 *
 * CodeIgniter and the application it powers have to know where the
 * Absolute path to the Symfony and Composer provided autoloader are,
 * as well as the path to the "system" folder after this point.
 * Else, the constants get defined.
 *
 */

$env_path = __DIR__ . '/.env';

if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
        $_ENV[trim($name)] = trim($value);
    }
}

define('BASEPATH', $system_path);

/**
 * The path information to make available as a constant for those
 * who may need it when working with the files in the system folder.
 * @deprecated 3.1.0	This constant is no longer necessary.
 */
define('VIEWPATH', $application_path.'views'.DIRECTORY_SEPARATOR);

require_once BASEPATH . 'core/CodeIgniter.php';

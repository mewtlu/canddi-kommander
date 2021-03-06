<?php
/**
 *
 * @author     Tim Langley
 * @date        2010-08-11
 *
 **/

define('ENVIRONMENT_DEVELOPMENT', 'development');
define('ENVIRONMENT_UNIT_TEST', 'unit-test');
define('ENVIRONMENT_PRODUCTION', 'production');
define('ENVIRONMENT_PRODUCTION_FRONTEND', 'production-frontend');
define('ENVIRONMENT_PRODUCTION_BACKEND', 'production-backend');

define('ENVIRONMENT_PREPRODUCTION', 'pp');
define('ENVIRONMENT_PREPRODUCTION_FRONTEND', 'pp-frontend');
define('ENVIRONMENT_PREPRODUCTION_BACKEND', 'pp-backend');

if (!getenv('APPLICATION_PATH')) {
	throw new Exception("Missing APPLICATION_PATH unable to start");
}
if (!getenv('APPLICATION_ENV')) {
	throw new Exception("Missing APPLICATION_ENV unable to start");
}
if (!getenv('VENDOR_PATH')) {
	throw new Exception("Missing VENDOR_PATH unable to start");
}
if (!getenv('APPLICATION_CONFIG_PATH')) {
	throw new Exception("Missing APPLICATION_CONFIG_PATH unable to start");
}
if (!defined('ENVIRONMENT_TYPE_PATH')) {
	throw new Exception("Missing ENVIRONMENT_TYPE_PATH unable to start");
}

define('APPLICATION_ENV', getenv('APPLICATION_ENV'));
define('VENDOR_PATH', getenv('VENDOR_PATH'));
define('APPLICATION_CONFIG_PATH', getenv('APPLICATION_CONFIG_PATH'));

/**
 * Include paths set by default
 *
 * @author Tim Langley
 **/
$paths = array(
	VENDOR_PATH,
	APPLICATION_PATH
);
set_include_path(implode(PATH_SEPARATOR, $paths));

require_once VENDOR_PATH.'/autoload.php';

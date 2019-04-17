<?php
/**
* @category
* @package
* @copyright  2011-03-01 (c) 2011-12 Campaign and Digital Intelligence
* @license
* @author     Tim Langley
**/

// Fix REQUEST_URI because for some reason PHPUnit hates it.
if (!isset($_SERVER['REQUEST_URI']) and isset($_SERVER['SCRIPT_NAME']))
{
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    if (isset($_SERVER['QUERY_STRING']) and
        !empty($_SERVER['QUERY_STRING']))
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
}

ini_set("memory_limit","1500M");
set_time_limit(0);



// PHP unit needs to get these from the phpunit script.
// It may eventually get them from the .profile of the user
putenv('APPLICATION_PATH='.realpath("./"));
putenv('APPLICATION_ENV=unit-test');
putenv('APPLICATION_CONFIG_PATH='.realpath("./Canddi/Helper/Config/config/canddi"));
putenv('VENDOR_PATH='.realpath("./vendor/"));

define('APPLICATION_PATH',   realpath("./"));
define('TEST_PATH',          realpath("../../test/php/"));
define('MOCKERY_PATH',       realPath("./vendor/mockery/mockery/library"));

define('ENVIRONMENT_TYPE_PATH', TEST_PATH.'/../');
require_once APPLICATION_PATH.'/environments.php';

require_once TEST_PATH.'/cli/CliControllerTestCase.php';
require_once TEST_PATH.'/Canddi/TestCase.php';

require_once VENDOR_PATH.'/autoload.php';

require_once MOCKERY_PATH.'/Mockery.php';
require_once MOCKERY_PATH.'/Mockery/Loader.php';
require_once MOCKERY_PATH.'/Mockery/Configuration.php';
$mockery_loader = new \Mockery\Loader;
$mockery_loader->register();
Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
Mockery::getConfiguration()->allowMockingMethodsUnnecessarily(false);

Zend_Session::$_unitTestEnabled = true;
Zend_Session::start();

$paths = array(
    VENDOR_PATH,
    VENDOR_PATH.'/zendframework/zendframework1/library',
    APPLICATION_PATH
);
set_include_path(implode(PATH_SEPARATOR, $paths));



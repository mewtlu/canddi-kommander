<?php

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

define('ENVIRONMENT_TYPE_PATH', TEST_PATH.'/../');
require_once APPLICATION_PATH.'/environments.php';

require_once TEST_PATH.'/cli/CliControllerTestCase.php';
require_once TEST_PATH.'/Canddi/TestCase.php';

require_once VENDOR_PATH.'/autoload.php';

require_once 'Mockery/Loader.php';
require_once 'Mockery/Configuration.php';
$mockery_loader = new \Mockery\Loader;
$mockery_loader->register();
Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
Mockery::getConfiguration()->allowMockingMethodsUnnecessarily(false);

require_once ZEND_PATH.'/Phly/Mustache/_autoload.php';

Zend_Session::$_unitTestEnabled = true;
Zend_Session::start();

if (in_array('--coverage-html', $_SERVER['argv'])) {
    $bUseFilter = true;
    if (false == $bUseFilter) {
        PHPUnit_Util_Filter::setFilter(true);
        PHPUnit_Util_Filter::addDirectoryToFilter(TEST_PATH.'/');
        PHPUnit_Util_Filter::removeDirectoryFromFilter(APPLICATION_PATH);
    } else {
        PHPUnit_Util_Filter::addDirectoryToWhitelist(APPLICATION_PATH);
        PHPUnit_Util_Filter::removeDirectoryFromWhitelist(TEST_PATH.'/');

        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/Canddi/.classmap.php");

        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/canddi.com/application.php");
        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/canddi.com/.classmap.php");

        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/cli/cli.php");
        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/cli/.classmap.php");

        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/cdn.canddi.com/application.php");
        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/cdn.canddi.com/.classmap.php");

        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/i.canddi.com/application.php");
        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/i.canddi.com/.classmap.php");

        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/s.canddi.com/application.php");
        PHPUnit_Util_Filter::removeFileFromWhitelist(APPLICATION_PATH."/s.canddi.com/.classmap.php");
        //@DanDart
        //  We've got a problem here
        //      canddi.com/module/CookieController AND
        //      s.canddi.com/module/CookieController
        //  These have the same class name hence we can't do code coverage
        //  oops - how can we solve this :(
        PHPUnit_Util_Filter::removeDirectoryFromWhitelist(APPLICATION_PATH.'/s.canddi.com/modules/module/controllers');
    }
}

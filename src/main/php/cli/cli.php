<?php
/**
 * @category
 * @package
 * @copyright  2010-12-14, Campaign and Digital Intelligence Ltd
 * @license
 * @author     Tim Langley
 **/
//In the commandline we need to set it to run forever ;-)
set_time_limit(0);
if(!getenv('APPLICATION_PATH') ) {
    throw new Exception("Missing APPLICATION_PATH unable to start");
}

define('APPLICATION_PATH',          getenv('APPLICATION_PATH'));
define('ENVIRONMENT_TYPE_PATH',     APPLICATION_PATH.'/cli/');
require_once APPLICATION_PATH . '/environments.php';

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/cli/config/application.ini');


try {
    $opts = new Zend_Console_Getopt(
        array(
            'help|h' => 'Displays usage information.',
            'action|a=s' => 'Action to perform in format of controller.action',
            'param|p=s' => 'Any parameters in query string format Key1=Value1&Key2=Value2'
        )
    );
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getMessage() . "\n\n" . $e->getUsageMessage());
}

if (isset($opts->h) || !isset($opts->a)) {
    echo $opts->getUsageMessage();
    exit;
}

$arrActionParams = explode('.', $opts->a);
if (2 > count($arrActionParams) && 4 < count($arrActionParams)) {
    echo 'Invalid number of action parameters. Usage: -a controller.action or -a module.controller.action.verb'.PHP_EOL;
    exit;
}

if(2 === count($arrActionParams)) {
    $module     = "listeners";
    $controller = $arrActionParams[0];
    $action     = $arrActionParams[1];
    $strVerb    = cliCommon_Controller_Dispatcher_Cli::DEFAULT_METHOD;
} else {
    $module     = $arrActionParams[0];
    $controller = $arrActionParams[1];
    $action     = $arrActionParams[2];
    $strVerb    = isset($arrActionParams[3])?$arrActionParams[3]:cliCommon_Controller_Dispatcher_Cli::DEFAULT_METHOD;
}

$arrParam = array();
if (isset($opts->p)) {
    parse_str($opts->p, $arrParam);
}

$arrParam[cliCommon_Controller_Dispatcher_Cli::PARAM_METHOD] = $strVerb;

$front = Zend_Controller_Front::getInstance();
$front->setRequest(new Zend_Controller_Request_Simple($action, $controller, $module, $arrParam));
$front->setRouter(new cliCommon_Controller_Router_Cli());
$front->setResponse(new Zend_Controller_Response_Cli());
$front->setDispatcher(new cliCommon_Controller_Dispatcher_Cli());
$errorHandler = new Zend_Controller_Plugin_ErrorHandler();
$front->registerPlugin($errorHandler, 100);
$error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');
$error->setErrorHandler(
    array(
        'controller'    => 'error',
        'action'        => 'error'
    )
);

//For the cli controller we don't want to be buffering output
$front->setParam('disableOutputBuffering', true);

$application->bootstrap();
$front->dispatch();
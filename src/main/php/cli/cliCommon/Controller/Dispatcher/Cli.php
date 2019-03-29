<?php

class cliCommon_Controller_Dispatcher_Cli
    extends Zend_Controller_Dispatcher_Standard
{
    const PARAM_METHOD = 'method';
    const DEFAULT_METHOD = 'get';

    /**
     * Returns whether the specified class implements
     *    Cli_Abstract
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return boolean
     * @author Dan Dart
     **/
    public function isCliController(
        Zend_Controller_Request_Abstract $request
    )
    {
        $className = $this->getControllerClass($request);
        if (!$className) {
            return false;
        }

        $finalClass = $className;
        if (($this->_defaultModule != $this->_curModule)
            || $this->getParam('prefixDefaultModule')
        ) {
            $finalClass = $this->formatClassName($this->_curModule, $className);
        }
        if (!class_exists($finalClass, false)) {
            return false;
        }
        return is_subclass_of($finalClass, "Cli_Abstract");
    }

    /**
     * Determine the action name
     *
     * First attempt to retrieve from request; then from request params
     * using action key; default to default action
     *
     * Returns formatted action name
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    public function getActionMethod(
        Zend_Controller_Request_Abstract $request
    )
    {
        $action = $request->getActionName();
        if (empty($action)) {
            $action = $this->getDefaultAction();
            $request->setActionName($action);
        }

        $actionName = $this->formatActionName($action);

        if ($this->isCliController($request)) {
            $actionName .= "_" . strtoupper($request->getParam(self::PARAM_METHOD));
        }

        return $actionName;
    }
}
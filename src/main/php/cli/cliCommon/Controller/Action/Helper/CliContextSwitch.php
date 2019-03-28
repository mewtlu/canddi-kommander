<?php
class cliCommon_Controller_Action_Helper_CliContextSwitch
    extends Zend_Controller_Action_Helper_ContextSwitch
{
    /**
     * Define the parameter name to switch context.
     **/
    protected $_contextParam = 'accept';

    /**
     * This stops the default constructor running
     *
     * @param string $options
     * @author Tim Langley
     **/
    public function __construct($options = null)
    {
    }

    /**
     * All actions point back to the global action
     * or return the complete list of actions
     *
     * @param string $action
     * @return array
     * @author Tim Langley
     **/
    public function getActionContexts($action = null)
    {
        return parent::getActionContexts($action ? 'global' : null);
    }

    /**
     * Every Action has an Action Context
     * Essentially this disables the checks
     *
     * @param string $action
     * @param string $context
     * @return void
     * @author Tim Langley
     **/
    public function hasActionContext($action, $context)
    {
        return true;
    }

    /**
     * It's not possible to add individual Action Contexts
     *
     * @param string $action
     * @param string $context
     * @return void
     * @author Tim Langley
     **/
    public function addActionContext($action, $context)
    {
        throw new Zend_Controller_Action_Exception(
            'You must call addGlobalContext() not addActionContext()'
        );
    }

    /**
     * Instead of ActionContexts we'll set the Global one
     *
     * @param string $contexts
     * @return void
     * @author Tim Langley
     **/
    public function addGlobalContext($contexts)
    {
        return parent::addActionContext('global', $contexts);
    }

    /**
     * Add new context
     *
     * @param  string $context Context type
     * @param  array  $spec    Context specification
     * @throws Zend_Controller_Action_Exception
     * @return Zend_Controller_Action_Helper_ContextSwitch (fluent)
     */
    public function addContext($context, array $spec)
    {
        parent::addContext($context, $spec);
        return $this->_setDisableLayout($context, $spec);
    }

    /**
     * Should we automatically disable the layout
     *
     * @param string $context
     * @return boolean
     * @author Tim Langley
     **/
    protected function _getDisableLayout($context)
    {
        if (!isset($this->_contexts[$context]['disableLayout'])) {
            return false;
        }
        return $this->_contexts[$context]['disableLayout'];
    }

    /**
     * Sets whether to disable the layout for this context
     *
     * @param string $context
     * @param string $spec
     * @return void
     * @author Tim Langley
     **/
    protected function _setDisableLayout($context, $spec)
    {
        $bDisableLayout = isset($spec['disableLayout']) ?
            $spec['disableLayout'] :
            false;
        $this->_contexts[$context]['disableLayout'] = $bDisableLayout;
        return $this;
    }

    /**
     * Initiate the correct context
     *
     * @param string $format
     * @return void
     * @author Tim Langley
     **/
    public function initContext($format = null)
    {
        $this->_currentContext = null;

        $request = $this->getRequest();
        $action = $request->getActionName();

        // Return if no context switching enabled, or no context switching
        // enabled for this action
        $contexts = $this->getActionContexts($action);
        if (empty($contexts)) {
            return;
        }

        // This will ALWAYS run the ContextSwitch
        if (!$context = $request->getParam($this->getContextParam())) {
            $context = $format;
        }
        //If nothing specified then we'll use the default one
        if (is_null($context)) {
            $context = $this->getDefaultContext();
        }

        //If an invalid context parameter is specified then exception
        if (!$this->hasContext($context)) {
                throw new Common_Exception_Error415($context);
        }

        $suffix = $this->getSuffix($context);

        $this->_getViewRenderer()->setViewSuffix($suffix);

        $headers = $this->getHeaders($context);
        if (!empty($headers)) {
            $response = $this->getResponse();
            foreach ($headers as $header => $content) {
                $response->setHeader($header, $content);
            }
        }

        if ($this->_getDisableLayout($context)) {
                $layout = Zend_Layout::getMvcInstance();
            if (null !== $layout) {
                $layout->disableLayout();
            }
        }

        $callback = $this->getCallback($context, self::TRIGGER_INIT);
        if (null !== $callback) {
            if (is_string($callback) && method_exists($this, $callback)) {
                $this->$callback();
            } else if (is_string($callback) && function_exists($callback)) {
                $callback();
            } else if (is_array($callback)) {
                call_user_func($callback);
            } else {
                        throw new Zend_Controller_Action_Exception(
                    sprintf('Invalid context callback registered for context "%s"', $context)
                );
            }
        }

        $this->_currentContext = $context;
    }


    /**
     * JSON pretty post processing
     *
     * @return void
     */
    public function postJsonPrettyContext()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if ($view instanceof Zend_View_Interface) {
            /**
             * @see Zend_Json
             */
            if(method_exists($view, 'getVars')) {
                ////require_once 'Zend/Json.php';
                $vars = Zend_Json::prettyPrint(Canddi_Helper_Json::encode($view->getVars()));
                $this->getResponse()->setBody($vars);
            } else {
                ////require_once 'Zend/Controller/Action/Exception.php';
                throw new Zend_Controller_Action_Exception('View does not implement the getVars() method needed to encode the view into JSON');
            }
        }
    }
}

<?php
/**
 * @category
 * @package
 * @license
 * @author     Luke Roberts
 **/

namespace Canddi\Kommander\Exception\Fatal;

class ResponseException extends \Canddi_Exception_Fatal
{

    public function __construct($intStatus, $strMessage, $arrErrors)
    {
      $this->setStatus($intStatus);

      $strErrorMessage = "Error in response from Github API: (%s %s)";
      if (isset($arrErrors)) { // Only append errors if they're in the response
        $strErrorMessage .= "\nErrors: %s";
      }
      /* Sometimes Github returns ugly formatted messages so let's clean them up: */
      $strMessage = preg_replace('/\n+/', ' ', $strMessage); // replace newlines with spaces

      parent::__construct(sprintf($strErrorMessage, $intStatus, $strMessage, JSON_encode($arrErrors, JSON_PRETTY_PRINT)));
    }

    public function getStatus()
    {
      return $this->status;
    }

    public function setStatus($intStatus)
    {
      return $this->status = $intStatus;
    }
}
<?php

trait Canddi_Trait_Singleton {
  /**
   * This is an array (one per singleton)
   *
   * @author  Tim Langley
   * @var     array
   **/
  private static $_arrConfigInstances = array();

  /**
   * This is the ZendConfigFile
   *
   * @author  Tim Langley
   * @var     Zend_Config
   **/
  protected $_arrConfig;

  public static function getInstance()
  {
    $strClass = get_called_class();
    if (isset(self::$_arrConfigInstances[$strClass])) {
        return self::$_arrConfigInstances[$strClass];
    }

    //Otherwise we should create one
    $newConfigInstance = new $strClass();
    self::$_arrConfigInstances[$strClass] = $newConfigInstance;
    return $newConfigInstance;
  }

  /**
   * Injects a mock helper instance for testing
   *
   * @param   Canddi_Helper_Config_Abstract $mockInstance
   * @return  Canddi_Helper_Config_Abstract
   * @author  Tim Langley
   **/
  public static function inject($mockInstance)
  {
      $strClass = get_called_class();
      self::$_arrConfigInstances[$strClass] = $mockInstance;
      return $mockInstance;
  }

  /**
   * Also used for testing - this resets all the instances
   *
   * @return void
   * @author Tim Langley
   **/
  public static function reset()
  {
      self::$_arrConfigInstances = array();
  }
}
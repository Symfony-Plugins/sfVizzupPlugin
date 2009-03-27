<?php
/**
 * sfVizzup singleton class. Contains proxy method vizzupClient class
 *
 * @package     sfVizzupPlugin
 * @author      Lionel Guichard <lionel.guichard@gmail.com>
 */
 
class sfVizzup
{
  private $vizzup = null;
  
  private static $instance = null;
	
	private function __construct(){}
	
  public static function getInstance()
  {
    $class = __CLASS__;
    if (!(self::$instance instanceof $class))
    {
      self::$instance = new $class();
      self::$instance->initialize();
    }
    return self::$instance;
  }
  
  protected function initialize()
  {
    $api_key = sfConfig::get('app_sf_vizzup_plugin_api_key');

    if (!$api_key)
    {
      throw new sfException("app_sf_vizzup_plugin_api_key not defined in app.yml");
    }
    
    if (sfConfig::get('sf_logging_enabled'))
    {
      sfContext::getInstance()->getLogger()->info('{sfVizzup} initialization');
    }

    $this->vizzup = new vizzupClient($api_key);
  }
  
  public function __clone()
  {
  	throw new sfException("Le clônage n\'est pas autorisé.");
  }
  
  public function __call($m, $a)
  {
    return call_user_func_array(array($this->vizzup, $m), $a);
  }
  
  public function __get($m)
  {
    return $this->vizzup->$m;
  }
}
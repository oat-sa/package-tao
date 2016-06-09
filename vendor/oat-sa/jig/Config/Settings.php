<?php
/**
 * This class provides a settings facility to avoid cluttering of the global name space 
 * whilst granting general access to variables. All functions must be called staticly!
 * 
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author     (C) 2008 Claudia Kosny <claudia@kosny>
 * @author     (C) 2008 Dieter Raber <me@dieterraber.net>
 * @author     (C) 2009 Xavier Arnal <arnal@h2a.lu>
 */
 
 
namespace Jig\Config;

use Jig\Utils\ArrayUtils;
use Jig\Utils\FsUtils;
use Symfony\Component\Yaml\Yaml;

class Settings {
  
  /**
   * the place to store the data
   *
   * @access  public
   * @var     array
   */
  protected $data = array();
  
  
  /**
   * Hold an instance of the class
   *
   * @access  private
   */ 
  private static $instance;
  
  
  /**
   * A private constructor; prevents direct creation of object
   */
  private function __construct() {
  }
  
  
  /**
   * creates and returns the global instance of the settings class
   *
   * @return object 
   */  
  public static function getInstance() {
    if(!isset(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class;
    }
    return self::$instance;
  }


  /**
   * Use this to pass a bunch of data at once to the object
   *
   * @param $data, can be an array or a file of the types yaml, json or ini, PHP will be executed!
   * @param bool $override, by default all existing data are replaced
   */
  public static function init($data, $override=true){
    $settingsObj = self::getInstance();
    if(is_string($data)){
      
      ob_start();
      include($data);

      switch(FsUtils::getFileExtension($data)){
        case 'yaml':
        case 'yml':
          $data = Yaml::parse(ob_get_clean());
          break;

        case 'json':
          $data = json_decode(ob_get_clean(), 1);
          break;

        case 'ini':
          $data = parse_ini_string(ob_get_clean());
          break;
      }
    }

    if($override){
      $settingsObj -> data = $data;
    }
    else {
      $settingsObj -> data = ArrayUtils::arrayMergeRecursiveDistinct($settingsObj -> data, $data);
    }
  }
    
  
  /**
   * Writes data to the settings. Writing can be done can be done in several ways, i.e. name can either be a 
   * a string or a number. Or it can be a string followed by [], which would write the data in an array.
   * Or it can be  be a list, separated by periods. This would be converted to a multi level array.
   * 
   * @code
   * self::write('foo','bar')
   * self::write('foo[]','bar') // works like $array_name[]
   * self::write('foo.bar', 'quux') // works like an object in JavaScript
   * @endcode
   * 
   * @param string $name the name of the variable to write, use [] at the end if you want to write in an array
   * @param mixed $value the content of the variable to write
   * @return bool
   */  
  public static function write($name, $value) {
    $settingsObj = self::getInstance(); 
    if(strpos($name, '[]') !== false) {
      $name = substr($name,0, -2);
      $settingsObj -> data[$name][] = $value;
      return true;
    }
    if(strpos($name, '.') !== false) {
      $current[0] =& $settingsObj -> data;
      $i = 1;
      //create the array name 
      $name = explode('.', $name);
      foreach($name as $key)  {
        if(!isset($current[$i-1][$key])) {        
          $current[$i-1][$key] = null;
        }
        $current[$i] =& $current[$i-1][$key];
        $i++;
      } 
      //now set the value
      $current[$i-2][$key] = $value; 
      return true;
    }
    $settingsObj -> data[$name] = $value;
    return true;
  }

  
  /**
   * deletes data from settings
   * 
   * @code
   * self::delete('foo')
   * @endcode
   * 
   * @todo Should be able to handle arrays in the same fashion as storage:: write()
   *
   * @param string $name the name of the variable to delete
   * @return void
   */  
  public static function delete($name) {
    $settingsObj = self::getInstance();
    if(!empty($settingsObj -> data[$name])) {
      $settingsObj -> data[$name] = null;
    }
  }
  
  /**
   * modifies data in settings, basically an alias of write
   * 
   * @code
   * self::modify('foo')
   * @endcode
   *
   * @param string $name the name of the variable to modify
   * @param mixed $value the content of the variable to modify
   * @return void
   */  
  public static function modify($name, $value) {
    self::write($name, $value);
  }
  
  /**
   * reads data from settings
   *  
   * @code
   * self::read('foo.bar')
   * @endcode
   *
   * @param string $name the name of the variable to read
   * @return mixed  the content of the requested variable or false if the variable does not exist
   */
  public static function read($name) {
    $settingsObj = self::getInstance();
    if(isset($settingsObj -> data[$name])) {
      return $settingsObj -> data[$name]; 
    } 
    else if(strpos($name, '.') !== false) {
      $current[0] =& $settingsObj -> data;
      $i = 1;
      $name = explode('.', $name);
      $return = false;
      foreach($name as $value) {
        if(!isset($current[$i-1][$value])){       
          return false;
        }
        $return = $current[$i-1][$value];
        $current[$i] =& $current[$i-1][$value];
        $i++;
      } 
      //now return the value
      return ($return);
    }
    return false;
  }
  

  /**
   * reads all data from settings
   *  
   * @code
   * self::readAll()
   * @endcode
   *
   * @return array of all items in settings, this is merely a debug function
   */
  public static function readAll() {
    $settingsObj = self::getInstance();
    if(isset($settingsObj -> data)) {
      return $settingsObj -> data; 
    }
    return false;
  }
}
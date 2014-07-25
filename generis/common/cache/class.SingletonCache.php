<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/cache/class.SingletonCache.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.03.2013, 15:43:51 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package common
 * @subpackage cache
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002006-includes begin
// section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002006-includes end

/* user defined constants */
// section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002006-constants begin
// section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002006-constants end

/**
 * Short description of class common_cache_SingletonCache
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package common
 * @subpackage cache
 */
abstract class common_cache_SingletonCache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instances
     *
     * @access private
     * @var array
     */
    private static $instances = array();

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return common_cache_Cache
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002008 begin
        $cacheName = get_called_class();
        if (!isset(self::$instances[$cacheName])) {
        	self::$instances[$cacheName] = new $cacheName();
        }
        
        $returnValue = self::$instances[$cacheName];
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002008 end

        return $returnValue;
    }

    /**
     * Short description of method getCached
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  function
     */
    public static function getCached($function)
    {
        $returnValue = null;

        // section 10-30-1--78--412d45c0:13d453d515d:-8000:000000000000200A begin
		$args = func_get_args();
		array_shift($args);
        if (!is_string($function)){
            $r = new ReflectionFunction($function);
            $serial = md5(
              $r->getFileName().
              $r->getStartLine().
              serialize($args)
            );
        } else {
            $serial = md5($function.serialize($args));
        }
        if (static::singleton()->has($serial)) {
        	$returnValue = static::singleton()->has($serial);
        } else { 
	        $returnValue = call_user_func_array($fn, $args);
	        static::singleton()->put($serial, $returnValue);
        }
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:000000000000200A end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    private function __construct()
    {
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002011 begin
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002011 end
    }

} /* end of abstract class common_cache_SingletonCache */

?>
<?php

/**
 * This file is part of the Jig package.
 * 
 * Copyright (c) 03-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Cache\Adapters\FileFormats;

use \Jig\Cache\Adapters\FileCacheAbstract;
use Jig\Cache\Interfaces\CacheInterface;

/**
 * Uses JSON as a generic format for file storage
 */
class GenericCache extends FileCacheAbstract implements CacheInterface{
  
  /**
   * Reads a resource from the cache
   * 
   * @param string $cacheFilePath
   * @return string|bool false if unreadable
   */
  public static function read($cacheFilePath) {
    
    if (!is_readable($cacheFilePath)) {
      return false;
    }
    
    $expirationCallback = function() use ($cacheFilePath) {
      self::delete($cacheFilePath);
    };
    
    $data = json_decode(file_get_contents($cacheFilePath), true);
    return self::getContent($data, $expirationCallback);
  }

  /**
   * Writes a resource to the cache, paths will ge created if applicable
   * 
   * @param type $cacheFilePath
   * @param mixed $data
   * @param mixed $duration can be a number of seconds or any argument accepted by strtotime()
   * @return int
   */
  public static function write($cacheFilePath, $data, $duration = 0) {
    $data      = self::getDataArray($data, $duration);
    return self::save($cacheFilePath, json_encode($data));
  }
}

<?php

/**
 * This file is part of the Jig package.
 * 
 * Copyright (c) 03-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Cache\Adapters;

use Jig\Cache\Adapters\FileCacheFactory;
use Jig\Cache\Interfaces\CacheInterface;


/**
 * FileCache. Uses JSON files as storage
 */
class FileCache implements CacheInterface {
  
  /**
   * Reads a resource from the cache
   * 
   * @param string $cacheFilePath
   * @return string
   */
  public static function read($cacheFilePath) {
    $className = FileCacheFactory::getClass($cacheFilePath);
    return $className::read($cacheFilePath);
  }

  /**
   * Writes a resource to the cache, paths will be created if applicable
   * 
   * @param string $cacheFilePath
   * @param mixed $data
   * @param mixed $duration can be a number of seconds or any argument accepted by strtotime()
   * @return int
   */
  public static function write($cacheFilePath, $data, $duration = 0) {
    $className = FileCacheFactory::getClass($cacheFilePath);
    return $className::write($cacheFilePath, $data, $duration);
  }
}

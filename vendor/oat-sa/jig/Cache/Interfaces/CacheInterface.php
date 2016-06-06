<?php

/**
 * This file is part of the Jig package.
 * 
 * Copyright (c) 03-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Cache\Interfaces;

/**
 * CacheInterface
 */
interface CacheInterface {
  
  /**
   * Reads a resource from the cache
   * 
   * @param string $cacheFilePath
   */
  public static function read($cacheFilePath);
  
  /**
   * Write a resource to the cache
   * 
   * @param type $cacheFilePath
   * @param mixed $data
   * @param mixed $duration can be a number of seconds or any argument accepted by strtotime()
   */
  public static function write($cacheFilePath, $data, $duration=0);
}
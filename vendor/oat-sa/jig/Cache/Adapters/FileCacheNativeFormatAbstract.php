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

use Jig\Cache\Adapters\FileCacheAbstract;
use Jig\Cache\Interfaces\CacheInterface;

/**
 * FileCacheNativeFormatAbstract. Uses native files as storage
 */
abstract class FileCacheNativeFormatAbstract extends FileCacheAbstract implements CacheInterface {

  /**
   * The format expected for a comment
   * 
   * @var string 
   */
  protected static $commentFormat = '// %s';
  
  
  /**
   * Reads a resource from the cache
   * 
   * @param string $cacheFilePath
   * @return string|false false if not readable
   */
  public static function read($cacheFilePath) {
    if (!is_readable($cacheFilePath)) {
      return false;
    }
    
    $content = file_get_contents($cacheFilePath); 
    $matches = [];
    $commentedTimestamp = strtok($content, "\n");
    preg_match('~([\d]+)~', $commentedTimestamp, $matches);
    $dataArray = [
      'expires' => !empty($matches[1]) ? $matches[1] : 1,
      'content' => substr($content, strlen($commentedTimestamp . "\n"))
    ];

    $expirationCallback = function() use ($cacheFilePath) {
      self::delete($cacheFilePath);
    };
    
    return self::getContent($dataArray, $expirationCallback);
  }

  /**
   * Writes a resource to the cache, paths will be created if applicable
   * 
   * @param type $cacheFilePath
   * @param mixed $data
   * @param mixed $duration can be a number of seconds or any argument accepted by strtotime()
   * @return int
   */
  public static function write($cacheFilePath, $data, $duration = 0) {
    $dataArray = self::getDataArray($data, $duration);
    return self::save(
      $cacheFilePath, 
      sprintf(
        static::$commentFormat . "\n", 
        $dataArray['expires']
      ) . $dataArray['content']
    );
  }
}

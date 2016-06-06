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

use Jig\Cache\CacheAbstract;

/**
 * FileCache. Uses JSON files as storage
 */
abstract class FileCacheAbstract extends CacheAbstract {

  /**
   * Delete a cache file. This can also be used to delete a file before its
   * actual expiration.
   * 
   * @param type $cacheFilePath
   */
  public static function delete($cacheFilePath) {
    if (is_file($cacheFilePath)) {
      unlink($cacheFilePath);
    }
  }
  
  /**
   * Save file, create directory if needed
   * 
   * @param string $cacheFilePath
   * @param string $data
   * @return int
   */
  protected static function save($cacheFilePath, $data){
    $directory = dirname($cacheFilePath);
    if (!is_dir($directory)) {
      mkdir($directory, 0777, true);
    }
    return file_put_contents($cacheFilePath, $data);
  }
}

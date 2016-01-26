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

use Jig\Utils\FsUtils;

/**
 * Factory of sorts, since all calls are static, only the class name is returned
 */
class FileCacheFactory {
  
  /**
   * Use native format if possible, generic json otherwise
   * 
   * @param string $cacheFilePath
   * @return string class name
   */
  public static function getClass($cacheFilePath) {
    $extension = strtolower(FsUtils::getFileExtension($cacheFilePath));
    if($extension === 'htm'){
      $extension = 'html';
    }
    else if($extension === 'yml'){
      $extension = 'yaml';
    }
    $nativeFormatClassName = __NAMESPACE__ . '\\FileFormats\\' . ucfirst($extension) . 'Cache';
    return class_exists($nativeFormatClassName) 
      ? $nativeFormatClassName 
      : __NAMESPACE__ . '\\FileFormats\\GenericCache';
  }
}

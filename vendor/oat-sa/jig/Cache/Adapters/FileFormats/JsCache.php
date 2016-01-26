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

use Jig\Cache\Adapters\FileCacheNativeFormatAbstract;

/**
 * Uses JavaScript files as storage
 */
class JsCache extends FileCacheNativeFormatAbstract {

  /**
   * The format expected for a comment
   * 
   * @var string 
   */
  protected static $commentFormat = '/* %s */';

  /**
   * There is no need to check a known text file for whether it's binary or not
   * 
   * @var string 
   */
  protected static $checkForBinary = false;
}

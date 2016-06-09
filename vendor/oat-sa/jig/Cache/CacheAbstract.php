<?php

/**
 * This file is part of the Jig package.
 * 
 * Copyright (c) 03-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Cache;

use Jig\Utils\StringUtils;

/**
 * CacheAbstract
 */
class CacheAbstract {

  /**
   * Check whether  the content is in a binary format
   * 
   * @var string 
   */
  protected static $checkForBinary = true;

  
  /**
   * Unserialize cache data and delete the record on expiration
   * 
   * @param string $data
   * @param string $expirationCallback
   * @return string|bool false if expired
   */
  protected static function getContent($data, $expirationCallback='') {
    
    if(self::isExpired($data['expires'])){
      if(is_callable($expirationCallback)) {
        $expirationCallback();
      }
      return false;
    }  
    $isBase64 = isset($data['base64']) ? $data['base64'] : false; 
    return self::decodeContent($data['content'], $isBase64);
  }
  
  
  /**
   * Compute whether an item is expired
   * 
   * @param int $expirationTime
   * @return bool
   */
  protected static function isExpired($expirationTime){
    return (int)$expirationTime < time();
  }

  
  /**
   * Create expiration timestamp
   * 
   * @param mixed $duration can be a number of seconds or any argument accepted by strtotime()
   * @return int
   * @throws JigException
   */
  protected static function computeDuration($duration = 0) {
    switch (true) {
      case !$duration;
        $duration = strtotime('+1 week');
        break;

      case !is_numeric($duration);
        $duration = strtotime($duration);
        break;

      case is_numeric($duration);
        $duration = intval($duration);
        break;

      default:
        throw new JigException('Invalid duration ' . $duration . ', caching not possible');
    }
    return $duration;
  }  
  

  /**
   * Encode content to base64 if binary
   * 
   * @param mixed $content
   * @return mixed|string
   */
  protected static function encodeContent($content) {  
    return static::$checkForBinary && is_string($content) && StringUtils::isBinary($content) 
      ? base64_encode($content) 
      : $content;
  }   
  

  /**
   * Decode data from base64 if binary
   * 
   * @param string $content, possibly encoded
   * @param bool $isBase64
   * @return string
   */
  protected static function decodeContent($content, $isBase64) {  
    return $isBase64 ? base64_decode($content) : $content;
  }
    

  /**
   * Serialize data and duration
   * 
   * @param mixed $content
   * @param mixed $duration can be a number of seconds or any argument accepted by strtotime()
   * @return string
   */
  protected static function getDataArray($content, $duration = 0) {
    $encodedContent = self::encodeContent($content);
    return [
      'expires' => self::computeDuration($duration),
      'content' => $encodedContent,
      'base64'  => ($encodedContent !== $content)
    ];
  }

}

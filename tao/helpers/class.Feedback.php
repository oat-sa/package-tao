<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Utility to display error messages and such
 *
 * @author Dieter Raber, <dieter@taotesting.com>
 * @package tao
 
 */
class tao_helpers_Feedback
{

  /**
   * @param $type
   * @param $message
   * @param array $settings
   * @return string
   */
  protected static function dispatch($type, $message, $settings=array()) {
    $defaults = array(
      'delayToClose' => 2000,
      'closer'       => true,
      'icon'         => true,
      'modal'        => false
    );
    $settings = array_merge($defaults, $settings);
    $closer = $settings['closer'] ? sprintf('<span class="icon-close close-trigger" title="%s"></span>', __('Remove Message')) : '';
    if(is_string($settings['icon'])){
      $icon = sprintf('<span class="%s"></span>', $settings['icon']);
    }
    else if($settings['icon'] === true) {
      $icon = sprintf('<span class="icon-%s"></span>', $type);
    }
    else {
      $icon = '';
    }
    return sprintf('<div class="feedback-%s">%s%s%s</div>', $type, $icon, $message, $closer);
  }

  /**
   * @param $message
   * @param array $settings
   * @return string
   */
  public static function error($message, $settings=array()) {
    return self::dispatch(__FUNCTION__, $message, $settings);
  }

  /**
   * @param $message
   * @param array $settings
   * @return string
   */
  public static function info($message, $settings=array()) {
    return self::dispatch(__FUNCTION__, $message, $settings);
  }

  /**
   * @param $message
   * @param array $settings
   * @return string
   */
  public static function success($message, $settings=array()) {
    return self::dispatch(__FUNCTION__, $message, $settings);
  }

  /**
   * @param $message
   * @param array $settings
   * @return string
   */
  public static function warning($message, $settings=array()) {
    return self::dispatch(__FUNCTION__, $message, $settings);
  }

}
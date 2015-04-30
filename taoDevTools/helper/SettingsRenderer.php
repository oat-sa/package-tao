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
 * @license GPLv2
 * @package taoDevTools
 *
 */
namespace oat\taoDevTools\helper;

use oat\oatbox\Configurable;
/**
 * Name generator to generate searchable names
 * 
 * Based on:
 *  http://www.englishbanana.com/ (public domain) 
 *  http://study4ielts.wikispaces.com/ (Creative Commons Attribution Share-Alike 3.0)
 * 
 * @author Joel Bout <joel@taotesting.com>
 *
 */
class SettingsRenderer
{
    static public function renderValue($settingValue) {
        if (is_array($settingValue)) {
            return self::renderArray($settingValue);
        } elseif (is_string($settingValue)) {
            return '"'.$settingValue.'"';
        } elseif (is_numeric($settingValue)) {
            return $settingValue;
        } elseif (is_object($settingValue) && $settingValue instanceof Configurable) {
            return get_class($settingValue).' : '.self::renderArray($settingValue->getOptions());
        } else {
            return 'other';
        }
    }
    
    static public function renderArray($settingArray) {
        $html = '<ul>';
        foreach ($settingArray as $key => $value) {
            $html .= ($key === 'password')
                ? "<li>".$key.' = PASSWORD_HIDDEN</li>'
                : "<li>".$key.(is_array($value) ? '' : ' = ').self::renderValue($value)."</li>";
        }
        $html .= "</ul>";
        return $html;
    }
}

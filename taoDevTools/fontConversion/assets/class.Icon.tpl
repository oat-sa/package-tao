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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

{DO_NOT_EDIT}

/**
 * Icon helper for tao - helpers/class.Icon.php
 *
 * Icons
 *
 * @access public
 * @author Dieter Raber, <dieter@taotesting.com>
 * @date   {DATE}
 * @package tao
 * @subpackage helpers
 */
class tao_helpers_Icon {

    /**
     * This function builds the actual HTML element and is used by all other functions. 
     * The doc for $options is the applicable for all other functions.
     * 
     * @param string $icon name of the icon to display
     * @param array $options (optional) hashtable with HTML attributes, also allows to set element="almostAnyHtmlElement"
     * @param string HTML element with icon
     */
    protected static function buildIcon($icon, $options=array()){
        $options['class'] = !empty($options['class']) ? $options['class'] . ' ' . $icon : $icon;
        $element = !empty($options['element']) ? $options['element'] : 'span';
        unset($options['element']);
        $retVal = '<' . $element . ' ';
        foreach($options as $key => $value) {
            $retVal .= $key . '="' . $value . '"';
        }
        $retVal .= '></' . $element . '>';
        return $retVal;
    }
	
    /**
     * List of all icons as constant
     */
{CONSTANTS}
	
    /**
     * List of all icons as function
     */

{FUNCTIONS}
}
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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */
namespace qtism\runtime\rendering\css;

/**
 * A collection of utility methods focusing on Cascading Style Sheets.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
    
    /**
     * Remap a given CSS selector following the $map array.
     * 
     * Example:
     * 
     * <code>
     * $map = array('prompt' => 'qti-prompt', 'div' => 'qti-div');
     * $selector = 'div > prompt';
     * echo Utils::mapSelector($selector, $map);
     * // .qti-div > .qti-prompt
     * </code> 
     * 
     * @param string $selector A Cascading Style Sheet selector.
     * @param array $map A QTI to XHTML CSS class map.
     */
    static public function mapSelector($selector, array $map) {
        foreach ($map as $k => $v) {
            $pattern = "/(?:(^|\s|\+|,|~|>)(${k})(\$|\s|,|\+|\.|\~|>|:|\[))/u";
            $count = 1;
            while ($count > 0) {
                $selector = preg_replace($pattern, '$1.' . $v . '$3', $selector, -1, $count);
            }
        }
        
        return $selector;
    }
}
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

namespace qtism\common\datatypes;

/**
 * A class focusing on providing utility methods
 * for QTI Datatypes handling.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
    
    /**
     * Whether a given $integer value is a QTI compliant
     * integer in the [-2147483647, 2147483647] range.
     * 
     * @param mixed $integer
     * @return boolean
     */
    static public function isQtiInteger($integer) {
        // QTI integers are twos-complement 32-bits integers.
        if (is_int($integer) === false) {
            return false;
        }
        else if ($integer > 2147483647) {
            return false;
        }
        else if ($integer < -2147483647) {
            return false;
        }
        else {
            return true;
        }
    }
}
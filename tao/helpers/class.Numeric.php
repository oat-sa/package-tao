<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Helper to process numeric input
 *
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--35f5fbfd:1379d8c2a8c:-8000:0000000000003AB5-includes begin
// section 127-0-1-1--35f5fbfd:1379d8c2a8c:-8000:0000000000003AB5-includes end

/* user defined constants */
// section 127-0-1-1--35f5fbfd:1379d8c2a8c:-8000:0000000000003AB5-constants begin
// section 127-0-1-1--35f5fbfd:1379d8c2a8c:-8000:0000000000003AB5-constants end

/**
 * Helper to process numeric input
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 * @subpackage helpers
 */
class tao_helpers_Numeric
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method parseFloat
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string value
     * @return float
     */
    public static function parseFloat($value)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--35f5fbfd:1379d8c2a8c:-8000:0000000000003AB6 begin
		$returnValue = str_replace(',', '.', $value);
		$returnValue = str_replace(' ', '', $returnValue);
		$p = strrpos($returnValue, '.');
		if ($p !== false) {
			$a = intval(str_replace('.', '', substr($returnValue, 0, $p)));
			$b = intval(substr($returnValue, $p + 1));
			$returnValue = floatval($a.'.'.$b);
		}
        // section 127-0-1-1--35f5fbfd:1379d8c2a8c:-8000:0000000000003AB6 end

        return (float) $returnValue;
    }

} /* end of class tao_helpers_Numeric */

?>
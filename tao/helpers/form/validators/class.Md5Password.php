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
 * TAO - tao\helpers\form\validators\class.Md5Password.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.05.2012, 11:25:15 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_validators_Password
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('tao/helpers/form/validators/class.Password.php');

/* user defined includes */
// section 127-0-1-1--d36e6ea:12597e82faa:-8000:0000000000001D71-includes begin
// section 127-0-1-1--d36e6ea:12597e82faa:-8000:0000000000001D71-includes end

/* user defined constants */
// section 127-0-1-1--d36e6ea:12597e82faa:-8000:0000000000001D71-constants begin
// section 127-0-1-1--d36e6ea:12597e82faa:-8000:0000000000001D71-constants end

/**
 * Short description of class tao_helpers_form_validators_Md5Password
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_form_validators
 */
class tao_helpers_form_validators_Md5Password
    extends tao_helpers_form_validators_Password
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  values
     * @return string
     */
    public function getValue($values)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--d36e6ea:12597e82faa:-8000:0000000000001D73 begin
		
		$returnValue = md5(parent::getRawValue());
		
        // section 127-0-1-1--d36e6ea:12597e82faa:-8000:0000000000001D73 end

        return (string) $returnValue;
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  values
     * @return boolean
     */
    public function evaluate($values)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--1e71c2ba:1370cdb32ad:-8000:00000000000039C7 begin
    	if (is_array($values) && count($values) == 2) {
        	list($first, $second) = $values;
        	$returnValue = $first == $second;
        	
        } elseif (isset($this->options['password2_ref'])) {
			$secondElement = $this->options['password2_ref'];
			if (is_null($secondElement) || ! $secondElement instanceof tao_helpers_form_FormElement) {
				throw new common_Exception("Please set the reference of the second password element");
			}
			if(md5($values) == $secondElement->getRawValue() && trim($values) != ''){
				$returnValue = true;
			}
        }
        // section 10-13-1-85--1e71c2ba:1370cdb32ad:-8000:00000000000039C7 end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_form_validators_Md5Password */

?>
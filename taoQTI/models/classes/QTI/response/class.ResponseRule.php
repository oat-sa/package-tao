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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoQTI/models/classes/QTI/response/class.ResponseRule.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 17:25:48 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoQTI_models_classes_QTI_response_ConditionalExpression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/class.ConditionalExpression.php');

/**
 * include taoQTI_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-includes begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-includes end

/* user defined constants */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-constants begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-constants end

/**
 * Short description of class taoQTI_models_classes_QTI_response_ResponseRule
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
abstract class taoQTI_models_classes_QTI_response_ResponseRule
        implements taoQTI_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        throw new common_Exception('class '.get_class($this).' needs to implement getRule', array('TAOITEMS', 'QTI', 'HARD'));
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

} /* end of abstract class taoQTI_models_classes_QTI_response_ResponseRule */

?>
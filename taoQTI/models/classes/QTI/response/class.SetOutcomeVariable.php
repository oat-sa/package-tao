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
 * TAO - taoQTI/models/classes/QTI/response/class.SetOutcomeVariable.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.01.2012, 09:35:30 with ArgoUML PHP module 
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
 * include taoQTI_models_classes_QTI_response_ResponseRule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/class.ResponseRule.php');

/* user defined includes */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:000000000000628A-includes begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:000000000000628A-includes end

/* user defined constants */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:000000000000628A-constants begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:000000000000628A-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
class taoQTI_models_classes_QTI_response_SetOutcomeVariable
    extends taoQTI_models_classes_QTI_response_ResponseRule
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute outcomeVariableIdentifier
     *
     * @access public
     * @var string
     */
    public $outcomeVariableIdentifier = '';

    /**
     * Short description of attribute expression
     *
     * @access public
     * @var Expression
     */
    public $expression = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string identifier
     * @param  Expression expression
     * @return mixed
     */
    public function __construct($identifier,  taoQTI_models_classes_QTI_expression_Expression $expression)
    {
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AE begin
        $this->outcomeVariableIdentifier	= $identifier;
        $this->expression					= $expression;
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AE end
    }

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

        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062A2 begin
		$returnValue = 'setOutcomeValue("'.$this->outcomeVariableIdentifier.'", '.$this->expression->getRule().');';
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062A2 end

        return (string) $returnValue;
    }

} /* end of class taoQTI_models_classes_QTI_response_SetOutcomeVariable */

?>
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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.RuleService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 06.09.2011, 09:22:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F93-includes begin
// section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F93-includes end

/* user defined constants */
// section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F93-constants begin
// section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F93-constants end

/**
 * Short description of class wfEngine_models_classes_RuleService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_RuleService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getExpression
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @return mixed
     */
    public function getExpression( core_kernel_classes_Resource $rule)
    {
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F94 begin
		$returnValue = null;
		
		$property = new core_kernel_classes_Property(PROPERTY_RULE_IF);
		$returnValue = new core_kernel_rules_Expression($rule->getUniquePropertyValue($property)->getUri(), __METHOD__);
		
		return $returnValue;
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F94 end
    }

} /* end of class wfEngine_models_classes_RuleService */

?>
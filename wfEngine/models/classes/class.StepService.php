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
 * TAO - wfEngine/models/classes/class.StepService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.10.2012, 11:40:54 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
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
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BAE-includes begin
// section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BAE-includes end

/* user defined constants */
// section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BAE-constants begin
// section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BAE-constants end

/**
 * Short description of class wfEngine_models_classes_StepService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_StepService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getPreviousSteps
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource step
     * @return array
     */
    public function getPreviousSteps( core_kernel_classes_Resource $step)
    {
        $returnValue = array();

        // section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BAF begin
        $stepClass = new core_kernel_classes_Class(CLASS_STEP);
		$returnValue= $stepClass->searchInstances(
			array(PROPERTY_STEP_NEXT => $step),
			array('like' => false, 'recursive' => true)
		);
        // section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BAF end

        return (array) $returnValue;
    }

    /**
     * Short description of method getNextSteps
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource step
     * @return array
     */
    public function getNextSteps( core_kernel_classes_Resource $step)
    {
        $returnValue = array();

        // section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BB2 begin
    	$nextStepProp = new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
        foreach ($step->getPropertyValues($nextStepProp) as $stepUri) {
        	$returnValue[] = new core_kernel_classes_Resource($stepUri);
        }
        // section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003BB2 end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_StepService */

?>
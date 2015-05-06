<?php
/**
 *   
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

/**
 * Short description of class wfEngine_models_classes_StepService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 
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

        
        $stepClass = new core_kernel_classes_Class(CLASS_STEP);
		$returnValue= $stepClass->searchInstances(
			array(PROPERTY_STEP_NEXT => $step),
			array('like' => false, 'recursive' => true)
		);
        

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

        
    	$nextStepProp = new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
        foreach ($step->getPropertyValues($nextStepProp) as $stepUri) {
        	$returnValue[] = new core_kernel_classes_Resource($stepUri);
        }
        

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_StepService */

?>
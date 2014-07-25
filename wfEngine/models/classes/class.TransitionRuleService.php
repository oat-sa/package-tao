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
 * Short description of class wfEngine_models_classes_TransitionRuleService
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_models_classes_TransitionRuleService
    extends wfEngine_models_classes_RuleService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getElseActivity
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource rule
     * @return core_kernel_classes_Resource
     */
    public function getElseActivity( core_kernel_classes_Resource $rule)
    {
        $returnValue = null;

        
        $elseProperty = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE);        
        $returnValue = $rule->getOnePropertyValue($elseProperty);    
        

        return $returnValue;
    }

    /**
     * Short description of method getThenActivity
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource rule
     * @return core_kernel_classes_Resource
     */
    public function getThenActivity( core_kernel_classes_Resource $rule)
    {
        $returnValue = null;

        
        $thenProperty = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN);
        try{
            $returnValue = $rule->getUniquePropertyValue($thenProperty);
       
        }
        catch (common_Exception $e){
            throw new wfEngine_models_classes_ProcessExecutionException('Transition Rule ' . $rule->getUri() . ' do not have value for Then Property');
        }
        

        return $returnValue;
    }

    /**
     * Short description of method isTransitionRule
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource rule
     * @return boolean
     */
    public function isTransitionRule( core_kernel_classes_Resource $rule)
    {
        $returnValue = (bool) false;

        
        $returnValue = $rule->hasType(new core_kernel_classes_Class(CLASS_TRANSITIONRULES));
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getExpression
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource rule
     * @return core_kernel_classes_Expression
     */
    public function getExpression( core_kernel_classes_Resource $rule)
    {
        $returnValue = null;

        
        $ruleObj = new core_kernel_rules_Rule($rule->getUri());
        $returnValue = $ruleObj->getExpression();
        

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_TransitionRuleService */

?>
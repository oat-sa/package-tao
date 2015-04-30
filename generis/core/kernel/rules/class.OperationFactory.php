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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 * 
 */

/**
 * Short description of class core_kernel_rules_OperationFactory
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis
 
 */
class core_kernel_rules_OperationFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createOperation
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Term term1
     * @param  Term term2
     * @param  Resource operator
     * @return core_kernel_rules_Operation
     */
    public static function createOperation( core_kernel_rules_Term $term1,  core_kernel_rules_Term $term2,  core_kernel_classes_Resource $operator)
    {
        $returnValue = null;

        
        $operationClass = new core_kernel_classes_Class(CLASS_OPERATION,__METHOD__); 
        $label = 'Def Operation Label ' . $term1->getLabel() . ' ' . $operator->getLabel() . ' ' . $term2->getLabel();
        $comment = 'Def Operation Comment ' . $term1->getUri() . ' ' . $operator->getUri(). ' ' . $term2->getUri();
		$operatorProperty = new core_kernel_classes_Property(PROPERTY_OPERATION_OPERATOR,__METHOD__);
        $firstOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP,__METHOD__);
		$secondOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP,__METHOD__);		
        $termOperationInstance = core_kernel_classes_ResourceFactory::create($operationClass,$label,$comment);
        $returnValue = new core_kernel_rules_Operation($termOperationInstance->getUri());
        $returnValue->debug = __METHOD__;
        $returnValue->setPropertyValue($operatorProperty,$operator->getUri());
        $returnValue->setPropertyValue($firstOperand,$term1->getUri());
		$returnValue->setPropertyValue($secondOperand,$term2->getUri());
        

        return $returnValue;
    }

}
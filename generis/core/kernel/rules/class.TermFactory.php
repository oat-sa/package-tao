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
 * Short description of class core_kernel_rules_TermFactory
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis
 
 */
class core_kernel_rules_TermFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createConst
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string $constant
     * @return core_kernel_rules_Term
     */
    public static function createConst($constant)
    {
        $returnValue = null;

        
        $termConstClass = new core_kernel_classes_Class(CLASS_TERM_CONST,__METHOD__); 
		$termValueProperty = new core_kernel_classes_Property(PROPERTY_TERM_VALUE,__METHOD__);
		$logicalOperatorProperty = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR,__METHOD__);
		$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
        
		$label = 'Def Term Constant Label : ' . $constant;
		$comment = 'Def Term Constant Comment : '. $constant;
        $constantResource =  core_kernel_classes_ResourceFactory::create($termConstClass,$label , $comment);
        $returnValue = new core_kernel_rules_Term($constantResource->getUri());
        $returnValue->setPropertyValue($terminalExpressionProperty,$returnValue->getUri());
		$returnValue->setPropertyValue($logicalOperatorProperty,INSTANCE_EXISTS_OPERATOR_URI);
		$returnValue->setPropertyValue($termValueProperty,$constant);
		$returnValue->debug = __METHOD__;
        

        return $returnValue;
    }

    /**
     * Short description of method createSPX
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource $subject
     * @param  Property $predicate
     * @return core_kernel_rules_Term
     */
    public static function createSPX( core_kernel_classes_Resource $subject,  core_kernel_classes_Property $predicate)
    {
        $returnValue = null;

        
        $termSPXClass = new core_kernel_classes_Class(CLASS_TERM_SUJET_PREDICATE_X,__METHOD__);
		$label = 'Def Term SPX Label : ' .$subject->getLabel() . ' - ' . $predicate->getLabel();
		$comment = 'Def Term SPX Label : ' .$subject->getUri() . ' ' . $predicate->getUri();
        $SPXResource = core_kernel_classes_ResourceFactory::create($termSPXClass, $label,$comment );
     	$returnValue = new core_kernel_rules_Term($SPXResource->getUri());
     		
     	$subjectProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET,__METHOD__);
		$predicateProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE,__METHOD__);
     	$returnValue->setPropertyValue($subjectProperty,$subject->getUri());
     	$returnValue->setPropertyValue($predicateProperty,$predicate->getUri());
        

        return $returnValue;
    }

} 
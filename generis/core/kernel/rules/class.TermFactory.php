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
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\rules\class.TermFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:22 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000173E-includes begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000173E-includes end

/* user defined constants */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000173E-constants begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000173E-constants end

/**
 * Short description of class core_kernel_rules_TermFactory
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
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
     * @param  string constant
     * @return core_kernel_rules_Term
     */
    public static function createConst($constant)
    {
        $returnValue = null;

        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001757 begin
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
        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001757 end

        return $returnValue;
    }

    /**
     * Short description of method createSPX
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource subject
     * @param  Property predicate
     * @return core_kernel_rules_Term
     */
    public static function createSPX( core_kernel_classes_Resource $subject,  core_kernel_classes_Property $predicate)
    {
        $returnValue = null;

        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000175A begin
        $termSPXClass = new core_kernel_classes_Class(CLASS_TERM_SUJET_PREDICATE_X,__METHOD__);
		$label = 'Def Term SPX Label : ' .$subject->getLabel() . ' - ' . $predicate->getLabel();
		$comment = 'Def Term SPX Label : ' .$subject->getUri() . ' ' . $predicate->getUri();
        $SPXResource = core_kernel_classes_ResourceFactory::create($termSPXClass, $label,$comment );
     	$returnValue = new core_kernel_rules_Term($SPXResource->getUri());
     		
     	$subjectProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET,__METHOD__);
		$predicateProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE,__METHOD__);
     	$returnValue->setPropertyValue($subjectProperty,$subject->getUri());
     	$returnValue->setPropertyValue($predicateProperty,$predicate->getUri());
        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000175A end

        return $returnValue;
    }

} /* end of class core_kernel_rules_TermFactory */

?>
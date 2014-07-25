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
 * Generis Object Oriented API - core\kernel\rules\class.OperationFactory.php
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
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-includes begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-includes end

/* user defined constants */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-constants begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-constants end

/**
 * Short description of class core_kernel_rules_OperationFactory
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
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

        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000174F begin
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
        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000174F end

        return $returnValue;
    }

} /* end of class core_kernel_rules_OperationFactory */

?>
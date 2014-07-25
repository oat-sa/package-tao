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
 * Generis Object Oriented API - core\kernel\rules\class.Rule.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:21 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/* user defined includes */
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-includes begin
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-includes end

/* user defined constants */
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-constants begin
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-constants end

/**
 * Short description of class core_kernel_rules_Rule
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_Rule
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute expression
     *
     * @access public
     * @var Expression
     */
    public $expression = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_rules_Expression
     */
    public function getExpression()
    {
        $returnValue = null;

        // section 10-13-1--99--2ca656b4:11c9ebb5ddd:-8000:0000000000000ED7 begin
		common_Logger::i('Evaluating rule '.$this->getLabel().'('.$this->getUri().')', array('Generis Rule'));
         if(empty($this->expression)){
         	$property = new core_kernel_classes_Property(PROPERTY_RULE_IF);
         	$this->expression = new core_kernel_rules_Expression($this->getUniquePropertyValue($property)->getUri() ,__METHOD__);
        }
        $returnValue = $this->expression;
        // section 10-13-1--99--2ca656b4:11c9ebb5ddd:-8000:0000000000000ED7 end

        return $returnValue;
    }

} /* end of class core_kernel_rules_Rule */

?>
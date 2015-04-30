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
 * Short description of class core_kernel_rules_Rule
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis
 
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

        
		common_Logger::i('Evaluating rule '.$this->getLabel().'('.$this->getUri().')', array('Generis Rule'));
         if(empty($this->expression)){
         	$property = new core_kernel_classes_Property(PROPERTY_RULE_IF);
         	$this->expression = new core_kernel_rules_Expression($this->getUniquePropertyValue($property)->getUri() ,__METHOD__);
        }
        $returnValue = $this->expression;
        

        return $returnValue;
    }

} 
<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\rules;

use qtism\data\QtiComponentCollection;
use qtism\data\expressions\Expression;
use qtism\data\QtiComponent;

/**
 * A TemplateRule aiming at expressing constraints to Cloning Engines.
 * 
 * From IMS QTI:
 * 
 * By using a templateConstraint, authors can ensure that the values of variables set during 
 * templateProcessing satisfy the condition specified by the boolean expression. For example, 
 * two randomly selected numbers might be required which have no common factors.
 * 
 * A templateConstraint may occur anywhere as a child of templateProcessing. It may not be 
 * used as a child of any other element. Any number of templateConstraints may be used, though 
 * two or more consecutive templateConstraints could be combined using the 'and' element to 
 * combine their boolean expressions.
 * 
 * The maximum number of times that the operations preceding the templateConstraint can be expected 
 * to be performed is assumed to be 100; implementations may permit more iterations, but there must 
 * be a finite maximum number of iterations. This prevents the occurrence of an endless loop. 
 * It is the responsibility of the author to provide default values for any variables assigned 
 * under a templateConstraint.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemplateConstraint extends QtiComponent implements TemplateRule {
    
    /**
     * From IMS QTI:
     * 
     * A templateConstraint contains an expression which must have an effective baseType of 
     * boolean and single cardinality. For more information about the runtime data model employed
     * see Expressions. If the expression is false (including if the expression is NULL), the 
     * template variables are set to their default values and templateProcessing is restarted; 
     * this happens repeatedly until the expression is true or the maximum number of iterations 
     * is reached. In the event that the maximum number of iterations is reached, any default 
     * values provided for the variables during declaration are used. Processing then continues 
     * with the next templateRule after the templateConstraint, or finishes if there are no 
     * further templateRules.
     * 
     * @var Expression
     * @qtism-bean-property
     */
    private $expression;
    
    /**
     * Create a new TemplateConstraint object.
     * 
     * @param Expression $expression An Expression object defining the constraint to be applied.
     */
    public function __construct(Expression $expression) {
        $this->setExpression($expression);
    }
    
    /**
     * Set the expresssion defining the constraint.
     * 
     * @param Expression $expression An Expression object.
     */
    public function setExpression(Expression $expression) {
        $this->expression = $expression;
    }
    
    /**
     * Get the expression defining the constraint.
     * 
     * @return Expression An Expression object.
     */
    public function getExpression() {
        return $this->expression;
    }
    
    public function getQtiClassName() {
        return 'templateConstraint';
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array($this->getExpression()));
    }
}
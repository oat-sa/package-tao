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
use qtism\common\utils\Format;
use qtism\data\expressions\Expression;
use qtism\data\QtiComponent;
use \InvalidArgumentException;

/**
 * A template rule aiming at setting the value of a given template variable.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SetTemplateValue extends QtiComponent implements TemplateRule {
    
    /**
     * From IMS QTI:
     * 
     * The template variable to be set.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * From IMS QTI:
     * 
     * An expression which must have an effective baseType and cardinality that 
     * matches the base-type and cardinality of the template variable being set.
     * 
     * The setTemplateValue rule sets the value of a template variable to the value 
     * obtained from the associated expression. A template variable can be updated 
     * with reference to a previously assigned value, in other words, the template 
     * variable being set may appear in the expression where it takes the value 
     * previously assigned to it.
     * 
     * @var Expression
     * @qtism-bean-property
     */
    private $expression;
    
    /**
     * Create a new SetTemplateValue object.
     * 
     * @param string $identifier The identifier of the template variable to be set.
     * @param Expression $expression The expression that depicts the way to compute the value to be set to the template variable.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function __construct($identifier, Expression $expression) {
        $this->setIdentifier($identifier);
        $this->setExpression($expression);
    }
    
    /**
     * Set the identifier of the template variable to be set.
     * 
     * @param string $identifier A valid QTI identifier.
     */
    public function setIdentifier($identifier) {
        if (Format::isIdentifier($identifier, false) === true) {
            $this->identifier = $identifier;
        }
        else {
            $msg = "The 'identifier' argument must be a valid QTI identifier, '" . $identifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the identifier of the template variable to be set.
     * 
     * @return string A QTI identifier.
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Set the expression that provides the value to be set to the template variable.
     * 
     * @param Expression $expression An Expression object.
     */
    public function setExpression(Expression $expression) {
        $this->expression = $expression;
    }
    
    /**
     * Get the expression that provides the value to be set to the template variable.
     * 
     * @return Expression An expression object.
     */
    public function getExpression() {
        return $this->expression;
    }
    
    public function getQtiClassName() {
        return 'setTemplateValue';
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array($this->getExpression()));
    }
}
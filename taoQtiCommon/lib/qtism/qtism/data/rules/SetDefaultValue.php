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
 * A rule aiming at setting a given response variable or outcome variable
 * a default value.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SetDefaultValue extends QtiComponent implements TemplateRule {
    
    /**
     * From IMS QTI:
     * 
     * The response variable or outcome variable to have its default value set.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * The expression to be executed to get the default value to be set.
     * 
     * @var Expression
     * @qtism-bean-property
     */
    private $expression;
    
    /**
     * Create a new SetDefaultValue object.
     * 
     * @param string $identifier The identifier of the response variable or outcome variable to have its default value set.
     * @param Expression $expression An expression to be executed to get the value to assign to the variable.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function __construct($identifier, Expression $expression) {
        $this->setIdentifier($identifier);
        $this->setExpression($expression);
    }
    
    /**
     * Set the identifier of the response or outcome variable to have its
     * default value set.
     * 
     * @param string $identifier A valid QTI identifier.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function setIdentifier($identifier) {
        if (Format::isIdentifier($identifier, false) === true) {
            $this->identifier = $identifier;
        }
        else {
            $msg = "The value of the 'identifier' argument must be a valid QTI identifier, '" . $identifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the identifier of the response or outcome variable to have its default
     * value set.
     * 
     * @return string A QTI identifier.
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Get the expression to be executed to get the default value to be set.
     * 
     * @param Expression $expression An Expression object.
     */
    public function setExpression(Expression $expression) {
        $this->expression = $expression;
    }
    
    /**
     * Get the expression to be executed to get the default value to be set.
     * 
     * @return Expression An Expression object.
     */
    public function getExpression() {
        return $this->expression;
    }
    
    public function getQtiClassName() {
        return 'setDefaultValue';
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array($this->getExpression()));
    }
}
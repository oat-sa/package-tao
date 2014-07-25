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
 * A template rule aiming at setting a correct response value to a given
 * variable.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SetCorrectResponse extends QtiComponent implements TemplateRule {
    
    /**
     * The identifier of the response variable to have its correct value set.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * The expression to be executed to get the value to be set as the correct response
     * to a given variable.
     * 
     * @var Expression
     * @qtism-bean-property
     */
    private $expression;
    
    /**
     * Create a new SetCorrectResponse object.
     * 
     * @param string $identifier The identifier of the variable to have its correct value set.
     * @param Expression $expression An expression to be executed to get the value to be set as the correct response value.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function __construct($identifier, Expression $expression) {
        $this->setIdentifier($identifier);
        $this->setExpression($expression);
    }
    
    /**
     * Set the identifier of the response variable to have its correct value set.
     * 
     * @param string $identifier A valid QTI identifier.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
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
     * Get the identifier of the response variable to have its correct value set.
     * 
     * @return string A QTI identifier.
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Set the expression to be executed to get the value to be set as the correct response
     * to a given variable.
     * 
     * @param Expression $expression An Expression object.
     */
    public function setExpression(Expression $expression) {
        $this->expression = $expression;
    }
    
    /**
     * Get the expression to be executed to get the value to be set as the correct response
     * to a given variable.
     * 
     * @return Expression An Expression object.
     */
    public function getExpression() {
        return $this->expression;
    }
    
    public function getQtiClassName() {
        return 'setCorrectResponse';
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array($this->getExpression()));
    }
}
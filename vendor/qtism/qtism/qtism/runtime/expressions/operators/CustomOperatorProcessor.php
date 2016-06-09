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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */
namespace qtism\runtime\expressions\operators;

use qtism\data\expressions\operators\CustomOperator;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The base class for all Custom Operator implementations.
 * 
 * From IMS QTI:
 * 
 * The custom operator provides an extension mechanism for defining operations not currently
 * supported by this specification.
 * 
 * The class attribute allows simple sub-classes to be named. The definition of a sub-class 
 * is tool specific and may be inferred from toolName and toolVersion.
 * 
 * A URI that identifies the definition of the custom operator in the global namespace.
 * 
 * In addition to the class and definition attributes, sub-classes may add any number of 
 * attributes of their own.
 * 
 * Custom operators can take any number of sub-expressions of any type to be treated as parameters.
 * 
 * It has been suggested that customOperator might be used to help link processing rules defined by
 * this specification to instances of web-service based processing engines. For example, a web-service
 * which offered automated marking of free text responses. Implementors experimenting with this approach
 * are encouraged to share information about their solutions to help determine the best way to achieve
 * this type of processing.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class CustomOperatorProcessor extends OperatorProcessor {
    
    /**
     * Create a new CustomOperatorProcessor object.
     * 
     * @param Expression $expression The CustomOperator object to be processed.
     * @param OperandsCollection $operands A collection of operands to be used as parameters for the CustomOperator implementation.
     */
    public function __construct(Expression $expression, OperandsCollection $operands) {
        parent::__construct($expression, $operands);
    }
    
    public function setExpression(Expression $expression) {
        if ($expression instanceof CustomOperator) {
            parent::setExpression($expression);
        }
        else {
            $msg = "The CustomOperatorProcessor can only process CustomOperator objects.";
            throw new InvalidArgumentException($msg);
        }
    }
}
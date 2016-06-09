<?php

use qtism\common\enums\BaseType;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Identifier;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\NullValue;
use qtism\data\expressions\operators\CustomOperator;
use qtism\data\expressions\operators\Multiple;
use qtism\data\expressions\ExpressionCollection;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qti\customOperators\math\graph\CountPointsThatSatisfyEquation;

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
 * Copyright (c) 2013-2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

 
/**
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class CountPointsThatSatisfyEquationTest extends PHPUnit_Framework_TestCase {
	
    public function testSimpleOne() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::POINT, new Point(0, 0)),
                    new BaseValue(BaseType::POINT, new Point(1, 1)),
                    new BaseValue(BaseType::POINT, new Point(2, 4)),
                    new BaseValue(BaseType::POINT, new Point(3, 9)),
                    new BaseValue(BaseType::POINT, new Point(4, 16)),
                    new BaseValue(BaseType::POINT, new Point(5, 25)),
                    new BaseValue(BaseType::POINT, new Point(6, 36)),
                    new BaseValue(BaseType::POINT, new Point(7, 49)),
                )
            )
        );
        $equation = new BaseValue(BaseType::STRING, 'x ^ 2');
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="point">0 0</baseValue><baseValue baseType="point">1 1</baseValue><baseValue baseType="point">2 4</baseValue><baseValue baseType="point">3 9</baseValue><baseValue baseType="point">4 16</baseValue><baseValue baseType="point">5 25</baseValue><baseValue baseType="point">6 36</baseValue><baseValue baseType="point">7 49</baseValue></multiple><baseValue baseType="string">y = x ^ 2</baseValue></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::POINT,
                    array(
                        new Point(0, 0),
                        new Point(1, 1),
                        new Point(2, 4),
                        new Point(3, 9),
                        new Point(4, 16),
                        new Point(5, 25),
                        new Point(6, 36),
                        new Point(7, 49)
                    )
                ),
                new String('y = x ^ 2')
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertEquals(8, $result->getValue());
    }
    
    public function testSimpleOneWithStrings() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::STRING, '0 0'),
                    new BaseValue(BaseType::STRING, '1 1'),
                    new BaseValue(BaseType::STRING, '2 4'),
                    new BaseValue(BaseType::STRING, '3 9'),
                    new BaseValue(BaseType::STRING, '4 16'),
                    new BaseValue(BaseType::STRING, '5 25'),
                    new BaseValue(BaseType::STRING, '6 36'),
                    new BaseValue(BaseType::STRING, '7 49'),
                )
            )
        );
        $equation = new BaseValue(BaseType::STRING, 'x ^ 2');
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="string">0 0</baseValue><baseValue baseType="string">1 1</baseValue><baseValue baseType="string">2 4</baseValue><baseValue baseType="string">3 9</baseValue><baseValue baseType="string">4 16</baseValue><baseValue baseType="string">5 25</baseValue><baseValue baseType="string">6 36</baseValue><baseValue baseType="string">7 49</baseValue></multiple><baseValue baseType="string">y = x ^ 2</baseValue></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::STRING,
                    array(
                        new String('0 0'),
                        new String('1 1'),
                        new String('2 4'),
                        new String('3 9'),
                        new String('4 16'),
                        new String('5 25'),
                        new String('6 36'),
                        new String('7 49')
                    )
                ),
                new String('y = x ^ 2')
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertEquals(8, $result->getValue());
    }
    
    public function testSimpleTwo() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::POINT, new Point(0, 0)),
                    new BaseValue(BaseType::POINT, new Point(-1, 1)),
                    new BaseValue(BaseType::POINT, new Point(2, 4)),
                    new BaseValue(BaseType::POINT, new Point(3, 9)),
                    new BaseValue(BaseType::POINT, new Point(4, 16)),
                    new BaseValue(BaseType::POINT, new Point(5, 25)),
                    new BaseValue(BaseType::POINT, new Point(14, 35)),
                    new BaseValue(BaseType::POINT, new Point(-5, 49)),
                )
            )
        );
        $equation = new BaseValue(BaseType::STRING, 'x ^ 2');
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="point">0 0</baseValue><baseValue baseType="point">-1 1</baseValue><baseValue baseType="point">2 4</baseValue><baseValue baseType="point">3 9</baseValue><baseValue baseType="point">4 16</baseValue><baseValue baseType="point">5 25</baseValue><baseValue baseType="point">14 35</baseValue><baseValue baseType="point">-5 49</baseValue></multiple><baseValue baseType="string">y = x ^ 2</baseValue></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::POINT,
                    array(
                        new Point(0, 0),
                        new Point(-1, 1),
                        new Point(2, 4),
                        new Point(3, 9),
                        new Point(4, 16),
                        new Point(5, 25),
                        new Point(14, 35),
                        new Point(-5, 49)
                    )
                ),
                new String('y = x ^ 2')
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertEquals(6, $result->getValue());
    }
    
    public function testInvalidEquation() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::POINT, new Point(0, 0)),
                    new BaseValue(BaseType::POINT, new Point(-1, 1)),
                    new BaseValue(BaseType::POINT, new Point(2, 4)),
                    new BaseValue(BaseType::POINT, new Point(3, 9)),
                    new BaseValue(BaseType::POINT, new Point(4, 16)),
                    new BaseValue(BaseType::POINT, new Point(5, 25)),
                    new BaseValue(BaseType::POINT, new Point(14, 35)),
                    new BaseValue(BaseType::POINT, new Point(-5, 49)),
                )
            )
        );
        $equation = new BaseValue(BaseType::STRING, 'x ^ 2');
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="point">0 0</baseValue><baseValue baseType="point">-1 1</baseValue><baseValue baseType="point">2 4</baseValue><baseValue baseType="point">3 9</baseValue><baseValue baseType="point">4 16</baseValue><baseValue baseType="point">5 25</baseValue><baseValue baseType="point">14 35</baseValue><baseValue baseType="point">-5 49</baseValue></multiple><baseValue baseType="string">y = x ^^^^^^ 4 \ vli 2</baseValue></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::POINT,
                    array(
                        new Point(0, 0),
                        new Point(-1, 1),
                        new Point(2, 4),
                        new Point(3, 9),
                        new Point(4, 16),
                        new Point(5, 25),
                        new Point(14, 35),
                        new Point(-5, 49)
                    )
                ),
                new String('y = x ^^^^^^ 4 \ vli 2')
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertNull($result);
    }
    
    public function testWrongEquationType() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::POINT, new Point(0, 0))
                )
            )
        );
        $equation = new BaseValue(BaseType::INTEGER, 3);
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="point">0 0</baseValue></multiple><baseValue baseType="integer">3</baseValue></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::POINT,
                    array(
                        new Point(0, 0)
                    )
                ),
                new Integer(3)
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertNull($result);
    }
    
    public function testNullEquation() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::POINT, new Point(0, 0))
                )
            )
        );
        $equation = new NullValue();
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="point">0 0</baseValue></multiple></null></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::POINT,
                    array(
                        new Point(0, 0)
                    )
                ),
                null
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertNull($result);
    }
    
    public function testWrongPointsType() {
        
        // --- Build Custom Operator PHP Expression Model.
        $points = new Multiple(
            new ExpressionCollection(
                array(
                    new BaseValue(BaseType::IDENTIFIER, '0 0')
                )
            )
        );
        $equation = new BaseValue(BaseType::STRING, 'x = y');
        
        $customOperator = new CustomOperator(
            new ExpressionCollection(
                array(
                    $points,
                    $equation
                )
            ),
            '<customOperator class="qti.customOperators.math.graph.CountPointsThatSatisfyEquation"><multiple><baseValue baseType="point">0 0</baseValue></multiple><baseValue baseType="string">x = y</baseValue></customOperator>'
        );
        
        // --- Build Runtime Operands for PHP Runtime Model.
        $operands = new OperandsCollection(
            array(
                new MultipleContainer(
                    BaseType::IDENTIFIER,
                    array(
                        new Identifier('0 0')
                    )
                ),
                new String('x = y')
            )
        );
        $operator = new CountPointsThatSatisfyEquation($customOperator, $operands);
        $result = $operator->process();
        
        $this->assertNull($result);
    }
}

<?php

namespace qti\customOperators\math\graph;

use qtism\common\enums\BaseType;
use qtism\common\datatypes\Integer as QtismInteger;
use qtism\common\datatypes\String as QtismString;
use qtism\common\datatypes\Point;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;

class CountPointsThatSatisfyEquation extends CustomOperatorProcessor
{
    public function process() 
    {
        $returnValue = new QtismInteger(0);
        $operands = $this->getOperands();
        
        if (count($operands) >= 2) {
            $points = $operands[0];
            $equation = $operands[1];
            
            if (($points instanceof MultipleContainer || $points instanceof OrderedContainer) && ($points->getBaseType() === BaseType::POINT || $points->getBaseType() === BaseType::STRING) && $equation instanceof QtismString) {
                // Check every Point X,Y against the equation...
                $math = new \oat\beeme\Parser();
                $math->setConstant('#pi', M_PI);
                
                try {
                    foreach ($points as $point) {
                        
                        if ($point instanceof Point) {
                            $x = floatval($point->getX());
                            $y = floatval($point->getY());
                        } else {
                            $strs = explode("\x20", $point->getValue());
                            if (count($strs) !== 2) {
                                // Parsing error, the NULL value is returned.
                                return null;
                            } else {
                                $x = floatval($strs[0]);
                                $y = floatval($strs[1]);
                            }
                        }
                        
                        $result = $math->evaluate(
                            $equation->getValue(),
                            array(
                                'x' => $x,
                                'y' => $y
                            )
                        );
                        
                        if ($result === true) {
                            // The Point X,Y satisfies the equation...
                            $returnValue->setValue($returnValue->getValue() + 1);
                        }
                    }
                } catch (\Exception $e) {
                    // If an error occurs e.g. invalid expression, the NULL value is returned.
                    return null;
                }
            } else {
                // Not supported operands, return the NULL value.
                return null;
            }
        }
        
        return $returnValue;
    }
}

<?php

namespace oat\beeme\TranslationStrategy;

use InvalidArgumentException;
use oat\beeme\Token;
use oat\beeme\Operator;
use SplQueue;
use SplStack;

/**
 * Implementation of Shunting-Yard algorithm.
 * Used to translate infix mathematical expressions
 * to RPN mathematical expressions.
 *
 * @see http://en.wikipedia.org/wiki/Shunting-yard_algorithm
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 */
class ShuntingYard implements TranslationStrategyInterface
{
    /**
     * Operator stack
     *
     * @var \SplStack 
     */
    private $operatorStack;
    
    /**
     * Output queue
     *
     * @var \SplQueue 
     */
    private $outputQueue;

    /**
     * Translate array sequence of tokens from infix to 
     * Reverse Polish notation (RPN) which representing mathematical expression.
     * 
     * @param array $tokens Collection of Token intances
     * @return array Collection of Token intances
     * @throws InvalidArgumentException
     */
    public function translate(array $tokens)
    {
        $this->operatorStack = new SplStack();
        $this->outputQueue = new SplQueue();
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];
            
            switch ($token->getType()) {
                case Token::T_OPERAND:
                    $this->outputQueue->enqueue($token);
                    break;
                case Token::T_OPERATOR:
                    $o1 = $token;
                    $isUnary = $this->isPreviousTokenOperator($tokens, $i) || $this->isPreviousTokenLeftParenthesis($tokens, $i) || $this->hasPreviousToken($i) === false;
                    
                    if ($isUnary) {
                        if ($o1->getValue() === '-' || $o1->getValue() === '+') {
                            $o1 = new Operator($o1->getValue() . 'u', 3, Operator::O_NONE_ASSOCIATIVE);
                        } else {
                            throw new \InvalidArgumentException("Syntax error: operator '" . $o1->getValue() . "' cannot be used as a unary operator or with an operator as an operand.");
                        }
                    } else {
                        while($this->hasOperatorInStack() && ($o2 = $this->operatorStack->top()) && $o1->hasLowerPriority($o2)) {
                            $this->outputQueue->enqueue($this->operatorStack->pop());
                        }
                    }
                    
                    $this->operatorStack->push($o1);
                    break;
                case Token::T_LEFT_BRACKET:
                    $this->operatorStack->push($token);
                    break;
                case Token::T_RIGHT_BRACKET:
                    while((!$this->operatorStack->isEmpty()) && (Token::T_LEFT_BRACKET != $this->operatorStack->top()->getType())) {
                        $this->outputQueue->enqueue($this->operatorStack->pop());
                    }
                    if($this->operatorStack->isEmpty()) {
                        throw new InvalidArgumentException(sprintf('Syntax error: mismatched parentheses.'));
                    }
                    $this->operatorStack->pop();
                    break;
                default:
                    throw new InvalidArgumentException(sprintf('Invalid token detected: %s.', $token));
                    break;
            }
        }
        while($this->hasOperatorInStack()) {
            $this->outputQueue->enqueue($this->operatorStack->pop());
        }

        if(!$this->operatorStack->isEmpty()) {
            throw new InvalidArgumentException(sprintf('Syntax error: mismatched parentheses or misplaced number.'));
        }

        return iterator_to_array($this->outputQueue);
    }

    /**
     * Determine if there is operator token in operator stack
     * 
     * @return boolean
     */
    private function hasOperatorInStack()
    {
        $hasOperatorInStack = false;
        if(!$this->operatorStack->isEmpty()) {
            $top = $this->operatorStack->top();
            if(Token::T_OPERATOR == $top->getType()) {
                $hasOperatorInStack = true;
            }
        }

        return $hasOperatorInStack;
    }
    
    private function isPreviousTokenOperator(array $tokens, $index)
    {
        if ($this->hasPreviousToken($index) === true) {
            return $tokens[$index - 1] instanceOf Operator;
        } else {
            return false;
        }
    }
    
    private function isPreviousTokenLeftParenthesis(array $tokens, $index)
    {
        if ($this->hasPreviousToken($index) === true) {
            return $tokens[$index - 1]->getValue() === '(';
        } else {
            return false;
        }
    }
    
    private function hasPreviousToken($index)
    {
        return $index > 0;
    }
}

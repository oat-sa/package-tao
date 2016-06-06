<?php

namespace oat\beeme;

/**
 * Math Parser.
 * 
 * A Math Parser enabling mathematical expressions to be parsed. It supports
 * the -, +, *, / (including unary - and +), provides pi and e math constants,
 * but also a variable substitution mechanism. Finally, it enables comparisons,
 * using the = operator, returning boolean values for equation solving.
 *
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class Parser
{
    /**
     * Lexer wich should tokenize the mathematical expression.
     *
     * @var Lexer
     */
    protected $lexer;

    /**
     * TranslationStrategy from infix to postfix (a.k.a. postfix).
     * 
     * Translation strategy that should translate from infix
     * mathematical expression notation to reverse-polish 
     * mathematical expression notation.
     *
     * @var TranslationStrategy\TranslationStrategyInterface
     */
    protected $translationStrategy;
    
    /**
     * Array of key => value options.
     *
     * @var array 
     */
    private $options = array(
        'translationStrategy' => '\oat\beeme\TranslationStrategy\ShuntingYard',
    );
    
    /**
     * List of predefined constants.
     * 
     * Array of key => value predefined constants. The keys
     * are the constant names and values are their actual values.
     * 
     * @var array
     */
    private $predefinedConstants = array(
        'pi' => M_PI,
        'e'=> M_E
    );

    /**
     * Create new Lexer wich can evaluate mathematical expression.
     * Accept array of configuration options, currently supports only 
     * one option "translationStrategy" => "Fully\Qualified\Classname".
     * Class represent by this options is responsible for translation
     * from infix mathematical expression notation to reverse-polish
     * mathematical expression notation.
     * 
     * <code>
     *  $options = array(
     *      'translationStrategy' => '\oat\beeme\TranslationStrategy\ShuntingYard'
     *  );
     * </code>
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->lexer = new Lexer();
        $this->options = array_merge($this->options, $options);
        $this->translationStrategy = new $this->options['translationStrategy']();
    }

    /**
     * Evaluate string representing mathematical expression.
     * 
     * @param string $expression An expression to be evaluated.
     * @param array $variables
     * @return float
     * @throws \InvalidArgumentException In case of invalid $expression.
     * @throws \RangeException In case of division by zero.
     */
    public function evaluate($expression, array $variables = array())
    {
        $variables = array_merge($this->predefinedConstants, $variables);
        $lexer = $this->getLexer();
        $tokens = $lexer->tokenize($expression);

        $translationStrategy = new \oat\beeme\TranslationStrategy\ShuntingYard();

        return $this->evaluateRPN($translationStrategy->translate($tokens), $variables);
    }
    
    /**
     * Register a global constant for the engine.
     * 
     * Register a global constant with a given $name and $value to the parser.
     * 
     * @param string $name The name of the constant to register.
     * @param float $value The value of the registered constant.
     */
    public function setConstant($name, $value)
    {
        $this->predefinedConstants[$name] = floatval($value);
    }

    /**
     * Evaluate array sequence of tokens in Reverse Polish notation (RPN)
     * representing mathematical expression.
     * 
     * @param array $expressionTokens
     * @param array $variables
     * @return float
     * @throws \InvalidArgumentException On syntaxic error.
     * @throws \RangeException On division by zero.
     */
    private function evaluateRPN(array $expressionTokens, array $variables = array())
    {
        $stack = new \SplStack();

        foreach ($expressionTokens as $token) {
            $tokenValue = $token->getValue();
            
            if ($token->getType() === Token::T_OPERAND) {
                if (is_numeric($tokenValue) === true) {
                    // Number.
                    $stack->push($tokenValue);
                } else {
                    // Variable to substitute.
                    if (isset($variables[$tokenValue]) === true) {
                        if (is_numeric($variables[$tokenValue]) === true) {
                            $stack->push(floatval($variables[$tokenValue]));
                        } else {
                            $nonNumericValue = $variables[$tokenValue];
                            throw new \InvalidArgumentException("Cannot substitute variable '${tokenValue}' with non-numeric value '${nonNumericValue}'.");
                        }
                    } else {
                        throw new \InvalidArgumentException("Cannot substitute variable '${tokenValue}'. No value provided.");
                    }
                }
            } else {
                switch ($tokenValue) {
                    case '+':
                        $stack->push($stack->pop() + $stack->pop());
                        break;
                    case '+u':
                        $stack->push($stack->pop() * 1.);
                        break;
                    case '-':
                        $n = $stack->pop();
                        $stack->push($stack->pop() - $n);
                        break;
                    case '-u':
                        $stack->push($stack->pop() * -1.);
                        break;
                    case '*':
                        $stack->push($stack->pop() * $stack->pop());
                        break;
                    case '/':
                        $n = $stack->pop();
                        
                        if ($n == 0) {
                            throw new \RangeException('Division by zero.');
                        }
                        
                        $stack->push($stack->pop() / $n);
                        break;
                    case '%':
                        $n = $stack->pop();
                        $stack->push($stack->pop() % $n);
                        break;
                    case '^':
                        $n = $stack->pop();
                        $stack->push(pow($stack->pop(), $n));
                        break;
                    case '=':
                        $n = $stack->pop();
                        $stack->push($stack->pop() == $n);
                        break;
                    default:
                        throw new \InvalidArgumentException(sprintf('Unexpected character: %s', $tokenValue));
                        break;
                }
            }
        }

        return $stack->top();
    }

    /**
     * Return lexer.
     * 
     * Returns the Lexer object used by the parser.
     * 
     * @return Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }
}

<?php

namespace oat\beeme;

/**
 * Tokenize mathematical expression.
 *
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class Lexer
{
    /**
     * Collection of Token instances
     * 
     * @var array
     */
    protected $tokens;

    /**
     * The mathematical expression that should be tokenized
     *
     * @var string
     */
    protected $code;
    
    /**
     * A buffer containing constant values while parsing.
     * 
     * @var string
     */
    protected $constantBuffer;
    
    /**
     * Mathematical operators map
     *
     * @var array
     */
    protected static $operatorsMap = array(
        '=' => array('priority' => 0, 'associativity' => Operator::O_LEFT_ASSOCIATIVE),
        '+' => array('priority' => 1, 'associativity' => Operator::O_LEFT_ASSOCIATIVE),
        '-' => array('priority' => 1, 'associativity' => Operator::O_LEFT_ASSOCIATIVE),
        '*' => array('priority' => 2, 'associativity' => Operator::O_LEFT_ASSOCIATIVE),
        '/' => array('priority' => 2, 'associativity' => Operator::O_LEFT_ASSOCIATIVE),
        '%' => array('priority' => 2, 'associativity' => Operator::O_LEFT_ASSOCIATIVE),
        '^' => array('priority' => 3, 'associativity' => Operator::O_RIGHT_ASSOCIATIVE)
    );

    public function __construct()
    {
        $this->tokens = array();
        $this->constantBuffer = '';
    }
    
    /**
     * Tokenize mathematical expression.
     * 
     * @param type $code
     * @return array Collection of Token instances
     * @throws \InvalidArgumentException
     */
    public function tokenize($code)
    {
        if (is_string($code) === false) {
            throw new \InvalidArgumentException('Cannot tokenize a non-string value.');
        }
        
        // Remove all white spaces from $code.
        $code = preg_replace('/\s+/', '', $code);
        
        if ($code === '') {
            throw new \InvalidArgumentException('Cannot tokenize empty string.');
        }
        
        $this->code = $code;
        $this->tokens = array();
        $this->constantBuffer = '';
        
        $availableOperators = array_keys(static::$operatorsMap);
        $constantBuffer = '';
        
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            
            if (in_array($char, $availableOperators) === true) {
                
                // If the constant buffer is not empty, there is a token to be built.
                $this->cleanConstantBuffer();
                
                // Deal with operator.
                $token = new Operator(
                    $char,
                    static::$operatorsMap[$char]['priority'],
                    static::$operatorsMap[$char]['associativity']
                );
                $this->tokens[] = $token;
            } elseif (LexingUtils::isCharPartOfNumber($char) === true || LexingUtils::isCharPartOfVariable($char)) {
                // Deal with constant content.
                $this->constantBuffer .= $char;
            } elseif ($char === '(') {
                // If the constant buffer is not empty, there is a token to be built.
                $this->cleanConstantBuffer();
                
                $token = new Token($char, Token::T_LEFT_BRACKET);
                $this->tokens[] = $token;
            } elseif ($char === ')') {
                // If the constant buffer is not empty, there is a token to be built.
                $this->cleanConstantBuffer();
                
                $token = new Token($char, Token::T_RIGHT_BRACKET);
                $this->tokens[] = $token;
            } else {
                throw new \InvalidArgumentException(sprintf("Syntax error: unexpected character '%s'.", $char));
            }
        }
        
        $this->cleanConstantBuffer();
        
        return $this->tokens;
    }
    
    /**
     * Clean the constant buffer.
     * 
     * Tries to create a constant (e.g. a number) token from the read buffer. An operand 
     * token will be created only if the current buffer is not empty. The created token
     * will be placed in the collection of previously created tokens.
     * 
     */
    private function cleanConstantBuffer()
    {
        if ($this->constantBuffer !== '') {
            $val = (is_numeric($this->constantBuffer)) ? floatval($this->constantBuffer) : $this->constantBuffer;
            
            $token = new Token($val, Token::T_OPERAND);
            $this->tokens[] = $token;
            $this->constantBuffer = '';
        }
    }
}

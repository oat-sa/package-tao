<?php

namespace oat\beeme;

/**
 * Value object representing one token of mathematical expression.
 *
 * @author Adrean Boyadzhiev (netforce) <adrean.boyadzhiev@gmail.com>
 */
class Token
{

    const T_OPERATOR = 1;
    const T_OPERAND = 2;
    const T_LEFT_BRACKET = 3;
    const T_RIGHT_BRACKET = 4;

    /**
     * String representation of this token
     *
     * @var string
     */
    protected $value;
    
    /**
     * Token type one of Token::T_* constants
     *
     * @var integer
     */
    protected $type;

    /**
     * Create new "Value object" which represent one token
     * 
     * @param integer|string $value
     * @param integer $type
     * @throws \InvalidArgumentException
     */
    public function __construct($value, $type)
    {
        $tokenTypes = array(
            self::T_OPERATOR,
            self::T_OPERAND,
            self::T_LEFT_BRACKET,
            self::T_RIGHT_BRACKET
        );
        if (!in_array($type, $tokenTypes, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid token type: %s', $type));
        }

        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Return token value
     * 
     * @return string|integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return token type
     *
     * return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return string representation of this token.
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

}

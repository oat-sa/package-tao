<?php

namespace oat\beeme;

/**
 * Lexing Utility Methods.
 * 
 * This class provides lexing utility methods.
 */
class LexingUtils
{
    
    /**
     * Is a character part of a number.
     * 
     * This method checks whether or not a given $char is composing a number.
     * For instance, is $char '9' a valid character to compose a number e.g. 9.4?
     * 
     * @param string $char
     * @return boolean
     */
    static public function isCharPartOfNumber($char)
    {
        return is_numeric($char) === true || $char === '.';
    }
    
    /**
     * Is a character par of a variable name.
     * 
     * This method checks whether or not a given $char is composing a variable name.
     * For instance, is $char 'f' a valid character to composer a variable name e.g. 'foo'.
     */
    static public function isCharPartOfVariable($char)
    {
        return preg_match('/[a-z#_]/ui', $char);
    }
}

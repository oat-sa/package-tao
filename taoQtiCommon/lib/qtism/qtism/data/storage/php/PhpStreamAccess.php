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

namespace qtism\data\storage\php;

use qtism\data\storage\php\Utils as PhpUtils;
use qtism\common\storage\StreamAccessException;
use qtism\common\storage\IStream;
use qtism\common\storage\AbstractStreamAccess;
use qtism\common\storage\StreamException;
use \InvalidArgumentException;

/**
 * 
 * The PhpStreamAccess class provides methods to write some
 * PHP Code into a given IStream object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpStreamAccess extends AbstractStreamAccess {
    
    /**
     * Creates a new PhpStreamAccess object.
     * 
     * @param IStream $stream The stream to write some PHP into.
     * @throws StreamAccessException If $stream is not open yet.
     */
    public function __construct(IStream $stream) {
        parent::__construct($stream);
    }
    
    /**
     * Write a scalar value into the current stream. 
     * 
     * If the given value is a string, it will be output surrounded by double quotes (") characters.
     * 
     * @param mixed $scalar A PHP scalar value or null.
     * @throws InvalidArgumentException If $scalar is not a PHP scalar value nor null.
     * @throws StreamAccessException If an error occurs while writing the scalar value.
     */
    public function writeScalar($scalar) {
        if (Utils::isScalar($scalar) === false) {            
            $msg = "A '" . gettype($scalar) . "' value is not a PHP scalar value nor null.";
            throw new InvalidArgumentException($msg);
        }
        
        try {
            if (is_int($scalar) === true) {
                $this->getStream()->write($scalar);
            }
            else if (is_double($scalar) === true) {
                if (strpos('' . $scalar, '.') === false) {
                    $scalar = $scalar . '.0';
                }
                
                $this->getStream()->write($scalar);
            }
            else if (is_string($scalar) === true) {
                $this->getStream()->write(PhpUtils::doubleQuotedPhpString($scalar));
            }
            else if (is_bool($scalar) === true) {
                $this->getStream()->write(($scalar === true) ? 'true' : 'false');
            }
            else if (is_null($scalar) === true) {
                $this->getStream()->write('null');
            }
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing the scalar value '${scalar}'.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write the PHP equality symbol into the current stream.
     * 
     * @param boolean $spaces Whether to surround the equality symbol with spaces.
     * @throws StreamAccessException If an error occurs while writing the equality symbol.
     */
    public function writeEquals($spaces = true) {
        try {
            if ($spaces === true) {
                $this->getStream()->write(' = ');
            }
            else {
                $this->getStream()->write('=');
            }    
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing the PHP equality symbol (=).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a newline escape sequence in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the equality symbol.
     */
    public function writeNewline() {
        try {
            $this->getStream()->write("\n");
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a newline escape sequence (\\n).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a PHP opening tag in the current stream.
     * 
     * @param boolean $newline Whether a newline escape sequence must be written after the opening tag.
     * @throws StreamAccessException If an error occurs while writing the opening tag.
     */
    public function writeOpeningTag($newline = true) {
        try {
            $this->getStream()->write('<?php');
            if ($newline === true) {
                $this->writeNewline();
            }    
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a PHP opening tag (<?php).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a PHP closing tag in the current string.
     * 
     * @param boolean $newline
     */
    public function writeClosingTag($newline = true) {
        try {
            if ($newline === true) {
                $this->writeNewline();
            }
            $this->getStream()->write('?>');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a PHP closing tag (?>).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a PHP semicolon (;) in the current stream.
     * 
     * @param boolean $newline Wether a newline escape sequence follows the semicolon.
     * @throws StreamAccessException If an error occurs while writing the semicolon;
     */
    public function writeSemicolon($newline = true) {
        try {
            $this->getStream()->write(';');
            if ($newline === true) {
                $this->writeNewline();
            }
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a semicolon (;).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a PHP colon (:) in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the colon.
     */
    public function writeColon() {
        try {
            $this->getStream()->write(':');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a colon (:).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a PHP scope resolution operator (::) in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the scope resolution operator.
     */
    public function writeScopeResolution() {
        try {
            $this->getStream()->write('::');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a scope resolution operator (::).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * An alias to PhpStreamAccess::writeScopeResolution ;-).
     * 
     * @see PhpStreamAccess::writeScopeResolution
     * @throws StreamAccessException If an error occurs while writing the "Paamayim Nekudotayim".
     */
    public function writePaamayimNekudotayim() {
        try {
            $this->writeScopeResolution();
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a Paamayim Nekudotayim.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write an opening parenthesis in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the opening parenthesis.
     */
    public function writeOpeningParenthesis() {
        try {
            $this->getStream()->write('(');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing an opening parenthesis (().";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a closing parenthesis in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the closing parenthesis.
     */
    public function writeClosingParenthesis() {
        try {
            $this->getStream()->write(')');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a closing parenthesis ()).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a comma in the current stream.
     * 
     * @param boolean $space Whether a white space must be written after the comma.
     * @throws StreamAccessException If an error occurs while writing the comma.
     */
    public function writeComma($space = true) {
        try {
            $this->getStream()->write(',');
            if ($space === true) {
                $this->writeSpace();
            }    
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a comma (,).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a white space in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the white space.
     */
    public function writeSpace() {
        try {
            $this->getStream()->write(' ');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a white space ( ).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a variable reference in the current stream.
     * 
     * @param string $varname The name of the variable reference to write.
     * @throws StreamAccessException If an error occurs while writing the variable reference.
     */
    public function writeVariable($varname) {
        try {
            $this->getStream()->write('$' . $varname);
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a variable reference.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a object operator (->) in the current stream.
     * 
     * @throws StreamAccessException If an error occurs while writing the object operator.
     */
    public function writeObjectOperator() {
        try {
            $this->getStream()->write('->');
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing an object operator (->).";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a function call in the current stream.
     * 
     * @param string $funcname The name of the function that has to be called.
     * @param PhpArgumentCollection $arguments A collection of PhpArgument objects representing the arguments to be given to the function call.
     * @throws StreamAccessException If an error occurs while writing the function call.
     */
    public function writeFunctionCall($funcname, PhpArgumentCollection $arguments = null) {
        
        try {
            $this->getStream()->write($funcname);
            $this->writeOpeningParenthesis();
            
            if (is_null($arguments) === false) {
                $this->writeArguments($arguments);
            }
            
            $this->writeClosingParenthesis();
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a function call.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a method call in the current stream.
     * 
     * @param string $objectname The name of the variable where the object on which you want to call the method is stored e.g. 'foobar'.
     * @param string $methodname The name of the method you want to call.
     * @param PhpArgumentCollection $arguments A collection of PhpArgument objects.
     * @param boolean $static Whether or not the call is static.
     * @throws StreamAccessException If an error occurs while writing the method call.
     */
    public function writeMethodCall($objectname, $methodname, PhpArgumentCollection $arguments = null, $static = false) {
        
        try {
            $this->writeVariable($objectname);
            
            if ($static === false) {
                $this->writeObjectOperator();
            }
            else {
                $this->writePaamayimNekudotayim();
            }
            
            $this->getStream()->write($methodname);
            $this->writeOpeningParenthesis();
            
            if (is_null($arguments) === false) {
                $this->writeArguments($arguments);
            }
            
            $this->writeClosingParenthesis();
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a method call.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write the new operator in the current stream.
     * 
     * @param boolean $space Whether to write an extra white space after the new operator.
     * @throws StreamAccessException If an error occurs while writing the new operator.
     */
    public function writeNew($space = true) {
        try {
            $this->getStream()->write('new');
            if ($space === true) {
                $this->writeSpace();
            }    
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a new operator.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write the instantiation of a given $classname with some $arguments.
     * 
     * @param string $classname The name of the class to be instantiated. Fully qualified class names are supported.
     * @param PhpArgumentCollection $arguments A collection of PhpArgument objects.
     */
    public function writeInstantiation($classname, PhpArgumentCollection $arguments = null) {
        
        try {
            $this->writeNew();
            $this->getStream()->write($classname);
            $this->writeOpeningParenthesis();
            
            if (is_null($arguments) === false) {
                $this->writeArguments($arguments);
            }
            
            $this->writeClosingParenthesis();
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing an object instantiation.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a sequence of arguments in the current stream.
     * 
     * @param PhpArgumentCollection $arguments A collection of PhpArgument objects.
     * @throws StreamAccessException If an error occurs while writing the sequence of arguments.
     */
    public function writeArguments(PhpArgumentCollection $arguments) {
        try {
            $argsCount = count($arguments);
            
            for ($i = 0; $i < $argsCount; $i++) {
            
                $this->writeArgument($arguments[$i]);
            
                if ($i < $argsCount - 1) {
                    $this->writeComma();
                }
            }    
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing a sequence of arguments.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
    
    /**
     * Write a PHP function/method argument in the current stream.
     * 
     * @param PhpArgument $argument A PhpArgument object.
     * @throws StreamAccessException If an error occurs while writing the PHP argument.
     */
    public function writeArgument(PhpArgument $argument) {
        try {
            $value = $argument->getValue();
            
            if ($argument->isVariableReference() === true) {
                $this->getStream()->write('$' . $value->getName());
            }
            else {
                $this->writeScalar($value);
            }
        }
        catch (StreamException $e) {
            $msg = "An error occured while writing an argument with value '${value}'.";
            throw new StreamAccessException($msg, $this, 0, $e);
        }
    }
}
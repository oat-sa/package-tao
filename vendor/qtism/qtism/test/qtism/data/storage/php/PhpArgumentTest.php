<?php

use qtism\data\storage\php\PhpVariable;

use qtism\data\storage\php\PhpArgument;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class PhpArgumentTest extends QtiSmTestCase {
	
    public function testPhpArgument() {
        
        // Test a variable reference.
        $arg = new PhpArgument(new PhpVariable('test'));
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpArgument', $arg);
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpVariable', $arg->getValue());
        $this->assertTrue($arg->isVariableReference());
        $this->assertFalse($arg->isScalar());
        
        // Test a null value (considered to be scalar in this context).
        $arg = new PhpArgument(null);
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpArgument', $arg);
        $this->assertSame(null, $arg->getValue());
        $this->assertFalse($arg->isVariableReference());
        $this->assertTrue($arg->isScalar());
        
        // Test a string value.
        $str = "Hello World!\nThis is me!";
        $arg = new PhpArgument($str);
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpArgument', $arg);
        $this->assertEquals($str, $arg->getValue());
        $this->assertFalse($arg->isVariableReference());
        $this->assertTrue($arg->isScalar());
        
        // Test a boolean value.
        $arg = new PhpArgument(false);
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpArgument', $arg);
        $this->assertFalse($arg->getValue());
        $this->assertFalse($arg->isVariableReference());
        $this->assertTrue($arg->isScalar());
        
        // Test an integer value.
        $arg = new PhpArgument(-23);
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpArgument', $arg);
        $this->assertEquals(-23, $arg->getValue());
        $this->assertFalse($arg->isVariableReference());
        $this->assertTrue($arg->isScalar());
        
        // Test a float value.
        $arg = new PhpArgument(-23.3);
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpArgument', $arg);
        $this->assertEquals(-23.3, $arg->getValue());
        $this->assertFalse($arg->isVariableReference());
        $this->assertTrue($arg->isScalar());
    }
    
    
    public function testObject() {
        $this->setExpectedException('\\InvalidArgumentException');
        $arg = new PhpArgument(new stdClass());
    }
}
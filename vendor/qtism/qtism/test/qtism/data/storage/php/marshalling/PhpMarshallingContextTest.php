<?php

use qtism\common\datatypes\Point;
use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Coords;
use qtism\common\storage\MemoryStream;
use qtism\data\storage\php\marshalling\PhpMarshallingContext;
use qtism\data\storage\php\PhpStreamAccess;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class PhpMarshallingContextTest extends QtiSmTestCase {
	
    /**
     * An open access to a PHP source code stream. 
     * 
     * @param PhpStreamAccess
     */
    private $streamAccess;
    
    protected function setStreamAccess(PhpStreamAccess $streamAccess) {
        $this->streamAccess = $streamAccess;
    }
    
    protected function getStreamAccess() {
        return $this->streamAccess;
    }
    
    public function setUp() {
        parent::setUp();
        
        $stream = new MemoryStream();
        $stream->open();
        $this->setStreamAccess(new PhpStreamAccess($stream));
    }
    
    public function tearDown() {
        parent::tearDown();
        
        $streamAccess = $this->getStreamAccess();
        unset($streamAccess);
    }
    
    public function testPhpMarshallingContext() {
        $ctx = new PhpMarshallingContext($this->getStreamAccess());
        $this->assertFalse($ctx->mustFormatOutput());
        
        $ctx->setFormatOutput(true);
        $this->assertTrue($ctx->mustFormatOutput());
        
        $ctx->pushOnVariableStack('foo');
        $this->assertEquals(array('foo'), $ctx->popFromVariableStack());
        
        $ctx->pushOnVariableStack(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $ctx->popFromVariableStack(2));
        
        $this->assertInstanceOf('qtism\\data\\storage\\php\\PhpStreamAccess', $ctx->getStreamAccess());
    }
    
    public function testPhpMarshallingTooLargeQuantity() {
        $ctx = new PhpMarshallingContext($this->getStreamAccess());
        $ctx->pushOnVariableStack(array('foo', 'bar', '2000'));
        
        try {
            $values = $ctx->popFromVariableStack(4);
            $this->assertFalse(true, "An exception must be thrown because the requested quantity is too large.");
        }
        catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testPhpMarshallingEmptyStack() {
        $ctx = new PhpMarshallingContext($this->getStreamAccess());
        
        try {
            $value = $ctx->popFromVariableStack();
            $this->assertFalse(true, "An exception must be thrown because the variable names stack is empty.");
        }
        catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testWrongQuantity() {
        $ctx = new PhpMarshallingContext($this->getStreamAccess());
        $ctx->pushOnVariableStack('foo');
        
        try {
            $value = $ctx->popFromVariableStack(0);
            $this->assertTrue(false, "An exception must be thrown because the 'quantity' argument must be >= 1");
        }
        catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testGenerateVariableName() {
        $ctx = new PhpMarshallingContext($this->getStreamAccess());
        
        $this->assertEquals('integer_0', $ctx->generateVariableName(0));
        $this->assertEquals('integer_1', $ctx->generateVariableName(-10));
        $this->assertEquals('nullvalue_0', $ctx->generateVariableName(null));
        $this->assertEquals('nullvalue_1', $ctx->generateVariableName(null));
        $this->assertEquals('nullvalue_2', $ctx->generateVariableName(null));
        $this->assertEquals('boolean_0', $ctx->generateVariableName(true));
        $this->assertEquals('boolean_1', $ctx->generateVariableName(false));
        $this->assertEquals('double_0', $ctx->generateVariableName(20.3));
        $this->assertEquals('double_1', $ctx->generateVariableName(0.0));
        $this->assertEquals('string_0', $ctx->generateVariableName('String!'));
        $this->assertEquals('string_1', $ctx->generateVariableName('String!'));
        $this->assertEquals('integer_2', $ctx->generateVariableName(1337));
        
        $this->assertEquals('coords_0', $ctx->generateVariableName(new Coords(Shape::CIRCLE, array(10, 10, 5))));
        $this->assertEquals('coords_1', $ctx->generateVariableName(new Coords(Shape::CIRCLE, array(10, 10, 3))));
        $this->assertEquals('point_0', $ctx->generateVariableName(new Point(0, 0)));
        $this->assertEquals('point_1', $ctx->generateVariableName(new Point(0, 1)));
        $this->assertEquals('coords_2', $ctx->generateVariableName(new Coords(Shape::CIRCLE, array(5, 5, 3))));
    }
}

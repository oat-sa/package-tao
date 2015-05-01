<?php

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\files\FileSystemFileManager;
use qtism\common\datatypes\files\FileSystemFile;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\IntOrIdentifier;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\MatchProcessor;

class MatchProcessorTest extends QtiSmTestCase {
	
	public function testScalar() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(10)));
		$processor = new MatchProcessor($expression, $operands);
		$this->assertTrue($processor->process()->getValue() === true);
		
		$operands = new OperandsCollection(array(new Integer(10), new Integer(11)));
		$processor->setOperands($operands);
		$this->assertFalse($processor->process()->getValue() === true);
	}
	
	public function testContainer() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(5), new Integer(4), new Integer(3), new Integer(2), new Integer(1)));
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(1), new Integer(2), new Integer(3), new Integer(4), new Integer(5)));
		$processor = new MatchProcessor($expression, $operands);
		
		$this->assertTrue($processor->process()->getValue() === true);
		
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(5), new Integer(4), new Integer(3), new Integer(2), new Integer(1)));
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(1), new Integer(6), new Integer(7), new Integer(8), new Integer(5)));
		$processor->setOperands($operands);
		$this->assertFalse($processor->process()->getValue() === true);
	}
	
	public function testFile() {
	    $fManager = new FileSystemFileManager();
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection();
	    
	    $file1 = $fManager->createFromData('Some text', 'text/plain');
	    $file2 = $fManager->createFromData('Some text', 'text/plain');
	    
	    $operands[] = $file1;
	    $operands[] = $file2;
	    $processor = new MatchProcessor($expression, $operands);
	    
	    $this->assertTrue($processor->process()->getValue());
	    $fManager->delete($file1);
	    $fManager->delete($file2);
	    
	    $operands->reset();
	    $file1 = $fManager->createFromData('Some text', 'text/plain');
	    $file2 = $fManager->createFromData('Other text', 'text/plain');
	    $operands[] = $file1;
	    $operands[] = $file2;

	    $this->assertFalse($processor->process()->getValue());
	    $fManager->delete($file1);
	    $fManager->delete($file2);
	}
	
	public function testWrongBaseType() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection();
	    $operands[] = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('txt1'), new Identifier('txt2')));
	    $operands[] = new MultipleContainer(BaseType::STRING, array(new String('txt1'), new String('txt2')));
	    $processor = new MatchProcessor($expression, $operands);
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
	    $processor->process();
	}
	
	public function testWrongBaseTypeCompliance() {
	    $expression = $this->createFakeExpression();
	    $operands = new OperandsCollection();
	    $operands[] = new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array(new IntOrIdentifier('txt1'), new IntOrIdentifier('txt2')));
	    $operands[] = new MultipleContainer(BaseType::STRING, array(new String('txt1'), new String('txt2')));
	    $processor = new MatchProcessor($expression, $operands);
	    
	    // Unfortunately, INT_OR_IDENTIFIER cannot be considered as compliant with STRING.
	    $this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
	    $processor->process();
	}
	
	public function testDifferentBaseTypesScalar() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new Integer(15);
		$operands[] = new String('String!');
		$processor = new MatchProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testDifferentBaseTypesContainer() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10), new Integer(20), new Integer(30), new Integer(40)));
		$operands[] = new MultipleContainer(BaseType::FLOAT, array(new Float(10.0), new Float(20.0), new Float(30.0), new Float(40.0)));
		$processor = new MatchProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testDifferentBaseTypesMixed() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new String('String!');
		$operands[] = new OrderedContainer(BaseType::FLOAT, array(new Float(10.0), new Float(20.0)));
		$processor = new MatchProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testDifferentCardinalitiesOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new String('String!');
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('String!')));
		$processor = new MatchProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testDifferentCardinalitiesTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('String!')));
		$operands[] = new MultipleContainer(BaseType::STRING, array(new String('String!')));
		$processor = new MatchProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testDifferentCardinalitiesThree() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new OrderedContainer(BaseType::STRING, array(new String('String!')));
		$operands[] = new RecordContainer(array('entry1' => new String('String!')));
		$processor = new MatchProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(15)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new MatchProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Integer(25), new Integer(25), new Integer(25)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new MatchProcessor($expression, $operands);
	}
	
	public function testNullScalar() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new Float(15.0), null));
		$processor = new MatchProcessor($expression, $operands);
		$this->assertSame(null, $processor->process());
	}
	
	public function testNullContainer() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::INTEGER, array(new Integer(10), new Integer(20)));
		$operands[] = new MultipleContainer(BaseType::INTEGER);
		$processor = new MatchProcessor($expression, $operands);
		$this->assertSame(null, $processor->process());
	}
	
	private function createFakeExpression() {
		return $this->createComponentFromXml('
			<match>
				<baseValue baseType="integer">10</baseValue>
				<baseValue baseType="integer">11</baseValue>
			</match>
		');
	}
}
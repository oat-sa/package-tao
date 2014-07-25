<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\SumProcessor;

class SumProcessorTest extends QtiSmTestCase {

	public function testSimple() {
		$sum = $this->createFakeSumComponent();
		
		$operands = new OperandsCollection(array(new Integer(1), new Integer(1)));
		$sumProcessor = new SumProcessor($sum, $operands);
		$result = $sumProcessor->process();
		
		$this->assertInstanceOf('qtism\\runtime\\common\\Processable', $sumProcessor);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(2, $result->getValue());
	}
	
	public function testNary() {
		$sum = $this->createFakeSumComponent();
		
		$operands = new OperandsCollection(array(new Integer(24), new Integer(-4), new Integer(0)));
		$sumProcessor = new SumProcessor($sum, $operands);
		$result = $sumProcessor->process();

		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(20, $result->getValue());
	}
	
	public function testComplex() {
		$sum = $this->createFakeSumComponent();
		
		$operands = new OperandsCollection(array(new Integer(-1), new Integer(1)));
		$operands[] = new MultipleContainer(BaseType::FLOAT, array(new Float(2.1), new Float(4.3)));
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(10), new Integer(15)));
		$sumProcessor = new SumProcessor($sum, $operands);
		$result = $sumProcessor->process();
		
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(31.4, $result->getValue());
	}
	
	public function testZero() {
	    $sum = $this->createFakeSumComponent();
	    
	    $operands = new OperandsCollection(array(new Integer(0), new Float(6.0)));
	    $sumProcessor = new SumProcessor($sum, $operands);
	    $result = $sumProcessor->process();
	    
	    $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
	    $this->assertEquals(6.0, $result->getValue());
	}
	
	public function testInvalidOperandsOne() {
		$sum = $this->createFakeSumComponent();
		
		$this->setExpectedException('\\RuntimeException');
		
		$operands = new OperandsCollection(array(new Boolean(true), new Integer(14), new Integer(10)));
		$sumProcessor = new SumProcessor($sum, $operands);
		$result = $sumProcessor->process();
	}
	
	public function testInvalidOperandsTwo() {
		$sum = $this->createFakeSumComponent();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false)));
		$sumProcessor = new SumProcessor($sum, $operands);
		
		$this->setExpectedException('\\RuntimeException');
		$result = $sumProcessor->process();
	}
	
	public function testNullInvolved() {
		$sum = $this->createFakeSumComponent();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(10), null));
		$sumProcessor = new SumProcessor($sum, $operands);
		$result = $sumProcessor->process();
		$this->assertTrue($result === null);
	}
	
	private function createFakeSumComponent() {
		$sum = $this->createComponentFromXml('
			<sum xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<baseValue baseType="integer">1</baseValue>
				<baseValue baseType="integer">3</baseValue>
			</sum>
		');
		
		return $sum;
	}
}
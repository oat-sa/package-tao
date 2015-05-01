<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\ProductProcessor;

class ProductProcessorTest extends QtiSmTestCase {

	public function testSimple() {
		$product = $this->createFakeProductComponent();
		
		$operands = new OperandsCollection(array(new Integer(1), new Integer(1)));
		$productProcessor = new ProductProcessor($product, $operands);
		$result = $productProcessor->process();
		
		$this->assertInstanceOf('qtism\\runtime\\common\\Processable', $productProcessor);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(1, $result->getValue());
	}
	
	public function testNary() {
		$product = $this->createFakeProductComponent();
		
		$operands = new OperandsCollection(array(new Integer(24), new Integer(-4), new Integer(1)));
		$productProcessor = new ProductProcessor($product, $operands);
		$result = $productProcessor->process();

		$this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $result);
		$this->assertEquals(-96, $result->getValue());
	}
	
	public function testComplex() {
		$product = $this->createFakeProductComponent();
		
		$operands = new OperandsCollection(array(new Integer(-1), new Integer(1)));
		$operands[] = new MultipleContainer(BaseType::FLOAT, array(new Float(2.1), new Float(4.3)));
		$operands[] = new OrderedContainer(BaseType::INTEGER, array(new Integer(10), new Integer(15)));
		$productProcessor = new ProductProcessor($product, $operands);
		$result = $productProcessor->process();
		
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(-1354.5, $result->getValue());
	}
	
	public function testInvalidOperandsOne() {
		$product = $this->createFakeProductComponent();
		
		$this->setExpectedException('\\RuntimeException');
		
		$operands = new OperandsCollection(array(new Boolean(true), new Integer(14), new Integer(10)));
		$productProcessor = new ProductProcessor($product, $operands);
		$result = $productProcessor->process();
	}
	
	public function testInvalidOperandsTwo() {
		$product = $this->createFakeProductComponent();
		$operands = new OperandsCollection();
		$operands[] = new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), new Boolean(false)));
		$productProcessor = new ProductProcessor($product, $operands);
		
		$this->setExpectedException('\\RuntimeException');
		$result = $productProcessor->process();
	}
	
	public function testNullInvolved() {
		$product = $this->createFakeProductComponent();
		$operands = new OperandsCollection(array(new Integer(10), new Integer(10), null));
		$productProcessor = new ProductProcessor($product, $operands);
		$result = $productProcessor->process();
		$this->assertTrue($result === null);
	}
	
	public function testNotEnoughOperands() {
		$product = $this->createFakeProductComponent();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$productProcessor = new ProductProcessor($product, $operands);
	}
	
	private function createFakeProductComponent() {
		return $this->createComponentFromXml('
			<product xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<baseValue baseType="integer">1</baseValue>
				<baseValue baseType="integer">3</baseValue>
			</product>
		');
	}
}
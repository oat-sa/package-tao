<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');
require_once (dirname(__FILE__) . '/custom/custom_operator_autoloader.php');

use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\runtime\expressions\operators\OperandsCollection;
use qtism\runtime\expressions\operators\OperatorProcessorFactory;
use qtism\data\expressions\operators\Operator;

class OperatorProcessorFactoryTest extends QtiSmTestCase {
	
    public function setUp() {
        parent::setUp();
        // register testing custom operators autoloader.
        spl_autoload_register('custom_operator_autoloader');
    }
    
    public function tearDown() {
        parent::tearDown();
        // unregister testing custom operators autoloader.
        spl_autoload_unregister('custom_operator_autoloader');
    }
    
	public function testCreateProcessor() {
		// get a fake sum expression.
		$expression = $this->createComponentFromXml(
			'<sum>
				<baseValue baseType="integer">2</baseValue>
				<baseValue baseType="integer">2</baseValue>
			</sum>'
		);
		
		$factory = new OperatorProcessorFactory();
		$operands = new OperandsCollection(array(new Integer(2), new Integer(2)));
		$processor = $factory->createProcessor($expression, $operands);
		$this->assertInstanceOf('qtism\\runtime\\expressions\\operators\\SumProcessor', $processor);
		$this->assertEquals('sum', $processor->getExpression()->getQtiClassName());
		$this->assertEquals(4, $processor->process()->getValue()); // x)
	}
	
	public function testInvalidOperatorClass() {
		$expression = $this->createComponentFromXml('<baseValue baseType="string">String!</baseValue>');
		$factory = new OperatorProcessorFactory();
		
		$this->setExpectedException('\\InvalidArgumentException');
		$processor = $factory->createProcessor($expression);
	}
	
	public function testCustomOperator() {
	    // Fake expression...
	    $expression = $this->createComponentFromXml(
	        '<customOperator xmlns:qtism="http://www.qtism.org/xsd/custom_operators/explode" class="org.qtism.test.Explode" qtism:delimiter="-">
	            <baseValue baseType="string">this-is-a-test</baseValue>
	        </customOperator>'
	    );
	    
	    $factory = new OperatorProcessorFactory();
	    $operands = new OperandsCollection(array(new String('this-is-a-test')));
	    $processor = $factory->createProcessor($expression, $operands);
	    $this->assertInstanceOf('org\\qtism\\test\\Explode', $processor);
	    $this->assertEquals('customOperator', $processor->getExpression()->getQtiClassName());
	    $this->assertTrue($processor->process()->equals(new OrderedContainer(BaseType::STRING, array(new String('this'), new String('is'), new String('a'), new String('test')))));
	}
}
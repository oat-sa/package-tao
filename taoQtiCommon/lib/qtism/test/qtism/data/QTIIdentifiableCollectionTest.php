<?php
require_once (dirname(__FILE__) . '/../../QtiSmTestCase.php');

use qtism\data\QtiIdentifiableCollection;
use qtism\data\state\Weight;
use qtism\data\state\WeightCollection;

class QtiIdentifiableCollectionTest extends QtiSmTestCase {
	
	public function testWithWeights() {
		
		$weight1 = new Weight('weight1', 1.0);
		$weight2 = new Weight('weight2', 1.1);
		$weight3 = new Weight('weight3', 1.2);
		$weights = new WeightCollection(array($weight1, $weight2, $weight3));
		
		$this->assertTrue($weights['weight1'] === $weight1);
		$this->assertTrue($weights['weight2'] === $weight2);
		$this->assertTrue($weights['weight3'] === $weight3);
		
		$this->assertTrue($weights['weightX'] === null);
		
		
		// Can I address the by identifier?
		$this->assertTrue($weights['weight2'] === $weight2);
		
		// What happens if I change the identifier of an object.
		// Is it adressable with the new identifier?
		$weight2->setIdentifier('weightX');
		$this->assertTrue($weights['weightX'] === $weight2);
		$this->assertFalse(isset($weights['weight2']));
	}
}
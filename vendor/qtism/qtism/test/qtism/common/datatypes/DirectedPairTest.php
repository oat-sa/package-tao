<?php

use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class DirectedPairTest extends QtiSmTestCase {

	public function testEquality() {
		$p1 = new DirectedPair('A', 'B');
		$p2 = new DirectedPair('A', 'B');
		$p3 = new DirectedPair('C', 'D');
		$p4 = new Pair('A', 'B');
		$p5 = new DirectedPair('D', 'C');
		
		$this->assertTrue($p1->equals($p2));
		$this->assertTrue($p2->equals($p1));
		$this->assertFalse($p1->equals($p3));
		$this->assertFalse($p3->equals($p1));
		$this->assertFalse($p3->equals(1337));
		$this->assertTrue($p3->equals($p3));
		$this->assertFalse($p1->equals($p4));
		$this->assertFalse($p3->equals($p5));
		
		$p7 = new DirectedPair('abc', 'def');
		$p8 = new DirectedPair('def', 'abc');
		$this->assertFalse($p7->equals($p8));
		$this->assertFalse($p8->equals($p7));
	}
}
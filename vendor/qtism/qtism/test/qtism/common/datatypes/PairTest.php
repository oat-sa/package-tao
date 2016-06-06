<?php

use qtism\common\datatypes\Pair;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class PairTest extends QtiSmTestCase {

	public function testEquality() {
		$p1 = new Pair('A', 'B');
		$p2 = new Pair('A', 'B');
		$p3 = new Pair('C', 'D');
		$p4 = new Pair('D', 'C');
		
		$this->assertTrue($p1->equals($p2));
		$this->assertTrue($p2->equals($p1));
		$this->assertFalse($p1->equals($p3));
		$this->assertFalse($p3->equals($p1));
		$this->assertFalse($p3->equals(1337));
		$this->assertTrue($p3->equals($p3));
		$this->assertTrue($p4->equals($p3));
	}
}
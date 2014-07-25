<?php
use qtism\common\datatypes\Duration;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class DurationTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider validDurationProvider
	 */
	public function testValidDurationCreation($intervalSpec) {
		$duration = new Duration($intervalSpec);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $duration);
	}
	
	/**
	 * @dataProvider invalidDurationProvider
	 */
	public function testInvalidDurationCreation($intervalSpec) {
		$this->setExpectedException('\\InvalidArgumentException');
		$duration = new Duration($intervalSpec);
	}
	
	public function testPositiveDuration() {
		$duration = new Duration('P3Y4DT6H8M'); // 2 years, 4 days, 6 hours, 8 minutes.
		$this->assertEquals(3, $duration->getYears());
		$this->assertEquals(4, $duration->getDays());
		$this->assertEquals(6, $duration->getHours());
		$this->assertEquals(8, $duration->getMinutes());
		$this->assertEquals(0, $duration->getMonths());
		$this->assertEquals(0, $duration->getSeconds());
	}
	
	public function testEquality() {
		$d1 = new Duration('P1DT12H'); // 1 day + 12 hours.
		$d2 = new Duration('P1DT12H');
		$d3 = new Duration('PT3600S'); // 3600 seconds.
		
		$this->assertTrue($d1->equals($d2));
		$this->assertTrue($d2->equals($d1));
		$this->assertFalse($d1->equals($d3));
		$this->assertFalse($d3->equals($d1));
		$this->assertTrue($d3->equals($d3));
	}
	
	public function testNegativeDuration() {
		$duration = new Duration('P2Y4DT6H8M'); // - 2 years, 4 days, 6 hours, 8 minutes.
	}
	
	public function testClone() {
		$d = new Duration('P1DT12H'); // 1 day + 12 hours.
		$c = clone $d;
		$this->assertFalse($c === $d);
		$this->assertTrue($c->equals($d));
		$this->assertEquals($d->getDays(), $c->getDays());
		$this->assertEquals($d->getHours(), $c->getHours());
		$this->assertEquals($d->getMinutes(), $c->getMinutes());
		$this->assertEquals($d->getSeconds(), $c->getSeconds());
		$this->assertEquals($d->getMonths(), $c->getMonths());
		$this->assertEquals($d->getYears(), $c->getYears());
	}
	
	/**
	 * @dataProvider toStringProvider
	 * 
	 * @param Duration $duration
	 * @param string $expected
	 */
	public function testToString(Duration $duration, $expected) {
		$this->assertEquals($duration->__toString(), $expected);
	}
	
	public function testAdd() {
		$d1 = new Duration('PT1S');
		$d2 = new Duration('PT1S');
		$d1->add($d2);
		$this->assertEquals('PT2S', $d1->__toString());
		
		$d1 = new Duration('PT23H59M59S');
		$d2 = new Duration('PT10S');
		$d1->add($d2);
		$this->assertEquals('P1DT9S', $d1->__toString());
	}
	
	public function testSub() {
	    $d1 = new Duration('PT2S');
	    $d2 = new Duration('PT1S');
	    $d1->sub($d2);
	    $this->assertEquals('PT1S', $d1->__toString());
	    
	    $d1 = new Duration('PT2S');
	    $d2 = new Duration('PT4S');
	    $d1->sub($d2);
	    $this->assertEquals('PT0S', $d1->__toString());
	    
	    $d1 = new Duration('P1DT2H25M30S');
	    $d2 = new Duration('P1DT2H');
	    $d1->sub($d2);
	    $this->assertEquals('PT25M30S', $d1->__toString());
	    
	    $d1 = new Duration('PT20S');
	    $d2 = new Duration('PT20S');
	    $d1->sub($d2);
	    $this->assertEquals('PT0S', $d1->__toString());
	    
	    $d1 = new Duration('PT20S');
	    $d2 = new Duration('PT21S');
	    $d1->sub($d2);
	    $this->assertTrue($d1->isNegative());
	}
		
	/**
	 * @dataProvider shorterThanProvider
	 * 
	 * @param Duration $duration1
	 * @param Duration $duration2
	 * @param boolean $expected
	 */
	public function testShorterThan(Duration $duration1, Duration $duration2, $expected) {
		$this->assertSame($expected, $duration1->shorterThan($duration2));
	}
	
	/**
	 * @dataProvider longerThanOrEqualsProvider
	 *
	 * @param Duration $duration1
	 * @param Duration $duration2
	 * @param boolean $expected
	 */
	public function testLongerThanOrEquals(Duration $duration1, Duration $duration2, $expected) {
		$this->assertSame($expected, $duration1->longerThanOrEquals($duration2));
	}
	
	public function shorterThanProvider() {
		$returnValue = array();
		$returnValue[] = array(new Duration('P1Y'), new Duration('P2Y'), true);
		$returnValue[] = array(new Duration('P1Y'), new Duration('P1Y'), false);
		$returnValue[] = array(new Duration('P1Y'), new Duration('P1YT2S'), true);
		$returnValue[] = array(new Duration('P2Y'), new Duration('P1Y'), false);
		$returnValue[] = array(new Duration('PT0S'), new Duration('PT1S'), true);
		$returnValue[] = array(new Duration('PT1H25M0S'), new Duration('PT1H26M12S'), true);
		$returnValue[] = array(new Duration('PT1H26M12S'), new Duration('PT1H25M0S'), false);
		
		return $returnValue;
	}
	
	public function longerThanOrEqualsProvider() {
		$returnValue = array();
		$returnValue[] = array(new Duration('P1Y'), new Duration('P2Y'), false);
		$returnValue[] = array(new Duration('P1Y'), new Duration('P1Y'), true);
		$returnValue[] = array(new Duration('P1Y'), new Duration('P1YT2S'), false);
		$returnValue[] = array(new Duration('P2Y'), new Duration('P1Y'), true);
		$returnValue[] = array(new Duration('PT0S'), new Duration('PT1S'), false);
		$returnValue[] = array(new Duration('PT1H25M0S'), new Duration('PT1H26M12S'), false);
		$returnValue[] = array(new Duration('PT1H26M12S'), new Duration('PT1H25M0S'), true);
		$returnValue[] = array(new Duration('PT1H26M'), new Duration('PT1H26M'), true);
	
		return $returnValue;
	}
	
	public function validDurationProvider() {
		return array(
			array('P2D'), // 2 days
			array('PT2S'), // 2 seconds
			array('P6YT5M') // 6 years, 5 months
		);
	}
	
	public function invalidDurationProvider() {
		return array(
			array('D2P'),
			array('PSSST'),
			array('Invalid'),
			array('')
		);
	}
	
	public function toStringProvider() {
		return array(
			array(new Duration('P2D'), 'P2D'), // 2 days
			array(new Duration('PT2S'), 'PT2S'), // 2 seconds
			array(new Duration('P6YT5M'), 'P6YT5M'), // 6 years, 5 months
			array(new Duration('PT0S'), 'PT0S'), // 0 seconds
		);
	}
}
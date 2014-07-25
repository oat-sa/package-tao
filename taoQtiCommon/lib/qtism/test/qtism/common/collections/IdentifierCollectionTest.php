<?php
use qtism\common\collections\IdentifierCollection;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class IdentifierCollectionTest extends QtiSmTestCase {
	
	/**
	 * The IdentifierCollection object to test.
	 * 
	 * @var IdentifierCollection
	 */
	private $collection;
	
	public function setUp() {
		parent::setUp();
		$this->collection = new IdentifierCollection();
	}
	
	public function tearDown() {
	    parent::tearDown();
	    unset($this->collection);
	}
	
	public function testAddIdentifier() {
		$string = 'foobar';
		$this->collection[] = $string;
		$this->assertEquals(count($this->collection), 1);
		$this->assertEquals($this->collection[0], 'foobar');
	}
	
	/**
	 * @depends testAddIdentifier
	 */
	public function testRemoveIdentifier() {
		$string  = 'foobar';
		$this->collection[] = $string;
		unset($this->collection[0]);
		$this->assertEquals(count($this->collection), 0);
	}
	
	/**
	 * @depends testAddIdentifier
	 */
	public function testModifyIdentifier() {
		$string = 'foobar';
		$this->collection[] = $string;
		$this->assertTrue(isset($this->collection[0]));
		$this->collection[0] = 'foo';
		$this->assertNotEquals($this->collection[0], $string);
	}
	
	public function testAddIdentifierWrongType() {
		$identifier = '.identifier';
		$this->setExpectedException('\\InvalidArgumentException');
		$this->collection[] = $identifier;
	}
}
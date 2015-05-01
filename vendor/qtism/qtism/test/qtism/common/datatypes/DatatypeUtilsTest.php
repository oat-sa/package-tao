<?php
use qtism\common\datatypes\Utils;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');


class DatatypeUtilsTest extends QtiSmTestCase {
    
    /**
     * @dataProvider isQtiIntegerValidProvider
     * @param integer $value
     */
    public function testIsQtiIntegerValid($value) {
        $this->assertTrue(Utils::isQtiInteger($value));
    }
    
	/**
     * @dataProvider isQtiIntegerInvalidProvider
     * @param integer $value
     */
    public function testIsQtiIntegerInvalid($value) {
        $this->assertFalse(Utils::isQtiInteger($value));
    }
    
    public function isQtiIntegerValidProvider() {
        return array(
            array(0),
            array(-0),
            array(250),
            array(-250),
            array(2147483647),
            array(-2147483647)
        );
    }
    
    public function isQtiIntegerInvalidProvider() {
        return array(
            array(null),
            array(''),
            array('bla'),
            array(25.5),
            array(true),
            array(2147483648),
            array(-2147483649)
        );
    }
}
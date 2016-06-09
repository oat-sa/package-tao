<?php

use qtism\data\storage\php\marshalling\PhpScalarMarshaller;
use qtism\data\storage\php\marshalling\PhpArrayMarshaller;

require_once (dirname(__FILE__) . '/../../../../../QtiSmPhpMarshallerTestCase.php');

class PhpArrayMarshallerTest extends QtiSmPhpMarshallerTestCase {
	
    public function testEmptyArray() {
        $ctx = $this->createMarshallingContext();
        $marshaller = new PhpArrayMarshaller($ctx, array());
        $marshaller->marshall();
        
        $this->assertEquals("\$array_0 = array();\n", $this->getStream()->getBinary());
    }
    
    public function testIntegerArray() {
        $ctx = $this->createMarshallingContext();
        $arrayMarshaller = new PhpArrayMarshaller($ctx, array(0, 1, 2));
        $arrayMarshaller->marshall();
        
        $expected = "\$array_0 = array(0, 1, 2);\n";
        $this->assertEquals($expected, $this->getStream()->getBinary());
    }
}
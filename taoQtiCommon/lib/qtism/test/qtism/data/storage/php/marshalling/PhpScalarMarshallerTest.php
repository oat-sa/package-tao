<?php

use qtism\data\storage\php\marshalling\PhpScalarMarshaller;

require_once (dirname(__FILE__) . '/../../../../../QtiSmPhpMarshallerTestCase.php');

class PhpScalarMarshallerTest extends QtiSmPhpMarshallerTestCase {
	
    /**
     * 
     * @dataProvider marshallDataProvider
     * @param string $expectedInStream
     * @param mixed $scalar
     */
    public function testMarshall($expectedInStream, $scalar) {
        $ctx = $this->createMarshallingContext();
        $marshaller = new PhpScalarMarshaller($ctx, $scalar);
        $marshaller->marshall();
        
        $this->assertEquals($expectedInStream, $this->getStream()->getBinary());
    }
    
    public function testMarshallWrongDataType() {
        $this->setExpectedException('\\InvalidArgumentException');
        $ctx = $this->createMarshallingContext();
        $marshaller = new PhpScalarMarshaller($ctx, new stdClass());
    }

    public function marshallDataProvider() {
        return array(
            array("\$nullvalue_0 = null;\n", null),
            array("\$integer_0 = 10;\n", 10),
            array("\$double_0 = 10.44;\n", 10.44),
            array("\$string_0 = \"\";\n", ''),
            array("\$string_0 = \"Hello!\";\n", "Hello!"),
            array("\$boolean_0 = true;\n", true),
            array("\$boolean_0 = false;\n", false),
            array("\$string_0 = \"Hello \\n there!\";\n", "Hello \n there!"),
            array("\$string_0 = \"Hello \\\\n there!\";\n", "Hello \\n there!"),
            array("\$string_0 = \"Hello \\\\ there!\";\n", "Hello \\ there!"),
        );
    }
}
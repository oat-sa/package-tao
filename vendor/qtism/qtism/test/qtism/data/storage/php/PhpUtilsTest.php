<?php
use qtism\data\storage\php\Utils as PhpUtils;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class PhpUtilsTest extends QtiSmTestCase {
    
    /**
     * @dataProvider doubleQuotedPhpStringDataProvider
     */
    public function testDoubleQuotedPhpString($input, $expected) {
        $this->assertEquals($expected, PhpUtils::doubleQuotedPhpString($input));
    }
    
    public function doubleQuotedPhpStringDataProvider() {
        return array(
            array('', '""'),
            array("\"", "\"\\\"\""),
            array("\"\"", "\"\\\"\\\"\""),
            array("\n", "\"\\n\""),
            array("\r\n", "\"\\r\\n\""),
            array("Hello World!", "\"Hello World!\""),
            array("中国是伟大的", "\"中国是伟大的\""), // chinese is great
            array("/[a-z]+/ui", "\"/[a-z]+/ui\""),
            array("\\nhello\\$", "\"\\\\nhello\\\\\\$\""),
            array("中国是伟\$大的", "\"中国是伟\\\$大的\"")
        );
    }
}
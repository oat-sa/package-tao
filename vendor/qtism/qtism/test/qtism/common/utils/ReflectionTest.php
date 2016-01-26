<?php

use qtism\common\datatypes\Integer;
use qtism\common\utils\Reflection;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class ReflectionTest extends QtiSmTestCase {
	
    /**
     * @dataProvider shortClassNameProvider
     * @param mixed $expected
     * @param mixed $object
     */
    public function testShortClassName($expected, $object) {
        $this->assertSame($expected, Reflection::shortClassName($object));
    }
    
    public function shortClassNameProvider() {
        return array(
            array("SomeClass", "SomeClass"),
            array("Class", "My\\Class"),
            array("Class", "My\\Super\\Class"),
            array("Class", "\\My\\Super\\Class"),
                        
            array("stdClass", new \stdClass()),
            array("Integer", new Integer(10)),
                        
            array("My_Stupid_Class", "My_Stupid_Class"),
            array(false, 12),
            array(false, null),
            array(false, "\\"),       
        );
    }
}
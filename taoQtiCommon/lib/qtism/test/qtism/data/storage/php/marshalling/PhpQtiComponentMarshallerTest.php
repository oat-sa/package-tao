<?php

use qtism\data\ItemSessionControl;

use qtism\data\storage\php\marshalling\PhpScalarMarshaller;

use qtism\data\state\Weight;

use qtism\data\storage\php\marshalling\PhpQtiComponentMarshaller;

use qtism\data\rules\ExitTest;

require_once (dirname(__FILE__) . '/../../../../../QtiSmPhpMarshallerTestCase.php');

class PhpQtiComponentMarshallerTest extends QtiSmPhpMarshallerTestCase {
	
    public function testEmptyComponent() {
        $component = new ExitTest();
        $ctx = $this->createMarshallingContext();
        $marshaller = new PhpQtiComponentMarshaller($ctx, $component);
        $marshaller->marshall();
        
        $this->assertEquals("\$exittest_0 = new qtism\\data\\rules\\ExitTest();\n", $this->getStream()->getBinary());
    }
    
    public function testOnlyScalarPropertiesComponentAllInConstructor() {
        $component = new Weight('weight1', 1.1);
        $ctx = $this->createMarshallingContext();
        
        $scalarMarshaller = new PhpScalarMarshaller($ctx, $component->getIdentifier());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->getValue());
        $scalarMarshaller->marshall();
        
        $componentMarshaller = new PhpQtiComponentMarshaller($ctx, $component);
        $componentMarshaller->marshall();
        
        $expected = "\$string_0 = \"weight1\";\n";
        $expected.= "\$double_0 = 1.1;\n";
        $expected.= "\$weight_0 = new qtism\\data\\state\\Weight(\$string_0, \$double_0);\n";
        
        $this->assertEquals($expected, $this->getStream()->getBinary());
    }
    
    public function testOnlyScalarPropertiesConstructorAndProperties() {
        $component = new ItemSessionControl();
        $ctx = $this->createMarshallingContext();
        
        $scalarMarshaller = new PhpScalarMarshaller($ctx, $component->getMaxAttempts());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->mustShowFeedback());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->doesAllowReview());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->mustShowSolution());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->doesAllowComment());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->mustValidateResponses());
        $scalarMarshaller->marshall();
        $scalarMarshaller->setToMarshall($component->doesAllowSkipping());
        $scalarMarshaller->marshall();
        
        $componentMarshaller = new PhpQtiComponentMarshaller($ctx, $component);
        $componentMarshaller->marshall();
        
        $expected = "\$integer_0 = 1;\n";
        $expected.= "\$boolean_0 = false;\n";
        $expected.= "\$boolean_1 = true;\n";
        $expected.= "\$boolean_2 = false;\n";
        $expected.= "\$boolean_3 = false;\n";
        $expected.= "\$boolean_4 = false;\n";
        $expected.= "\$boolean_5 = true;\n";
        $expected.= "\$itemsessioncontrol_0 = new qtism\\data\\ItemSessionControl();\n";
        $expected.= "\$itemsessioncontrol_0->setMaxAttempts(\$integer_0);\n";
        $expected.= "\$itemsessioncontrol_0->setShowFeedback(\$boolean_0);\n";
        $expected.= "\$itemsessioncontrol_0->setAllowReview(\$boolean_1);\n";
        $expected.= "\$itemsessioncontrol_0->setShowSolution(\$boolean_2);\n";
        $expected.= "\$itemsessioncontrol_0->setAllowComment(\$boolean_3);\n";
        $expected.= "\$itemsessioncontrol_0->setValidateResponses(\$boolean_4);\n";
        $expected.= "\$itemsessioncontrol_0->setAllowSkipping(\$boolean_5);\n";

        $this->assertEquals($expected, $this->getStream()->getBinary());
    }
}
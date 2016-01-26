<?php

use qtism\data\storage\xml\Utils;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlUtilsTest extends QtiSmTestCase {
    
	/**
	 * @dataProvider validInferQTIVersionProvider
	 */
	public function testInferQTIVersionValid($file, $expectedVersion) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->load($file);
		$this->assertEquals($expectedVersion, Utils::inferQTIVersion($dom));
	}
	
	public function validInferQTIVersionProvider() {
		return array(
			array(self::samplesDir() . 'ims/items/2_1/associate.xml', '2.1'),
			array(self::samplesDir() . 'ims/items/2_0/associate.xml', '2.0'),
			array(self::samplesDir() . 'ims/tests/arbitrary_collections_of_item_outcomes/arbitrary_collections_of_item_outcomes.xml', '2.1')		
		);
	}
	
	/**
	 * 
	 * @param string $originalXmlString
	 * @param string $expectedXmlString
	 * @dataProvider anonimizeElementProvider
	 */
	public function testAnonimizeElement($originalXmlString, $expectedXmlString) {
	    $elt = $this->createDOMElement($originalXmlString);
	    $newElt = Utils::anonimizeElement($elt);
	    
	    $this->assertEquals($expectedXmlString, $newElt->ownerDocument->saveXML($newElt));
	}
	
	public function anonimizeElementProvider() {
	    return array(
	        array('<m:math xmlns:m="http://www.w3.org/1998/Math/MathML" display="inline"><m:mn>1</m:mn><m:mo>+</m:mo><m:mn>2</m:mn><m:mo>=</m:mo><m:mn>3</m:mn></m:math>',
	               '<math display="inline"><mn>1</mn><mo>+</mo><mn>2</mn><mo>=</mo><mn>3</mn></math>'),
	                    
            array('<math xmlns="http://www.w3.org/1998/Math/MathML" display="inline"><mn>1</mn><mo>+</mo><mn>2</mn><mo>=</mo><mn>3</mn></math>',
                   '<math display="inline"><mn>1</mn><mo>+</mo><mn>2</mn><mo>=</mo><mn>3</mn></math>'),
	                    
	        array('<math xmlns="http://www.w3.org/1998/Math/MathML" display="inline"><mn><![CDATA[1]]></mn><mo>+</mo><mn><![CDATA[2]]></mn><mo>=</mo><mn><![CDATA[3]]></mn></math>',
	               '<math display="inline"><mn><![CDATA[1]]></mn><mo>+</mo><mn><![CDATA[2]]></mn><mo>=</mo><mn><![CDATA[3]]></mn></math>')
	    );
	}
}
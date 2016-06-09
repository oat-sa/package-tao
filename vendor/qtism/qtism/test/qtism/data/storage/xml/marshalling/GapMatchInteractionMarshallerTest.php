<?php

use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\P;
use qtism\data\content\interactions\Gap;
use qtism\data\content\interactions\GapChoiceCollection;
use qtism\data\content\interactions\GapMatchInteraction;
use qtism\data\content\xhtml\text\Div;
use qtism\data\content\BlockStaticCollection;
use qtism\data\content\interactions\GapImg;
use qtism\data\content\xhtml\Object;
use qtism\data\content\TextRun;
use qtism\data\content\TextOrVariableCollection;
use qtism\data\content\interactions\GapText;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class GapMatchInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		
	    $gapText = new GapText('gapText1', 1);
	    $gapText->setContent(new TextOrVariableCollection(array(new TextRun('This is gapText1'))));
	    
	    $object = new Object("./myimg.png", "image/png");
	    $gapImg = new GapImg('gapImg1', 1, $object);
	    
	    $gap1 = new Gap('G1');
	    $gap2 = new Gap('G2');
	    
	    $p = new P();
	    $p->setContent(new InlineCollection(array(new TextRun('A text... '), $gap1, new TextRun(' and an image... '), $gap2)));
	    
	    $gapMatch = new GapMatchInteraction('RESPONSE', new GapChoiceCollection(array($gapText, $gapImg)), new BlockStaticCollection(array($p)));
	    
        $marshaller = $this->getMarshallerFactory()->createMarshaller($gapMatch);
        $element = $marshaller->marshall($gapMatch);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<gapMatchInteraction responseIdentifier="RESPONSE"><gapText identifier="gapText1" matchMax="1">This is gapText1</gapText><gapImg identifier="gapImg1" matchMax="1"><object data="./myimg.png" type="image/png"/></gapImg><p>A text... <gap identifier="G1"/> and an image... <gap identifier="G2"/></p></gapMatchInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <gapMatchInteraction responseIdentifier="RESPONSE"><gapText identifier="gapText1" matchMax="1">This is gapText1</gapText><gapImg identifier="gapImg1" matchMax="1"><object data="./myimg.png" type="image/png"/></gapImg><p>A text... <gap identifier="G1"/> and an image... <gap identifier="G2"/></p></gapMatchInteraction>
        ');
        
        $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
        $gapMatch = $marshaller->unmarshall($element);
        
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\GapMatchInteraction', $gapMatch);
        $this->assertEquals('RESPONSE', $gapMatch->getResponseIdentifier());
        $this->assertFalse($gapMatch->mustShuffle());
        
        $gapChoices = $gapMatch->getGapChoices();
        $this->assertEquals(2, count($gapChoices));
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\GapText', $gapChoices[0]);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\GapImg', $gapChoices[1]);
        
        $gaps = $gapMatch->getComponentsByClassName('gap');
        $this->assertEquals(2, count($gaps));
        $this->assertEquals('G1', $gaps[0]->getIdentifier());
        $this->assertEquals('G2', $gaps[1]->getIdentifier());
	}
}
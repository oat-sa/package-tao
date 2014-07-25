<?php

use qtism\data\content\xhtml\Img;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ImgMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $img = new Img('my/image.png', "An Image...", "my-img");
	    $img->setClass('beautiful');
	    $img->setHeight('40%');
	    $img->setWidth(30);
	    $img->setLang('en-YO');
	    $img->setLongdesc("A Long Description...");
	    
 	    $marshaller = $this->getMarshallerFactory()->createMarshaller($img);
 	    $element = $marshaller->marshall($img);
	    
 	    $dom = new DOMDocument('1.0', 'UTF-8');
 	    $element = $dom->importNode($element, true);
 	    $this->assertEquals('<img src="my/image.png" alt="An Image..." width="30" height="40%" longdesc="A Long Description..." id="my-img" class="beautiful" xml:lang="en-YO"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    
	    $element = $this->createDOMElement('
            <img src="my/image.png" alt="An Image..." width="30" height="40%" longdesc="A Long Description..." id="my-img" class="beautiful" xml:lang="en-YO"/>
	    ');
	    
 	    $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
 	    $img = $marshaller->unmarshall($element);
 	    
 	    $this->assertInstanceOf('qtism\\data\\content\\xhtml\\Img', $img);
 	    $this->assertEquals('my/image.png', $img->getSrc());
 	    $this->assertEquals('An Image...', $img->getAlt());
 	    $this->assertSame(30, $img->getWidth());
 	    $this->assertEquals('40%', $img->getHeight());
 	    $this->assertEquals('A Long Description...', $img->getLongDesc());
 	    $this->assertEquals('my-img', $img->getId());
 	    $this->assertEquals('beautiful', $img->getClass());
 	    $this->assertEquals('en-YO', $img->getLang());
	}
}
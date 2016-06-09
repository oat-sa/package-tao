<?php
namespace qtismtest\data\storage\xml;

use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlStorageException;

class XmlDocumentXIncludeTest extends \QtiSmTestCase {
	
    public function testLoadAndSaveXIncludeNsInTag() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/items/xinclude/xinclude_ns_in_tag.xml', true);
         
        $includes = $doc->getDocumentComponent()->getComponentsByClassName('include');
        $this->assertEquals(1, count($includes));
        $this->assertEquals('xinclude_ns_in_tag_content1.xml', $includes[0]->getHref());
         
        $file = tempnam('/tmp', 'qsm');
        $doc->save($file);
         
        // Does it validate again?
        $doc = new XmlDocument();
        try {
            $doc->load($file, true);
            $this->assertTrue(true);
        } catch (XmlStorageException $e) {
            $this->assertFalse(true, "The document using xinclude should validate after being saved.");
        }
    }
    
    /**
     * @depends testLoadAndSaveXIncludeNsInTag
     */
    public function testLoadAndResolveXIncludeSameBase() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/items/xinclude/xinclude_ns_in_tag.xml', true);
        
        // At this moment, includes are not resolved.
        $includes = $doc->getDocumentComponent()->getComponentsByClassName('include');
        $this->assertEquals(1, count($includes));
        // So no img components can be found...
        $imgs = $doc->getDocumentComponent()->getComponentsByClassName('img');
        $this->assertEquals(0, count($imgs));
        
        $doc->xInclude();
        
        // Now they are!
        $includes = $doc->getDocumentComponent()->getComponentsByClassName('include');
        $this->assertEquals(0, count($includes));
        
        // And we should find an img component then!
        $imgs = $doc->getDocumentComponent()->getComponentsByClassName('img');
        $this->assertEquals(1, count($imgs));
        
        // Check that xml:base was appropriately resolved. In this case,
        // no content for xml:base because 'xinclude_ns_in_tag_content1.xml' is in the
        // same directory as the main xml file.
        $this->assertEquals('', $imgs[0]->getXmlBase());
    }
    
    /**
     * @depends testLoadAndResolveXIncludeSameBase
     */
    public function testLoadAndResolveXIncludeDifferentBase() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/items/xinclude/xinclude_ns_in_tag_subfolder.xml', true);
        $doc->xInclude();
        
        $includes = $doc->getDocumentComponent()->getComponentsByClassName('include');
        $this->assertEquals(0, count($includes));
    
        // And we should find an img component then!
        $imgs = $doc->getDocumentComponent()->getComponentsByClassName('img');
        $this->assertEquals(1, count($imgs));
    
        // Check that xml:base was appropriately resolved. In this case,
        // no content for xml:base because 'xinclude_ns_in_tag_content1.xml' is in the
        // same directory as the main xml file.
        $this->assertEquals('subfolder/', $imgs[0]->getXmlBase());
    }
}
<?php

use qtism\data\content\xhtml\text\Strong;
use qtism\data\content\xhtml\text\P;
use qtism\data\content\TextRun;
use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\text\H1;
use qtism\data\content\xhtml\text\Div;
use qtism\data\content\xhtml\lists\LiCollection;
use qtism\data\content\xhtml\lists\Ul;
use qtism\data\content\FlowCollection;
use qtism\data\content\xhtml\lists\Li;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class DivMarshallerTest extends QtiSmTestCase {

	public function testUnmarshall() {
        $div = $this->createComponentFromXml('
            <div id="main-container" class="ui-pane">
                <div id="menu">
                    <ul>
                        <li>Start the Game</li>
                        <li>Configure Inputs</li>
                        <li>Hall of Fame</li>
                        <li>Quit</li>
                    </ul>
                </div>
                <div id="content">
                   <h1>Escape from Death Star</h1>
                   <p class="short-story">An <strong>incredible</strong> adventure.</p>
                </div>
            </div>
        ');
        
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Div', $div);
        $this->assertEquals('main-container', $div->getId());
        $this->assertEquals('ui-pane', $div->getClass());
        
        $mainContainerContent = $div->getContent();
        $this->assertEquals(5, count($mainContainerContent));
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $mainContainerContent[0]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\Div', $mainContainerContent[1]);
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $mainContainerContent[2]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Div', $mainContainerContent[3]);
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $mainContainerContent[4]);
        
        $menu = $mainContainerContent[1];
        $this->assertEquals('menu', $menu->getId());
        $menuContent = $menu->getContent();
        $this->assertEquals(3, count($menuContent));
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $menuContent[0]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\Ul', $menuContent[1]);
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $menuContent[2]);
        
        $list = $menuContent[1];
        $listContent = $list->getContent();
        $this->assertEquals(4, count($listContent));
        
        $li1 = $listContent[0];
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\Li', $li1);
        $liContent = $li1->getContent();
        $this->assertEquals(1, count($liContent));
        $this->assertEquals('Start the Game', $liContent[0]->getContent());
        
        $li2 = $listContent[1];
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\Li', $li2);
        $liContent = $li2->getContent();
        $this->assertEquals(1, count($liContent));
        $this->assertEquals('Configure Inputs', $liContent[0]->getContent());
        
        $li3 = $listContent[2];
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\Li', $li3);
        $liContent = $li3->getContent();
        $this->assertEquals(1, count($liContent));
        $this->assertEquals('Hall of Fame', $liContent[0]->getContent());
        
        $li4 = $listContent[3];
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\lists\\Li', $li4);
        $liContent = $li4->getContent();
        $this->assertEquals(1, count($liContent));
        $this->assertEquals('Quit', $liContent[0]->getContent());
        
        $content = $mainContainerContent[3];
        $this->assertEquals('content', $content->getId());
        $contentContent = $content->getContent();
        $this->assertEquals(5, count($contentContent));
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $contentContent[0]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\H1', $contentContent[1]);
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $contentContent[2]);
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\P', $contentContent[3]);
        $this->assertInstanceOf('qtism\\data\\content\\TextRun', $contentContent[4]);
        
        $h1 = $contentContent[1];
        $h1Content = $h1->getContent();
        $this->assertEquals(1, count($h1Content));
        $this->assertEquals('Escape from Death Star', $h1Content[0]->getContent());
        
        $p = $contentContent[3];
        $this->assertEquals('short-story', $p->getClass());
        $pContent = $p->getContent();
        $this->assertEquals(3, count($pContent));
        $this->assertEquals('An ', $pContent[0]->getContent());
        $this->assertEquals(' adventure.', $pContent[2]->getContent());
        
        $strong = $pContent[1];
        $strongContent = $strong->getContent();
        $this->assertEquals(1, count($strongContent));
        $this->assertEquals('incredible', $strongContent[0]->getContent());
	}
	
	
	public function testMarshall() {
        $li1 = new Li();
        $li1->setContent(new FlowCollection(array(new TextRun('Start the Game'))));
        
        $li2 = new Li();
        $li2->setContent(new FlowCollection(array(new TextRun('Configure Inputs'))));
        
        $li3 = new Li();
        $li3->setContent(new FlowCollection(array(new TextRun('Hall of Fame'))));
        
        $li4 = new Li();
        $li4->setContent(new FlowCollection(array(new TextRun('Quit'))));
        
        $ul = new Ul();
        $ul->setContent(new LiCollection(array($li1, $li2, $li3, $li4)));
        
        $divMenu = new Div('menu');
        $divMenu->setContent(new FlowCollection(array($ul)));
        
        $h1 = new H1();
        $h1->setContent(new InlineCollection(array(new TextRun('Escape from Death Star'))));
        
        $strong = new Strong();
        $strong->setContent(new InlineCollection(array(new TextRun('incredible'))));
        
        $p = new P();
        $p->setClass('short-story');
        $p->setContent(new InlineCollection(array(new TextRun('An '), $strong, new TextRun(' adventure.'))));
        
        $divContent = new Div('content');
        $divContent->setContent(new FlowCollection(array($h1, $p)));
        
        $divContainer = new Div('main-container', 'ui-pane');
        $divContainer->setContent(new FlowCollection(array($divMenu, $divContent)));
        
        $element = $this->getMarshallerFactory()->createMarshaller($divContainer)->marshall($divContainer);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        
        $expected = '<div id="main-container" class="ui-pane"><div id="menu"><ul><li>Start the Game</li><li>Configure Inputs</li><li>Hall of Fame</li><li>Quit</li></ul></div><div id="content"><h1>Escape from Death Star</h1><p class="short-story">An <strong>incredible</strong> adventure.</p></div></div>';
        $this->assertEquals($expected, $dom->saveXML($element));
	}
}

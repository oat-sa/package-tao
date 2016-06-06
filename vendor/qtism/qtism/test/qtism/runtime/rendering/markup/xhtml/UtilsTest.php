<?php

use qtism\data\ShufflableCollection;
use qtism\data\content\interactions\SimpleChoice;
use qtism\runtime\rendering\markup\xhtml\Utils;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class RenderingMarkupXhtmlUtils extends QtiSmTestCase {
    
    public function testShuffleWithFixed() {
        // It is difficult to test a random algorithm.
        // In this way, we just check it runs. Deeper
        // analysis can be done in /test/scripts/.
        
        // DOM creation...
        $dom = new DOMDocument('1.0', 'UTF-8');
        $node = $dom->createElement('fakenode');
        $dom->appendChild($node);
        
        $choice = $dom->createElement('div');
        $choice->setAttribute('fixed', 'false');
        $choice->setAttribute('id', 'choice1');
        $choice->setAttribute('class', 'qti-simpleChoice');
        $node->appendChild($choice);
        
        $choice = $dom->createElement('div');
        $choice->setAttribute('fixed', 'true');
        $choice->setAttribute('id', 'choice2');
        $choice->setAttribute('class', 'qti-simpleChoice qti-hide');
        $node->appendChild($choice);
        
        $choice = $dom->createElement('div');
        $choice->setAttribute('fixed', 'false');
        $choice->setAttribute('id', 'choice3');
        $choice->setAttribute('class', 'qti-simpleChoice');
        $node->appendChild($choice);
        
        // In memory model creation ...
        $shufflables = new ShufflableCollection();
        
        $choice = new SimpleChoice('choice1');
        $choice->setFixed(false);
        $choice->setId('choice1');
        $shufflables[] = $choice;
        
        $choice = new SimpleChoice('choice2');
        $choice->setFixed(true);
        $choice->setId('choice2');
        $shufflables[] = $choice;
        
        $choice = new SimpleChoice('choice3');
        $choice->setFixed(false);
        $choice->setId('choice3');
        $shufflables[] = $choice;
        
        Utils::shuffle($node, $shufflables);
        
        // Let's check if 'choice2' is still in place...
        $this->assertEquals('choice2', $node->getElementsByTagName('div')->item(1)->getAttribute('id'));
        $node0Id = $node->getElementsByTagName('div')->item(0)->getAttribute('id');
        $node1Id = $node->getElementsByTagName('div')->item(2)->getAttribute('id');
        $this->assertTrue($node0Id === 'choice1' && $node1Id === 'choice3' || $node0Id === 'choice3' && $node1Id === 'choice1');
    }
}
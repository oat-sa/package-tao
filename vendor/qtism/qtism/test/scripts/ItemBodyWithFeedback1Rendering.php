<?php

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\runtime\common\State;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

$doc = new XmlDocument();
$doc->load('../samples/rendering/itembodywithfeedback_1.xml');

$outcome1 = new OutcomeVariable('outcome1', Cardinality::SINGLE, BaseType::IDENTIFIER, '');
$outcome2 = new OutcomeVariable('outcome2', Cardinality::SINGLE, BaseType::IDENTIFIER, '');

$renderer = new XhtmlRenderingEngine();

if (isset($argv[1]) && $argv[1] === 'CONTEXT_AWARE') {
    $renderer->setFeedbackShowHidePolicy(AbstractMarkupRenderingEngine::CONTEXT_AWARE);
    
    if (isset($argv[2])) {
        $outcome1->setValue($argv[2]);
    }
    
    if (isset($argv[3])) {
        $outcome2->setValue($argv[3]);
    }
}

$renderer->setState(new State(array($outcome1, $outcome2)));
$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
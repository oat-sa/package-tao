<?php

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;

use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

$doc = new XmlDocument();
$doc->load('../samples/rendering/xmlbase_1.xml');

$renderer = new XhtmlRenderingEngine();

if (empty($argv[1]) === false) {
    switch (strtolower($argv[1])) {
        case 'ignore':
            $renderer->setXmlBasePolicy(AbstractMarkupRenderingEngine::XMLBASE_IGNORE);
        break;
        
        case 'keep':
            $renderer->setXmlBasePolicy(AbstractMarkupRenderingEngine::XMLBASE_KEEP);
        break;
        
        case 'process':
            $renderer->setXmlBasePolicy(AbstractMarkupRenderingEngine::XMLBASE_PROCESS);
        break;
    }
}

if (empty($argv[2]) === false) {
    $renderer->setRootBase(strval($argv[2]));
}

$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
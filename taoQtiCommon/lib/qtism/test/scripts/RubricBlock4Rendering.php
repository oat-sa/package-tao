<?php

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\View;
use qtism\data\ViewCollection;
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

$doc = new XmlDocument();
$doc->load('../samples/rendering/rubricblock_4.xml');

$renderer = new XhtmlRenderingEngine();
$renderer->setViewPolicy(XhtmlRenderingEngine::TEMPLATE_ORIENTED);
$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
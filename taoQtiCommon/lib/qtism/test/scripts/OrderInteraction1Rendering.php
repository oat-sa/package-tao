<?php

use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

$doc = new XmlDocument();
$doc->load('../samples/rendering/orderinteraction_1.xml');

$renderer = new XhtmlRenderingEngine();

if (isset($argv[1]) && $argv[1] === 'shuffle') {
    $renderer->setShuffle(true);
}

$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveHTML();
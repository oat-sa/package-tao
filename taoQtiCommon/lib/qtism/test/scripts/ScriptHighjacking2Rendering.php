<?php
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;
use qtism\data\storage\StorageException;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

$doc = new XmlDocument();
try {
    $doc->load('../samples/rendering/script_highjacking_2.xml');
}
catch (StorageException $e) {
    do {
        echo $e->getMessage() . "\n";
        $e = $e->getPrevious();
    }
    while($e);
    
    die();
}

$renderer = new XhtmlRenderingEngine();
$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
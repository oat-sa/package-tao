<?php
require_once(dirname(__FILE__) . '/../../../../qtism/qtism.php');
use qtism\data\storage\php\PhpDocument;
use qtism\data\storage\xml\XmlCompactDocument;

$xmlFile = dirname(__FILE__) . '/../runtime/nonlinear_40_items.xml';
$xmlDoc = new XmlCompactDocument();
$xmlDoc->load($xmlFile);

$phpDoc = new PhpDocument();
$phpDoc->setDocumentComponent($xmlDoc->getDocumentComponent());
$phpDoc->save('nonlinear_40_items.php');
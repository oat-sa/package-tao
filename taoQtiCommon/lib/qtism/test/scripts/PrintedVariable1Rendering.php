<?php
use qtism\common\datatypes\String;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\TemplateVariable;
use qtism\runtime\common\State;
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../qtism/qtism.php');

$doc = new XmlDocument();
$doc->load('../samples/rendering/printedvariable_1.xml');

$renderer = new XhtmlRenderingEngine();
$state = new State();
$state->setVariable(new TemplateVariable('TPL_H', Cardinality::SINGLE, BaseType::STRING, new String('Bubble Gum')));
$state->setVariable(new TemplateVariable('TPL_He', Cardinality::SINGLE, BaseType::STRING, new String('Bacta')));
$state->setVariable(new TemplateVariable('TPL_C', Cardinality::SINGLE, BaseType::STRING, new String('Cola')));
$state->setVariable(new TemplateVariable('TPL_O', Cardinality::SINGLE, BaseType::STRING, new String('Meat')));
$state->setVariable(new TemplateVariable('TPL_N', Cardinality::SINGLE, BaseType::STRING, new String('Potatoes')));
$state->setVariable(new TemplateVariable('TPL_Cl', Cardinality::SINGLE, BaseType::STRING, new String('Candies')));

if (isset($argv[1]) && trim(strtolower($argv[1])) === 'context_aware') {
    $renderer->setPrintedVariablePolicy(XhtmlRenderingEngine::CONTEXT_AWARE);
    $renderer->setState($state);
}
else if (isset($argv[1]) && trim(strtolower($argv[1])) === 'template_oriented') {
    $renderer->setPrintedVariablePolicy(XhtmlRenderingEngine::TEMPLATE_ORIENTED);
    $renderer->setState($state);
}
else {
    $renderer->setPrintedVariablePolicy(XhtmlRenderingEngine::CONTEXT_STATIC);
}

$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
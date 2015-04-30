<?php

use qtism\data\rules\TemplateConstraint;

use qtism\data\rules\SetDefaultValue;

use qtism\data\expressions\operators\Lte;

use qtism\data\expressions\Variable;

use qtism\data\expressions\ExpressionCollection;

use qtism\data\expressions\operators\Match;

use qtism\data\rules\SetCorrectResponse;
use qtism\data\rules\TemplateElse;
use qtism\data\rules\TemplateElseIfCollection;
use qtism\data\rules\ExitTemplate;
use qtism\data\rules\TemplateElseIf;
use qtism\data\rules\TemplateCondition;
use qtism\data\rules\TemplateRuleCollection;
use qtism\data\rules\TemplateIf;
use qtism\data\rules\SetTemplateValue;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class TemplateConditionMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {
	    
	    $true = new BaseValue(BaseType::BOOLEAN, true);
	    $setTemplateValue = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $templateIf = new TemplateIf($true, new TemplateRuleCollection(array($setTemplateValue)));
	    $templateCondition = new TemplateCondition($templateIf);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($templateCondition)->marshall($templateCondition);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateCondition><templateIf><baseValue baseType="boolean">true</baseValue><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue></templateIf></templateCondition>', $dom->saveXML($element));
	}
	
	public function testUnmarshallMinimal() {
	    $element = $this->createDOMElement('
	        <templateCondition>
                <templateIf>
                    <baseValue baseType="boolean">true</baseValue>
                    <setTemplateValue identifier="tpl1">
                        <baseValue baseType="integer">1337</baseValue>
                    </setTemplateValue>
                </templateIf>
            </templateCondition>
	    ');
	    
	    $templateCondition = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    $this->assertEquals(0, count($templateCondition->getTemplateElseIfs()));
	    $this->assertFalse($templateCondition->hasTemplateElse());
	    
	    $templateIf = $templateCondition->getTemplateIf();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	    
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateIf->getExpression());
	    $this->assertEquals(BaseType::BOOLEAN, $templateIf->getExpression()->getBaseType());
	    $this->assertInternalType('boolean', $templateIf->getExpression()->getValue());
	    $this->assertTrue($templateIf->getExpression()->getValue());
	    
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertEquals('tpl1', $templateRules[0]->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	    $this->assertEquals(BaseType::INTEGER, $templateRules[0]->getExpression()->getBaseType());
	    $this->assertEquals(1337, $templateRules[0]->getExpression()->getValue());
	}
	
	public function testMarshallElseIfMinimal() {
	    $true = new BaseValue(BaseType::BOOLEAN, true);
	    $setTemplateValue = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $templateIf = new TemplateIf($true, new TemplateRuleCollection(array($setTemplateValue)));
	    $templateCondition = new TemplateCondition($templateIf);
	    
	    $false = new BaseValue(BaseType::BOOLEAN, false);
	    $exitTemplate = new ExitTemplate();
	    $templateElseIf = new TemplateElseIf($false, new TemplateRuleCollection(array($exitTemplate)));
	    $templateCondition->setTemplateElseIfs(new TemplateElseIfCollection(array($templateElseIf)));
	     
	    $element = $this->getMarshallerFactory()->createMarshaller($templateCondition)->marshall($templateCondition);
	     
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateCondition><templateIf><baseValue baseType="boolean">true</baseValue><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue></templateIf><templateElseIf><baseValue baseType="boolean">false</baseValue><exitTemplate/></templateElseIf></templateCondition>', $dom->saveXML($element));
	}
	
	public function testUnMarshallElseIfMinimal() {
	    $element = $this->createDOMElement('
	        <templateCondition>
                <templateIf>
                    <baseValue baseType="boolean">true</baseValue>
                    <setTemplateValue identifier="tpl1">
                        <baseValue baseType="integer">1337</baseValue>
                    </setTemplateValue>
                </templateIf>
                <templateElseIf>
                    <baseValue baseType="boolean">false</baseValue>
                    <exitTemplate/>
                </templateElseIf>
            </templateCondition>
	    ');
	     
	    $templateCondition = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    
	    $templateCondition = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    $this->assertFalse($templateCondition->hasTemplateElse());
	     
	    $templateIf = $templateCondition->getTemplateIf();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	     
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateIf->getExpression());
	    $this->assertEquals(BaseType::BOOLEAN, $templateIf->getExpression()->getBaseType());
	    $this->assertInternalType('boolean', $templateIf->getExpression()->getValue());
	    $this->assertTrue($templateIf->getExpression()->getValue());
	     
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertEquals('tpl1', $templateRules[0]->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	    $this->assertEquals(BaseType::INTEGER, $templateRules[0]->getExpression()->getBaseType());
	    $this->assertEquals(1337, $templateRules[0]->getExpression()->getValue());
	    
	    $templateElseIfs = $templateCondition->getTemplateElseIfs();
	    $this->assertEquals(1, count($templateElseIfs));
	    $templateElseIf = $templateElseIfs[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElseIf', $templateElseIf);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateElseIf->getExpression());
	    $this->assertEquals(BaseType::BOOLEAN, $templateElseIf->getExpression()->getBaseType());
	    $this->assertInternalType('boolean', $templateElseIf->getExpression()->getValue());
	    $this->assertFalse($templateElseIf->getExpression()->getValue());
	    
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\ExitTemplate', $templateRules[0]);
	}
	
	public function testMarshallIfElseMinimal() {
	    $true = new BaseValue(BaseType::BOOLEAN, true);
	    $setTemplateValue = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $templateIf = new TemplateIf($true, new TemplateRuleCollection(array($setTemplateValue)));
	    $templateCondition = new TemplateCondition($templateIf);
	    
	    $setCorrectResponse = new SetCorrectResponse('RESPONSE', new BaseValue(BaseType::IDENTIFIER, 'einstein'));
	    $templateElse = new TemplateElse(new TemplateRuleCollection(array($setCorrectResponse)));
	    $templateCondition->setTemplateElse($templateElse);
	     
	    $element = $this->getMarshallerFactory()->createMarshaller($templateCondition)->marshall($templateCondition);
	     
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    
	    $this->assertEquals('<templateCondition><templateIf><baseValue baseType="boolean">true</baseValue><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue></templateIf><templateElse><setCorrectResponse identifier="RESPONSE"><baseValue baseType="identifier">einstein</baseValue></setCorrectResponse></templateElse></templateCondition>', $dom->saveXML($element));
	}
	
	public function testUnmarshallIfElseMinimal() {
	    $element = $this->createDOMElement('
	        <templateCondition>
                <templateIf>
                    <baseValue baseType="boolean">true</baseValue>
                    <setTemplateValue identifier="tpl1">
                        <baseValue baseType="integer">1337</baseValue>
                    </setTemplateValue>
                </templateIf>
                <templateElse>
                    <setCorrectResponse identifier="RESPONSE">
                        <baseValue baseType="identifier">einstein</baseValue>
                    </setCorrectResponse>
                </templateElse>
            </templateCondition>
	    ');
	     
	    $templateCondition = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    $this->assertEquals(0, count($templateCondition->getTemplateElseIfs()));
	     
	    $templateIf = $templateCondition->getTemplateIf();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	     
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateIf->getExpression());
	    $this->assertEquals(BaseType::BOOLEAN, $templateIf->getExpression()->getBaseType());
	    $this->assertInternalType('boolean', $templateIf->getExpression()->getValue());
	    $this->assertTrue($templateIf->getExpression()->getValue());
	     
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertEquals('tpl1', $templateRules[0]->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	    $this->assertEquals(BaseType::INTEGER, $templateRules[0]->getExpression()->getBaseType());
	    $this->assertEquals(1337, $templateRules[0]->getExpression()->getValue());
	    
	    $this->assertTrue($templateCondition->hasTemplateElse());
	    $templateElse = $templateCondition->getTemplateElse();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElse', $templateElse);
	    $templateRules = $templateElse->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    
	    $setCorrectResponse = $templateRules[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\SetCorrectResponse', $setCorrectResponse);
	    $this->assertEquals('RESPONSE', $setCorrectResponse->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $setCorrectResponse->getExpression());
	    $this->assertEquals(BaseType::IDENTIFIER, $setCorrectResponse->getExpression()->getBaseType());
	    $this->assertEquals('einstein', $setCorrectResponse->getExpression()->getValue());
	}
	
	public function testUnmarshallUnleashTheBeast() {
	    $element = $this->createDOMElement('
	        <templateCondition>
	            <templateIf>
                    <lte>
	                    <variable identifier="var"/>
	                    <baseValue baseType="integer">2</baseValue>    
	                </lte>
	                <templateCondition>
	                    <templateIf>
                            <match>
	                            <variable identifier="var"/>
	                            <baseValue baseType="integer">0</baseValue>
	                        </match>
	                        <setTemplateValue identifier="tpl">
	                            <baseValue baseType="string">var is 0</baseValue>    
	                        </setTemplateValue>
	                    </templateIf>
	                    <templateElseIf>
	                        <match>
	                            <variable identifier="var"/>
	                            <baseValue baseType="integer">1</baseValue>
	                        </match>
	                        <setTemplateValue identifier="tpl">
	                            <baseValue baseType="string">var is 1</baseValue>    
	                        </setTemplateValue>
	                    </templateElseIf>
	                    <templateElse>
	                        <exitTemplate/>
	                    </templateElse>
	                </templateCondition>
	            </templateIf>
	            <templateElseIf>
	                <lte>
                        <variable identifier="var"/>
	                    <baseValue baseType="integer">5</baseValue>
	                </lte>
	                <templateCondition>
	                    <templateIf>
                            <match>
	                            <variable identifier="var"/>
	                            <baseValue baseType="integer">3</baseValue>
	                        </match>
	                        <setCorrectResponse identifier="RESPONSE">
	                            <baseValue baseType="string">jerome</baseValue>
	                        </setCorrectResponse>
	                    </templateIf>
	                    <templateElseIf>
	                        <match>
	                            <variable identifier="var"/>
	                            <baseValue baseType="integer">4</baseValue>
	                        </match>
	                        <setDefaultValue identifier="RESPONSE">
	                            <baseValue baseType="string">qtism</baseValue>
	                        </setDefaultValue>
	                    </templateElseIf>
	                    <templateElseIf>
	                        <match>
	                            <variable identifier="var"/>
	                            <baseValue baseType="integer">5</baseValue>
	                        </match>
	                        <templateConstraint>
	                            <baseValue baseType="boolean">false</baseValue>
	                        </templateConstraint>
	                        <templateConstraint>
	                            <baseValue baseType="boolean">true</baseValue>
	                        </templateConstraint>
	                    </templateElseIf>
	                </templateCondition>
	                
	                <!-- Oooooouh fucking little bastard :-D !!! -->
	                <exitTemplate/>
	            </templateElseIf>
	            <templateElseIf>
	                <lte>
	                    <variable identifier="var"/>
	                    <baseValue baseType="integer">8</baseValue>    
	                </lte>
	                <setCorrectResponse identifier="RESPONSE">
	                    <baseValue baseType="string">var is &lt;= 8</baseValue>
	                </setCorrectResponse>
	            </templateElseIf>
	            <templateElse>
	                <templateConstraint>
                        <baseValue baseType="boolean">true</baseValue>
	                </templateConstraint>
	            </templateElse>
	        </templateCondition>
	    ');
	    
	    $root = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $templateCondition = $root;
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    
	    // Entering branch (var <= 2).
	    $templateIf = $templateCondition->getTemplateIf();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	    $expression = $templateIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Lte', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    
	    // Entering branch (var <= 2 -----> var = 0)
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $templateCondition = $templateRules[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    $templateIf = $templateCondition->getTemplateIf();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	    $expression = $templateIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(0, $subExpressions[1]->getValue());
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertEquals(BaseType::STRING, $templateRules[0]->getExpression()->getBaseType());
	    $this->assertEquals('var is 0', $templateRules[0]->getExpression()->getValue());
	    
	    // Entering branch (var <= 2 -----> var = 1)
	    $templateElseIfs = $templateCondition->getTemplateElseIfs();
	    $templateElseIf = $templateElseIfs[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElseIf', $templateElseIf);
	    $expression = $templateElseIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(1, $subExpressions[1]->getValue());
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertEquals(BaseType::STRING, $templateRules[0]->getExpression()->getBaseType());
	    $this->assertEquals('var is 1', $templateRules[0]->getExpression()->getValue());
	    
	    // Entering branch (var <= 2 ----> else)
	    $this->assertTrue($templateCondition->hasTemplateElse());
	    $templateElse = $templateCondition->getTemplateElse();
	    $templateRules = $templateElse->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\ExitTemplate', $templateRules[0]);
	    
	    // Entering branch (var <= 5)
	    $templateCondition = $root;
	    $templateElseIfs = $templateCondition->getTemplateElseIfs();
	    $this->assertEquals(2, count($templateElseIfs));
	    
	    $templateElseIf = $templateElseIfs[0];
	    $expression = $templateElseIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Lte', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(5, $subExpressions[1]->getValue());
	    
	    // Entering branch (var <= 5 -----> var = 3)
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(2, count($templateRules));
	    
	    // There's a tiny little cute exitTemplate after the next else if... :)
	    $this->assertInstanceOf('qtism\\data\\rules\\ExitTemplate', $templateRules[1]);
	    
	    $templateCondition = $templateRules[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    $templateIf = $templateCondition->getTemplateIf();
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	    $expression = $templateIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(3, $subExpressions[1]->getValue());
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetCorrectResponse', $templateRules[0]);
	    $this->assertEquals('RESPONSE', $templateRules[0]->getIdentifier());
	    $this->assertEquals(BaseType::STRING, $templateRules[0]->getExpression()->getBaseType());
	    $this->assertEquals('jerome', $templateRules[0]->getExpression()->getValue());
	    
	    // Entering branch (var <= 5 -----> var = 4)
	    $templateElseIfs = $templateCondition->getTemplateElseIfs();
	    $templateElseIf = $templateElseIfs[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElseIf', $templateElseIf);
	    $expression = $templateElseIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(4, $subExpressions[1]->getValue());
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetDefaultValue', $templateRules[0]);
	    $this->assertEquals('qtism', $templateRules[0]->getExpression()->getValue());
	    $this->assertEquals(BaseType::STRING, $templateRules[0]->getExpression()->getBaseType());
	    
	    // Entering branch (var <= 5 -----> var = 5)
	    $templateElseIf = $templateElseIfs[1];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElseIf', $templateElseIf);
	    $expression = $templateElseIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(5, $subExpressions[1]->getValue());
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(2, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateConstraint', $templateRules[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	    $this->assertFalse($templateRules[0]->getExpression()->getValue());
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateConstraint', $templateRules[1]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[1]->getExpression());
	    $this->assertTrue($templateRules[1]->getExpression()->getValue());
	    
	    // Entering branch (var <= 8)
	    $templateCondition = $root;
	    $templateElseIfs = $templateCondition->getTemplateElseIfs();
	    $templateElseIf = $templateElseIfs[1];
	    $expression = $templateElseIf->getExpression();
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Lte', $expression);
	    $subExpressions = $expression->getExpressions();
	    $this->assertEquals(2, count($subExpressions));
	    $this->assertInstanceOf('qtism\\data\\expressions\\Variable', $subExpressions[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
	    $this->assertEquals(8, $subExpressions[1]->getValue());
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetCorrectResponse', $templateRules[0]);
	    $this->assertEquals('var is <= 8', $templateRules[0]->getExpression()->getValue());
	    
	    // Entering branch main else
	    $templateCondition = $root;
	    $templateElse = $templateCondition->getTemplateElse();
	    $templateRules = $templateElse->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateConstraint', $templateRules[0]);
	    $this->assertInternalType('boolean', $templateRules[0]->getExpression()->getValue());
	    $this->assertTrue($templateRules[0]->getExpression()->getValue());
	}
	
	public function testMarshallUnleashTheBeast() {
	    // Same structure as testUnmarshallUnleashTheBeast.
	    
	    // Building sub branch (var <= 2 -----> var = 0)
	    $expression = new Match(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 0))));
	    $templateIf = new TemplateIf($expression, new TemplateRuleCollection(array(new SetTemplateValue('tpl', new BaseValue(BaseType::STRING, 'var is 0')))));
	    
	    // building sub branch (var <= 2 -----> var = 1)
	    $expression = new Match(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 1))));
	    $templateElseIf = new TemplateElseIf($expression, new TemplateRuleCollection(array(new SetTemplateValue('tpl', new BaseValue(BaseType::STRING, 'var is 1')))));
	    
	    // building sub branch (var <= 2 -----> else)
	    $templateElse = new TemplateElse(new TemplateRuleCollection(array(new ExitTemplate())));
	    
	    // Building branch (var <= 2)
	    $expression = new Lte(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 2))));
	    $mainTemplateIf = new TemplateIf($expression, new TemplateRuleCollection(array(new TemplateCondition($templateIf, new TemplateElseIfCollection(array($templateElseIf)), $templateElse))));
	    
	    // Building sub branch (var <= 5 -----> var = 3)
	    $expression = new Match(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 3))));
	    $templateIf = new TemplateIf($expression, new TemplateRuleCollection(array(new SetCorrectResponse('RESPONSE', new BaseValue(BaseType::STRING, 'jerome')))));
	    
	    // Build sub branch (var <= 5 -----> var = 4)
	    $expression = new Match(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 4))));
	    $templateElseIf1 = new TemplateElseIf($expression, new TemplateRuleCollection(array(new SetDefaultValue('RESPONSE', new BaseValue(BaseType::STRING, 'qtism')))));
	    
	    // Building sub branch (var <= 5 -----> var = 5)
	    $expression = new Match(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 5))));
	    $templateElseIf2 = new TemplateElseIf($expression, new TemplateRuleCollection(array(new TemplateConstraint(new BaseValue(BaseType::BOOLEAN, false)), new TemplateConstraint(new BaseValue(BaseType::BOOLEAN, true)))));
	    
	    // Building branch (var <= 5)
	    $expression = new Lte(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 5))));
	    $mainTemplateElseIf1 = new TemplateElseIf($expression, new TemplateRuleCollection(array(new TemplateCondition($templateIf, new TemplateElseIfCollection(array($templateElseIf1, $templateElseIf2))), new ExitTemplate())));
	    
	    // Building branch (var <= 8)
	    $expression = new Lte(new ExpressionCollection(array(new Variable('var'), new BaseValue(BaseType::INTEGER, 8))));
	    $mainTemplateElseIf2 = new TemplateElseIf($expression, new TemplateRuleCollection(array(new SetCorrectResponse('RESPONSE', new BaseValue('RESPONSE', 'var is <= 8')))));
	    
	    // Build branch (else)
	    $mainTemplateElse = new TemplateElse(new TemplateRuleCollection(array(new TemplateConstraint(new BaseValue(BaseType::BOOLEAN, true)))));
	    
	    $templateCondition = new TemplateCondition($mainTemplateIf, new TemplateElseIfCollection(array($mainTemplateElseIf1, $mainTemplateElseIf2)), $mainTemplateElse);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($templateCondition)->marshall($templateCondition);
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateCondition><templateIf><lte><variable identifier="var"/><baseValue baseType="integer">2</baseValue></lte><templateCondition><templateIf><match><variable identifier="var"/><baseValue baseType="integer">0</baseValue></match><setTemplateValue identifier="tpl"><baseValue baseType="string">var is 0</baseValue></setTemplateValue></templateIf><templateElseIf><match><variable identifier="var"/><baseValue baseType="integer">1</baseValue></match><setTemplateValue identifier="tpl"><baseValue baseType="string">var is 1</baseValue></setTemplateValue></templateElseIf><templateElse><exitTemplate/></templateElse></templateCondition></templateIf><templateElseIf><lte><variable identifier="var"/><baseValue baseType="integer">5</baseValue></lte><templateCondition><templateIf><match><variable identifier="var"/><baseValue baseType="integer">3</baseValue></match><setCorrectResponse identifier="RESPONSE"><baseValue baseType="string">jerome</baseValue></setCorrectResponse></templateIf><templateElseIf><match><variable identifier="var"/><baseValue baseType="integer">4</baseValue></match><setDefaultValue identifier="RESPONSE"><baseValue baseType="string">qtism</baseValue></setDefaultValue></templateElseIf><templateElseIf><match><variable identifier="var"/><baseValue baseType="integer">5</baseValue></match><templateConstraint><baseValue baseType="boolean">false</baseValue></templateConstraint><templateConstraint><baseValue baseType="boolean">true</baseValue></templateConstraint></templateElseIf></templateCondition><exitTemplate/></templateElseIf><templateElseIf><lte><variable identifier="var"/><baseValue baseType="integer">8</baseValue></lte><setCorrectResponse identifier="RESPONSE"><baseValue baseType="identifier">var is &lt;= 8</baseValue></setCorrectResponse></templateElseIf><templateElse><templateConstraint><baseValue baseType="boolean">true</baseValue></templateConstraint></templateElse></templateCondition>', $dom->saveXML($element));
    }
}
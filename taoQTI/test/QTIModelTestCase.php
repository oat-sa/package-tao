<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */

require_once dirname(__FILE__).'/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__).'/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI
 * @subpackage test
 */
class QTIModelTestCase extends UnitTestCase
{

    protected $qtiService;

    /**
     * tests initialization
     * load qti service
     */
    public function setUp(){
        TaoTestRunner::initTest();
        $this->qtiService = taoQTI_models_classes_QTI_Service::singleton();
    }

    public function testModel(){

        $myItem = new taoQTI_models_classes_QTI_Item();
        $myItem->setAttribute('title', 'My Coolest Item');
        $myItem->getBody()->edit('sth');

        $myInteraction = new taoQTI_models_classes_QTI_interaction_ChoiceInteraction();
        $myInteraction->getPrompt()->edit('Prompt you');
        $myChoice1 = $myInteraction->createChoice(array('fixed' => true), 'This is correct');
        $myChoice2 = $myInteraction->createChoice(array('fixed' => true), 'This is not correct');
        $this->assertIsA($myChoice2, 'taoQTI_models_classes_QTI_choice_SimpleChoice');
        $this->assertEqual(count($myInteraction->getChoices()), 2);
        $myChoice1->setContent('answer #1');
        $myChoice2->setContent('answer #2');

        $myInteraction->removeChoice($myChoice1);
        $this->assertEqual(count($myInteraction->getChoices()), 1);

        $myItem->addInteraction($myInteraction, "Adding my interaction here {$myInteraction->getPlaceholder()}. And not there.");
        $this->assertNotNull($myInteraction->getRelatedItem());
        $this->assertEqual($myInteraction->getRelatedItem()->getSerial(), $myItem->getSerial());

        $myResponse = new taoQTI_models_classes_QTI_ResponseDeclaration();
        $myItem->addResponse($myResponse);
        $this->assertNotNull($myResponse->getRelatedItem());
        $this->assertEqual($myResponse->getRelatedItem()->getSerial(), $myItem->getSerial());

        $myItem->removeResponse($myResponse);
        $responses = $myItem->getResponses();
        $this->assertTrue(empty($responses));
    }

    public function testSimpleFeedback(){
        
        $response = new taoQTI_models_classes_QTI_ResponseDeclaration(array('identifier' => 'RESPONSE'));
//        $response->setHowMatch(taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT);
        $response->setHowMatch(taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE);
        $outcomeFeedback = new taoQTI_models_classes_QTI_OutcomeDeclaration(array('identifier' => 'FEEDBACK'));
        $modalFeedback1 = new taoQTI_models_classes_QTI_feedback_ModalFeedback(array('identifier' => 'feedbackOne'));
        $feebackRuleA = new taoQTI_models_classes_QTI_response_SimpleFeedbackRule($outcomeFeedback, $modalFeedback1);
        $feebackRuleA->setCondition($response, 'gte', 2.1);
        $output1 = $feebackRuleA->toQTI();

        $modalFeedback2 = new taoQTI_models_classes_QTI_feedback_ModalFeedback(array('identifier' => 'feedbackTwo'));
        $feebackRuleA->setFeedbackElse($modalFeedback2);
        $output2 = $feebackRuleA->toQTI();
        
        $doc = new DOMDocument();
        $doc->loadXML($output2);
//        var_dump($doc->saveXML());

        $data = simplexml_import_dom($doc);
        
        $subPatternFeedbackOperatorIf = '[name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [contains(name(./responseIf/*[1]/*[1]), "map")] [name(./responseIf/*[1]/*[2]) = "baseValue" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $subPatternFeedbackElse = '[name(./*[2]) = "responseElseIf"] [count(./responseElseIf/*) = 1 ] [name(./responseElseIf/*[1]) = "setOutcomeValue"] [name(./responseElseIf/setOutcomeValue/*[1]) = "baseValue"]';
        $patternFeedbackOperator = '/responseCondition [count(./*) = 1 ]'.$subPatternFeedbackOperatorIf;
        $patternFeedbackOperatorWithElse = '/responseCondition [count(./*) = 2 ]'.$subPatternFeedbackOperatorIf.$subPatternFeedbackElse;
        $match = $data->xpath($patternFeedbackOperatorWithElse);
//        var_dump($match);

        $operator = '';
        $responseIdentifier = '';
        $value = '';
        foreach($data->responseIf->children() as $child){
            $operator = $child->getName();
            $map = null;
            foreach($child->children() as $granChild){
                $map = $granChild->getName();
                $responseIdentifier = (string) $granChild['identifier'];
                break;
            }
            $value = (string) $child->baseValue;
            break;
        }
        
        $feedbackOutcomeIdentifier = (string) $data->responseIf->setOutcomeValue['identifier'];
        $feedbackIdentifier = (string) $data->responseIf->setOutcomeValue->baseValue;
        $feedbackIdentifierElse = (string) $data->responseElse->setOutcomeValue->baseValue;
        
        $this->assertEqual($feedbackOutcomeIdentifier, 'FEEDBACK');
        $this->assertEqual($feedbackIdentifier, 'feedbackOne');
        $this->assertEqual($map, 'mapResponse');
        $this->assertEqual($responseIdentifier, 'RESPONSE');
        $this->assertEqual($operator, 'gte');
        $this->assertEqual($value, '2.1');
        $this->assertEqual($feedbackIdentifierElse, 'feedbackTwo');
//        var_dump('found', $feedbackOutcomeIdentifier, $feedbackIdentifier, $map, $responseIdentifier, $operator, $value, $feedbackIdentifierElse);
        
    }
    
    public function testSimpleFeedbackCorrect(){
        
        $outcomeFeedback = new taoQTI_models_classes_QTI_OutcomeDeclaration(array('identifier' => 'FEEDBACK'));
        $response2 = new taoQTI_models_classes_QTI_ResponseDeclaration(array('identifier' => 'RESPONSE2'));
        $response2->setHowMatch(taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT);
        $modalFeedback3 = new taoQTI_models_classes_QTI_feedback_ModalFeedback(array('identifier' => 'feedbackThree'));
        $feebackRuleB = new taoQTI_models_classes_QTI_response_SimpleFeedbackRule($outcomeFeedback, $modalFeedback3);
        $feebackRuleB->setCondition($response2, 'correct');
        $output3 = $feebackRuleB->toQTI();
        
        $doc = new DOMDocument();
        $doc->loadXML($output3);
//        var_dump($doc->saveXML());
        
         $data = simplexml_import_dom($doc);
        $patternFeedbackCorrect = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/*[1]/*[1]) = "variable" ] [name(./responseIf/*[1]/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $match = $data->xpath($patternFeedbackCorrect);
//        var_dump($match);
        
        $responseIdentifier = (string) $data->responseIf->match->variable['identifier'];
        $feedbackOutcomeIdentifier = (string) $data->responseIf->setOutcomeValue['identifier'];
        $feedbackIdentifier = (string) $data->responseIf->setOutcomeValue->baseValue;
        
        $this->assertEqual($responseIdentifier, 'RESPONSE2');
        $this->assertEqual($feedbackOutcomeIdentifier, 'FEEDBACK');
        $this->assertEqual($feedbackIdentifier, 'feedbackThree');
        
//        var_dump('found', $responseIdentifier, $feedbackOutcomeIdentifier, $feedbackIdentifier);
    }
    
    /**
     * test the building of item from all the samples
     */
    public function testSamples(){

        //check if samples are loaded
        foreach(glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml') as $file){

            $qtiParser = new taoQTI_models_classes_QTI_Parser($file);

            $item = $qtiParser->load();
            $this->assertTrue($qtiParser->isValid());
            $this->assertNotNull($item);
            $this->assertIsA($item, 'taoQTI_models_classes_QTI_Item');

            foreach($item->getInteractions() as $interaction){
                $this->assertIsA($interaction, 'taoQTI_models_classes_QTI_interaction_Interaction');
                if($interaction instanceof taoQTI_models_classes_QTI_interaction_MatchInteraction){
                    foreach($interaction->getChoices(0) as $choice){
                        $this->assertIsA($choice, 'taoQTI_models_classes_QTI_choice_Choice');
                    }
                    foreach($interaction->getChoices(1) as $choice){
                        $this->assertIsA($choice, 'taoQTI_models_classes_QTI_choice_Choice');
                    }
                }else{
                    foreach($interaction->getChoices() as $choice){
                        $this->assertIsA($choice, 'taoQTI_models_classes_QTI_choice_Choice');
                    }
                }
            }

        }
    }

    /**
     * Generate sample json files
     */
    public function _testToJson(){
        $jsons = array();
        $outputDir = dirname(__FILE__).'/samples/json/';
        foreach(glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml') as $file){
            
            if(strpos($file, 'gap_match') === false){
//                continue;
            }
            
            $qtiParser = new taoQTI_models_classes_QTI_Parser($file);
            $item = $qtiParser->load();
            $data = $item->toArray();
            $jsons[$item->getIdentifier()] = $data;
            file_put_contents($outputDir.$item->getIdentifier().'.json', tao_helpers_Javascript::buildObject($data, true));
        }
        file_put_contents($outputDir.'ALL.json', tao_helpers_Javascript::buildObject($jsons, true));
    }

}
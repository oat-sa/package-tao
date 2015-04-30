<?php
/**
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\container\Container;
use oat\taoQtiItem\model\qti\exception\UnsupportedQtiElement;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\container\ContainerInteractive;
use oat\taoQtiItem\model\qti\container\ContainerItemBody;
use oat\taoQtiItem\model\qti\container\ContainerGap;
use oat\taoQtiItem\model\qti\container\ContainerHottext;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\response\Custom;
use oat\taoQtiItem\model\qti\interaction\BlockInteraction;
use oat\taoQtiItem\model\qti\interaction\ObjectInteraction;
use oat\taoQtiItem\model\qti\interaction\CustomInteraction;
use oat\taoQtiItem\model\qti\interaction\PortableCustomInteraction;
use oat\taoQtiItem\model\CustomInteractionRegistry;
use oat\taoQtiItem\model\qti\InfoControl;
use oat\taoQtiItem\model\qti\PortableInfoControl;
use oat\taoQtiItem\model\InfoControlRegistry;
use oat\taoQtiItem\model\qti\choice\ContainerChoice;
use oat\taoQtiItem\model\qti\choice\TextVariableChoice;
use oat\taoQtiItem\model\qti\choice\GapImg;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use oat\taoQtiItem\model\qti\response\Template;
use oat\taoQtiItem\model\qti\exception\UnexpectedResponseProcessing;
use oat\taoQtiItem\model\qti\response\TemplatesDriven;
use oat\taoQtiItem\model\qti\response\TakeoverFailedException;
use oat\taoQtiItem\model\qti\response\Summation;
use oat\taoQtiItem\model\qti\expression\ExpressionParserFactory;
use oat\taoQtiItem\model\qti\response\SimpleFeedbackRule;
use oat\taoQtiItem\model\qti\Object;
use oat\taoQtiItem\model\qti\Img;
use oat\taoQtiItem\model\qti\Math;
use oat\taoQtiItem\model\qti\Stylesheet;
use oat\taoQtiItem\model\qti\RubricBlock;
use oat\taoQtiItem\model\qti\container\ContainerFeedbackInteractive;
use oat\taoQtiItem\model\qti\container\ContainerStatic;
use \DOMDocument;
use \DOMXPath;
use \DOMElement;
use \common_Logger;
use \SimpleXMLElement;

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI

 */
class ParserFactory
{

    protected $data = null;
    protected $item = null;
    protected $qtiPrefix = '';
    protected $attributeMap = array('lang' => 'xml:lang');

    public function __construct(DOMDocument $data){
        $this->data = $data;
        $this->xpath = new DOMXPath($data);
    }

    public function load(){

        $item = null;

        if(!is_null($this->data)){
            $item = $this->buildItem($this->data->documentElement);
        }

        return $item;
    }

    protected function saveXML(DOMElement $data){
        return $data->ownerDocument->saveXML($data);
    }

    public function getBodyData(DOMElement $data, $removeNamespace = false){

        //prepare the data string
        $bodyData = $this->saveXML($data);

        $pattern = "/<{$data->nodeName}\b[^>]*\/>|<{$data->nodeName}\b[^>]*>(.*?)<\/{$data->nodeName}>/is";
        preg_match_all($pattern, $bodyData, $matches);
        if(isset($matches[1]) && isset($matches[1][0])){
            $bodyData = trim($matches[1][0]);
        }

        if($removeNamespace){
            $bodyData = preg_replace('/<(\/)?(\w*):/i', '<$1', $bodyData);
        }

        return $bodyData;
    }

    protected function replaceNode(DOMElement $node, Element $element){
        $placeholder = $this->data->createTextNode($element->getPlaceholder());
        $node->parentNode->replaceChild($placeholder, $node);
    }

    protected function deleteNode(DOMElement $node){
        $node->parentNode->removeChild($node);
    }

    protected function queryXPath($query, DOMElement $contextNode = null){

        $returnValue = $contextNode;
        if($this->qtiPrefix){
            
        }
        if(is_null($contextNode)){
            $returnValue = $this->xpath->query($query);
        }else{
            $returnValue = $this->xpath->query($query, $contextNode);
        }
        return $returnValue;
    }

    public function queryXPathChildren($paths = array(), DOMElement $contextNode = null, $ns = ''){
        $query = '.';
        $ns = empty($ns) ? '' : $ns.':';
        foreach($paths as $path){
            $query .= "/*[name(.)='".$ns.$path."']";
        }

        return $this->queryXPath($query, $contextNode);
    }

    protected function parseContainerStatic(DOMElement $data, Container $container){

        //var_dump('parseContainerStatic:'.$data->nodeName.':'.$data->asXML(), $bodyData);
        //initialize elements array to collect all QTI elements
        $bodyElements = array();

        //parse for feedback elements
        //warning: parse feddback elements before any other because ifeddbacks may contain them!
        $feedbackNodes = $this->queryXPath(".//*[not(ancestor::feedbackBlock) and not(ancestor::feedbackInline) and contains(name(.), 'feedback')]", $data);
        foreach($feedbackNodes as $feedbackNode){
            $feedback = $this->buildFeedback($feedbackNode);
            if(!is_null($feedback)){
                $bodyElements[$feedback->getSerial()] = $feedback;
                $this->replaceNode($feedbackNode, $feedback);
            }
        }

        // parse for QTI elements within item body

        $objectNodes = $this->queryXPath(".//*[name(.)='object']", $data);
        foreach($objectNodes as $objectNode){
            if(!in_array('object', $this->getAncestors($objectNode))){
                $object = $this->buildObject($objectNode);
                if(!is_null($object)){
                    $bodyElements[$object->getSerial()] = $object;

                    $this->replaceNode($objectNode, $object);
                }
            }
        }

        $imgNodes = $this->queryXPath(".//*[name(.)='img']", $data);
        foreach($imgNodes as $imgNode){
            $img = $this->buildImg($imgNode);
            if(!is_null($img)){
                $bodyElements[$img->getSerial()] = $img;

                $this->replaceNode($imgNode, $img);
            }
        }

        $ns = $this->getMathNamespace();
        $ns = empty($ns) ? '' : $ns.':';
        $mathNodes = $this->queryXPath(".//*[name(.)='".$ns."math']", $data);
        foreach($mathNodes as $mathNode){
            $math = $this->buildMath($mathNode);
            if(!is_null($math)){
                $bodyElements[$math->getSerial()] = $math;
                $this->replaceNode($mathNode, $math);
            }
        }

        $printedVariableNodes = $this->queryXPath(".//*[name(.)='printedVariable']", $data);
        foreach($printedVariableNodes as $printedVariableNode){
            //@todo: to be implemented
            throw new UnsupportedQtiElement($printedVariableNode);
            break;
        }

        $templateNodes = $this->queryXPath(".//*[name(.)='templateBlock'] | *[name(.)='templateInline']", $data);
        foreach($templateNodes as $templateNode){
            //@todo: to be implemented
            throw new UnsupportedQtiElement($templateNode);
            break;
        }

        //finally, add all body elements to the body
        $bodyData = $this->getBodyData($data);
        //there use to be $bodyData = ItemAuthoring::cleanHTML($bodyData); there

        if(empty($bodyElements)){
            $container->edit($bodyData);
        }elseif(!$container->setElements($bodyElements, $bodyData)){
            throw new ParsingException('Cannot set elements to the static container');
        }

        return $data;
    }

    protected function getAncestors(DOMElement $data, $topNode = 'itemBody'){
        $ancestors = array();
        $parentNodeName = '';
        $currentNode = $data;
        $i = 0;
        while(!is_null($currentNode->parentNode) && $parentNodeName != $topNode){

            if($i > 100){
                throw new ParsingException('maximum recursion of 100 reached');
            }

            $parentNodeName = $currentNode->parentNode->nodeName;
            $ancestors[] = $parentNodeName;
            $currentNode = $currentNode->parentNode;
            $i++;
        }
        return $ancestors;
    }

    protected function parseContainerInteractive(DOMElement $data, ContainerInteractive $container){

        $bodyElements = array();

        //var_dump('parseContainerInteractive:'.$data->nodeName.':'.$data->asXML(), $thisData);
        //parse the xml to find the interaction nodes
        $interactionNodes = $this->queryXPath(".//*[not(ancestor::feedbackBlock) and not(ancestor::feedbackInline) and contains(name(.), 'Interaction')]", $data);
        $debug = array($data->nodeName);
        foreach($interactionNodes as $k => $interactionNode){

            if(strpos($interactionNode->nodeName, 'portableCustomInteraction') === false){

                //build an interaction instance
                $interaction = $this->buildInteraction($interactionNode);
                $debug[] = $interactionNode->nodeName;

                if(!is_null($interaction)){
                    $bodyElements[$interaction->getSerial()] = $interaction;
                    $this->replaceNode($interactionNode, $interaction);
                }
            }
        }


        //parse for feedback elements interactive! 
        $feedbackNodes = $this->queryXPath(".//*[not(ancestor::feedbackBlock) and not(ancestor::feedbackInline) and contains(name(.), 'feedback')]", $data);
        foreach($feedbackNodes as $feedbackNode){
            $feedback = $this->buildFeedback($feedbackNode, true);
            if(!is_null($feedback)){
                $bodyElements[$feedback->getSerial()] = $feedback;
                $this->replaceNode($feedbackNode, $feedback);
            }
        }

        $bodyData = $this->getBodyData($data);
        foreach($bodyElements as $bodyElement){
            if(strpos($bodyData, $bodyElement->getPlaceholder()) === false){
                unset($bodyElements[$bodyElement->getSerial()]);
            }
        }
        if(!$container->setElements($bodyElements, $bodyData)){
            throw new ParsingException('Cannot set elements to the interactive container');
        }

        return $this->parseContainerStatic($data, $container);
    }

    protected function setContainerElements(Container $container, DOMElement $data, $bodyElements = array()){
        $bodyData = $this->getBodyData($data);
        foreach($bodyElements as $bodyElement){
            if(strpos($bodyData, $bodyElement->getPlaceholder()) === false){
                unset($bodyElements[$bodyElement->getSerial()]);
            }
        }
        if(!$container->setElements($bodyElements, $bodyData)){
            throw new ParsingException('Cannot set elements to the interactive container');
        }
    }

    protected function parseContainerItemBody(DOMElement $data, ContainerItemBody $container){

        $bodyElements = array();

        //parse for rubricBlocks: rubricBlock only allowed in item body !
        $rubricNodes = $this->queryXPath(".//*[name(.)='rubricBlock']", $data);
        foreach($rubricNodes as $rubricNode){
            $rubricBlock = $this->buildRubricBlock($rubricNode);
            if(!is_null($rubricBlock)){
                $bodyElements[$rubricBlock->getSerial()] = $rubricBlock;
                $this->replaceNode($rubricNode, $rubricBlock);
            }
        }
        
        //parse for infoControls: infoControl only allowed in item body !
        $infoControlNodes = $this->queryXPath(".//*[name(.)='infoControl']", $data);
        foreach($infoControlNodes as $infoControlNode){
            $infoControl = $this->buildInfoControl($infoControlNode);
            if(!is_null($infoControl)){
                $bodyElements[$infoControl->getSerial()] = $infoControl;
                $this->replaceNode($infoControlNode, $infoControl);
            }
        }
        
        $this->setContainerElements($container, $data, $bodyElements);

        return $this->parseContainerInteractive($data, $container);
    }

    private function parseContainerChoice(DOMElement $data, Container $container, $tag){

        $choices = array();
        $gapNodes = $this->queryXPath(".//*[name(.)='".$tag."']", $data);
        foreach($gapNodes as $gapNode){
            $gap = $this->buildChoice($gapNode);
            if(!is_null($gap)){
                $choices[$gap->getSerial()] = $gap;
                $this->replaceNode($gapNode, $gap);
            }
        }
        $bodyData = $this->getBodyData($data);
        $container->setElements($choices, $bodyData);

        $data = $this->parseContainerStatic($data, $container);

        return $data;
    }

    protected function parseContainerGap(DOMElement $data, ContainerGap $container){
        return $this->parseContainerChoice($data, $container, 'gap');
    }

    protected function parseContainerHottext(DOMElement $data, ContainerHottext $container){
        return $this->parseContainerChoice($data, $container, 'hottext');
    }

    protected function extractAttributes(DOMElement $data){
        $options = array();
        foreach($data->attributes as $attr){
            if($attr->nodeName === 'xsi:schemaLocation'){
                continue;
            }
            $options[isset($this->attributeMap[$attr->nodeName]) ? $this->attributeMap[$attr->nodeName] : $attr->nodeName] = (string) $attr->nodeValue;
        }
        return $options;
    }

    protected function getMathNamespace(){
        $returnValue = '';
        $namespaces = $this->item->getNamespaces();
        foreach($namespaces as $name => $uri){
            if(strpos($uri, 'MathML') > 0){
                $returnValue = $name;
                break;
            }
        }
        return $returnValue;
    }

    /**
     * Build a QTI_Item from a DOMElement, the root tag of which is root assessmentItem
     * 
     * @param DOMElement $data
     * @return oat\taoQtiItem\model\qti\Item
     * @throws oat\taoQtiItem\model\qti\exception\ParsingException
     */
    protected function buildItem(DOMElement $data){
        //check on the root tag.
        $itemId = (string) $data->getAttribute('identifier');

        common_Logger::i('Started parsing of QTI item'.(isset($itemId) ? ' '.$itemId : ''), array('TAOITEMS'));

        //create the item instance
        $this->item = new Item($this->extractAttributes($data));
        $namespaces = array();
        foreach($this->queryXPath('namespace::*') as $node){
            $name = preg_replace('/xmlns(:)?/', '', $node->nodeName);
            $this->item->addNamespace($name, $node->nodeValue);
        }
        $nsQti = $this->item->getNamespace('http://www.imsglobal.org/xsd/imsqti_v2p1');
        $this->qtiPrefix = $nsQti ? $nsQti.':' : '';

        $styleSheetNodes = $this->queryXPath("*[name(.) = 'stylesheet']", $data);
        foreach($styleSheetNodes as $styleSheetNode){
            $styleSheet = $this->buildStylesheet($styleSheetNode);
            $this->item->addStylesheet($styleSheet);
        }

        //extract the responses
        $responseNodes = $this->queryXPath("*[name(.) = 'responseDeclaration']", $data);
        foreach($responseNodes as $responseNode){
            $response = $this->buildResponseDeclaration($responseNode);
            if(!is_null($response)){
                $this->item->addResponse($response);
            }
        }

        //extract outcome variables
        $outcomes = array();
        $outComeNodes = $this->queryXPath("*[name(.) = 'outcomeDeclaration']", $data);
        foreach($outComeNodes as $outComeNode){
            $outcome = $this->buildOutcomeDeclaration($outComeNode);
            if(!is_null($outcome)){
                $outcomes[] = $outcome;
            }
        }
        if(count($outcomes) > 0){
            $this->item->setOutcomes($outcomes);
        }
        
        //extract modal feedbacks
        $feedbackNodes = $this->queryXPath("*[name(.) = 'modalFeedback']", $data);
        foreach($feedbackNodes as $feedbackNode){
            $modalFeedback = $this->buildFeedback($feedbackNode);
            if(!is_null($modalFeedback)){
                $this->item->addModalFeedback($modalFeedback);
            }
        }

        //extract the item structure to separate the structural/style content to the item content
        $itemBodies = $this->queryXPath("*[name(.) = 'itemBody']", $data); // array with 1 or zero bodies
        if($itemBodies === false){
            $errors = libxml_get_errors();
            if(count($errors) > 0){
                $error = array_shift($errors);
                $errormsg = $error->message;
            }else{
                $errormsg = "without errormessage";
            }
            throw new ParsingException('XML error('.$errormsg.') on itemBody read'.(isset($itemId) ? ' for item '.$itemId : ''));
        }elseif($itemBodies->length){
            $this->parseContainerItemBody($itemBodies->item(0), $this->item->getBody());
        }


        //warning: extract the response processing at the latest to make oat\taoQtiItem\model\qti\response\TemplatesDriven::takeOverFrom() work
        $rpNodes = $this->queryXPath("*[name(.) = 'responseProcessing']", $data);
        if($rpNodes->length === 0){
            common_Logger::i('No responseProcessing found for QTI Item, setting empty custom', array('QTI', 'TAOITEMS'));
            $customrp = new Custom(array(), '<responseProcessing/>');
            $this->item->setResponseProcessing($customrp);
        }else{
            $rpNode = $rpNodes->item(0); //the node should be alone
            $rProcessing = $this->buildResponseProcessing($rpNode, $this->item);
            if(!is_null($rProcessing)){
                $this->item->setResponseProcessing($rProcessing);
            }
        }

        return $this->item;
    }

    /**
     * Build a QTI_Interaction from a DOMElement (the root tag of this
     * is an 'interaction' node)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\interaction\Interaction
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
     */
    protected function buildInteraction(DOMElement $data){

        $returnValue = null;

        if($data->nodeName === 'customInteraction'){

            $returnValue = $this->buildCustomInteraction($data);
        }else{

            //build one of the standard interaction

            try{

                $type = ucfirst($data->nodeName);

                $interactionClass = '\\oat\\taoQtiItem\\model\\qti\\interaction\\'.$type;
                if(!class_exists($interactionClass)){
                    throw new ParsingException('The interaction class cannot be found: '.$interactionClass);
                }

                $myInteraction = new $interactionClass($this->extractAttributes($data), $this->item);

                if($myInteraction instanceof BlockInteraction){
                    //extract prompt:
                    $promptNodes = $this->queryXPath("*[name(.) = 'prompt']", $data); //prompt

                    foreach($promptNodes as $promptNode){
                        //only block interactions have prompt
                        $this->parseContainerStatic($promptNode, $myInteraction->getPrompt());
                        $this->deleteNode($promptNode);
                    }
                }

                //build the interaction's choices regarding it's type
                switch(strtolower($type)){

                    case 'matchinteraction':
                        //extract simpleMatchSet choices
                        $matchSetNodes = $this->queryXPath("*[name(.) = 'simpleMatchSet']", $data); //simpleMatchSet
                        $matchSetNumber = 0;
                        foreach($matchSetNodes as $matchSetNode){
                            $choiceNodes = $this->queryXPath("*[name(.) = 'simpleAssociableChoice']", $matchSetNode); //simpleAssociableChoice
                            foreach($choiceNodes as $choiceNode){
                                $choice = $this->buildChoice($choiceNode);
                                if(!is_null($choice)){
                                    $myInteraction->addChoice($choice, $matchSetNumber);
                                }
                            }
                            if(++$matchSetNumber === 2){
                                //matchSet is limited to 2 maximum
                                break;
                            }
                        }
                        break;

                    case 'gapmatchinteraction':
                        //create choices with the gapText nodes
                        $choiceNodes = $this->queryXPath("*[name(.)='gapText']", $data); //or gapImg!!
                        $choices = array();
                        foreach($choiceNodes as $choiceNode){
                            $choice = $this->buildChoice($choiceNode);
                            if(!is_null($choice)){
                                $myInteraction->addChoice($choice);
                                $this->deleteNode($choiceNode);
                            }

                            //remove node so it does not pollute subsequent parsing data
                            unset($choiceNode);
                        }

                        $this->parseContainerGap($data, $myInteraction->getBody());
                        break;

                    case 'hottextinteraction':
                        $this->parseContainerHottext($data, $myInteraction->getBody());
                        break;

                    case 'graphicgapmatchinteraction':
                        //create choices with the gapImg nodes
                        $choiceNodes = $this->queryXPath("*[name(.)='gapImg']", $data);
                        $choices = array();
                        foreach($choiceNodes as $choiceNode){
                            $choice = $this->buildChoice($choiceNode);
                            if(!is_null($choice)){
                                $myInteraction->addGapImg($choice);
                            }
                        }
                    default :
                        //parse, extract and build the choice nodes contained in the interaction
                        $exp = "*[contains(name(.),'Choice')] | *[name(.)='associableHotspot']";
                        $choiceNodes = $this->queryXPath($exp, $data);
                        foreach($choiceNodes as $choiceNode){
                            $choice = $this->buildChoice($choiceNode);
                            if(!is_null($choice)){
                                $myInteraction->addChoice($choice);
                            }
                            unset($choiceNode);
                        }
                        break;
                }

                if($myInteraction instanceof ObjectInteraction){
                    $objectNodes = $this->queryXPath("*[name(.)='object']", $data); //object
                    foreach($objectNodes as $objectNode){
                        $object = $this->buildObject($objectNode);
                        if(!is_null($object)){
                            $myInteraction->setObject($object);
                        }
                    }
                }

                $returnValue = $myInteraction;
            }catch(InvalidArgumentException $iae){
                throw new ParsingException($iae);
            }
        }

        return $returnValue;
    }

    /**
     * Build a QTI_Choice from a DOMElement (the root tag of this element
     * an 'choice' node)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\choice\Choice
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
     */
    protected function buildChoice(DOMElement $data){

        $className = '\\oat\\taoQtiItem\\model\\qti\\choice\\'.ucfirst($data->nodeName);
        if(!class_exists($className)){
            throw new ParsingException("The choice class does not exist ".$className);
        }

        $myChoice = new $className($this->extractAttributes($data));

        if($myChoice instanceof ContainerChoice){
            $this->parseContainerStatic($data, $myChoice->getBody());
        }elseif($myChoice instanceof TextVariableChoice){
            $myChoice->setContent($data->nodeValue);
        }elseif($myChoice instanceof GapImg){
            //extract the media object tag
            $objectNodes = $this->queryXPath("*[name(.)='object']", $data);
            foreach($objectNodes as $objectNode){
                $object = $this->buildObject($objectNode);
                $myChoice->setContent($object);
                break;
            }
        }

        return $myChoice;
    }

    /**
     * Short description of method buildResponseDeclaration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\ResponseDeclaration
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
     */
    protected function buildResponseDeclaration(DOMElement $data){

        $myResponse = new ResponseDeclaration($this->extractAttributes($data), $this->item);

        $data = simplexml_import_dom($data);
        //set the correct responses
        $correctResponseNodes = $data->xpath("*[name(.) = 'correctResponse']");
        $responses = array();
        foreach($correctResponseNodes as $correctResponseNode){
            foreach($correctResponseNode->value as $value){
                $correct = (string) $value;
                $responses[] = htmlentities($correct);
            }
            break;
        }
        $myResponse->setCorrectResponses($responses);

        //set the mapping if defined
        $mappingNodes = $data->xpath("*[name(.) = 'mapping']");
        foreach($mappingNodes as $mappingNode){

            if(isset($mappingNode['defaultValue'])){
                $myResponse->setMappingDefaultValue(floatval((string) $mappingNode['defaultValue']));
            }
            $mappingOptions = array();
            foreach($mappingNode->attributes() as $key => $value){
                if($key != 'defaultValue'){
                    $mappingOptions[$key] = (string) $value;
                }
            }
            $myResponse->setAttribute('mapping', $mappingOptions);

            $mapping = array();
            foreach($mappingNode->mapEntry as $mapEntry){
                $mapping[(string) $mapEntry['mapKey']] = (string) $mapEntry['mappedValue'];
            }
            $myResponse->setMapping($mapping);

            break;
        }

        //set the areaMapping if defined
        $mappingNodes = $data->xpath("*[name(.) = 'areaMapping']");
        foreach($mappingNodes as $mappingNode){

            if(isset($mappingNode['defaultValue'])){
                $myResponse->setMappingDefaultValue(floatval((string) $mappingNode['defaultValue']));
            }
            $mappingOptions = array();
            foreach($mappingNode->attributes() as $key => $value){
                if($key != 'defaultValue'){
                    $mappingOptions[$key] = (string) $value;
                }
            }
            $myResponse->setAttribute('areaMapping', $mappingOptions);

            $mapping = array();
            foreach($mappingNode->areaMapEntry as $mapEntry){
                $mappingAttributes = array();
                foreach($mapEntry->attributes() as $key => $value){
                    $mappingAttributes[(string) $key] = (string) $value;
                }
                $mapping[] = $mappingAttributes;
            }
            $myResponse->setMapping($mapping, 'area');

            break;
        }

        return $myResponse;
    }

    /**
     * Short description of method buildOutcomeDeclaration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\OutcomeDeclaration
     */
    protected function buildOutcomeDeclaration(DOMElement $data){

        $outcome = new OutcomeDeclaration($this->extractAttributes($data));
        $data = simplexml_import_dom($data);

        if(isset($data->defaultValue)){
            if(!is_null($data->defaultValue->value)){
                $outcome->setDefaultValue((string) $data->defaultValue->value);
            }
        }

        return $outcome;
    }

    /**
     * Short description of method buildTemplateResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    protected function buildTemplateResponseProcessing(DOMElement $data){
        $returnValue = null;

        if($data->hasAttribute('template') && $data->childNodes->length === 0){
            $templateUri = (string) $data->getAttribute('template');
            $returnValue = new Template($templateUri);
        }elseif($data->childNodes->length === 1){
            $patternCorrectIMS = 'responseCondition [count(./*) = 2 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/match/*[1]) = "variable" ] [name(./responseIf/match/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ] [name(./*[2]) = "responseElse" ] [count(./responseElse/*) = 1 ] [name(./responseElse/*[1]) = "setOutcomeValue" ] [name(./responseElse/setOutcomeValue/*[1]) = "baseValue"]';
            $patternMappingIMS = 'responseCondition [count(./*) = 2] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "isNull"] [name(./responseIf/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "variable"] [name(./*[2]) = "responseElse"] [count(./responseElse/*) = 1] [name(./responseElse/*[1]) = "setOutcomeValue"] [name(./responseElse/setOutcomeValue/*[1]) = "mapResponse"]';
            $patternMappingPointIMS = 'responseCondition [count(./*) = 2] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "isNull"] [name(./responseIf/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "variable"] [name(./*[2]) = "responseElse"] [count(./responseElse/*) = 1] [name(./responseElse/*[1]) = "setOutcomeValue"] [name(./responseElse/setOutcomeValue/*[1]) = "mapResponsePoint"]';
            if(count($this->queryXPath($patternCorrectIMS)) == 1){
                $returnValue = new Template(Template::MATCH_CORRECT);
            }elseif(count($this->queryXPath($patternMappingIMS)) == 1){
                $returnValue = new Template(Template::MAP_RESPONSE);
            }elseif(count($this->queryXPath($patternMappingPointIMS)) == 1){
                $returnValue = new Template(Template::MAP_RESPONSE_POINT);
            }else{
                throw new UnexpectedResponseProcessing('not Template, wrong rule');
            }
            $returnValue->setRelatedItem($this->item);
        }else{
            throw new UnexpectedResponseProcessing('not Template');
        }

        return $returnValue;
    }

    /**
     * Short description of method buildResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @param  Item item
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    protected function buildResponseProcessing(DOMElement $data, Item $item){
        $returnValue = null;

        // try template
        try{
            $returnValue = $this->buildTemplateResponseProcessing($data);

            try{
                //warning: require to add interactions to the item to make it work
                $returnValue = TemplatesDriven::takeOverFrom($returnValue, $item);
                common_Logger::d('Processing is Template converted to TemplateDriven', array('TAOITEMS', 'QTI'));
            }catch(TakeoverFailedException $e){
                common_Logger::d('Processing is Template', array('TAOITEMS', 'QTI'));
            }
        }catch(UnexpectedResponseProcessing $e){
            
        }
        //try templatedriven
        if(is_null($returnValue)){
            try{
                $returnValue = $this->buildTemplatedrivenResponse($data, $item->getInteractions());
                common_Logger::d('Processing is TemplateDriven', array('TAOITEMS', 'QTI'));
            }catch(UnexpectedResponseProcessing $e){
                
            }
        }

        // build custom
        if(is_null($returnValue)){
            try{
                $returnValue = $this->buildCustomResponseProcessing($data);
                common_Logger::d('ResponseProcessing is custom');
            }catch(UnexpectedResponseProcessing $e){
                // not a Template
                common_Logger::e('custom response processing failed, should never happen', array('TAOITEMS', 'QTI'));
            }
        }

        if(is_null($returnValue)){
            common_Logger::d('failled to determin ResponseProcessing');
        }

        return $returnValue;
    }

    /**
     * Short description of method buildCompositeResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @param  Item item
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    protected function buildCompositeResponseProcessing(DOMElement $data, Item $item){
        $returnValue = null;

        // STRONGLY simplified summation detection
        $patternCorrectTAO = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/match/*[1]) = "variable" ] [name(./responseIf/match/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue"]';
        $patternMapTAO = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "not" ] [count(./responseIf/not/*) = 1 ] [name(./responseIf/not/*[1]) = "isNull" ] [count(./responseIf/not/isNull/*) = 1 ] [name(./responseIf/not/isNull/*[1]) = "variable" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "mapResponse"]';
        $patternMapPointTAO = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "not" ] [count(./responseIf/not/*) = 1 ] [name(./responseIf/not/*[1]) = "isNull" ] [count(./responseIf/not/isNull/*) = 1 ] [name(./responseIf/not/isNull/*[1]) = "variable" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "mapResponsePoint"]';
        $patternNoneTAO = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "isNull" ] [count(./responseIf/isNull/*) = 1 ] [name(./responseIf/isNull/*[1]) = "variable" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue"]';
        $possibleSummation = '/setOutcomeValue [count(./*) = 1 ] [name(./*[1]) = "sum" ]';

        $irps = array();
        $composition = null;
        $data = simplexml_import_dom($data);
        foreach($data as $responseRule){

            if(!is_null($composition)){
                throw new UnexpectedResponseProcessing('Not composite, rules after composition');
            }

            $subtree = new SimpleXMLElement($responseRule->asXML());

            if(count($subtree->xpath($patternCorrectTAO)) > 0){
                $responseIdentifier = (string) $subtree->responseIf->match->variable[0]['identifier'];
                $irps[$responseIdentifier] = array(
                    'class' => 'MatchCorrectTemplate',
                    'outcome' => (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
                );
            }elseif(count($subtree->xpath($patternMapTAO)) > 0){
                $responseIdentifier = (string) $subtree->responseIf->not->isNull->variable[0]['identifier'];
                $irps[$responseIdentifier] = array(
                    'class' => 'MapResponseTemplate',
                    'outcome' => (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
                );
            }elseif(count($subtree->xpath($patternMapPointTAO)) > 0){
                $responseIdentifier = (string) $subtree->responseIf->not->isNull->variable[0]['identifier'];
                $irps[$responseIdentifier] = array(
                    'class' => 'MapResponsePointTemplate',
                    'outcome' => (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
                );
            }elseif(count($subtree->xpath($patternNoneTAO)) > 0){
                $responseIdentifier = (string) $subtree->responseIf->isNull->variable[0]['identifier'];
                $irps[$responseIdentifier] = array(
                    'class' => 'None',
                    'outcome' => (string) $subtree->responseIf->setOutcomeValue[0]['identifier'],
                    'default' => (string) $subtree->responseIf->setOutcomeValue[0]->baseValue[0]
                );
            }elseif(count($subtree->xpath($possibleSummation)) > 0){
                $composition = 'Summation';
                $outcomesUsed = array();
                foreach($subtree->xpath('/setOutcomeValue/sum/variable') as $var){
                    $outcomesUsed[] = (string) $var[0]['identifier'];
                }
            }else{
                throw new UnexpectedResponseProcessing('Not composite, unknown rule');
            }
        }

        if(is_null($composition)){
            throw new UnexpectedResponseProcessing('Not composit, Composition rule missing');
        }

        $responses = array();
        foreach($item->getInteractions() as $interaction){
            $responses[$interaction->getResponse()->getIdentifier()] = $interaction->getResponse();
        }

        if(count(array_diff(array_keys($irps), array_keys($responses))) > 0){
            throw new UnexpectedResponseProcessing('Not composit, no responses for rules: '.implode(',', array_diff(array_keys($irps), array_keys($responses))));
        }
        if(count(array_diff(array_keys($responses), array_keys($irps))) > 0){
            common_Logger::w('Some responses have no processing');
            throw new UnexpectedResponseProcessing('Not composit, no support for unmatched variables yet');
        }

        //assuming sum is correct

        $compositonRP = new Summation($item);
        foreach($responses as $id => $response){
            $outcome = null;
            foreach($item->getOutcomes() as $possibleOutcome){
                if($possibleOutcome->getIdentifier() == $irps[$id]['outcome']){
                    $outcome = $possibleOutcome;
                    break;
                }
            }
            if(is_null($outcome)){
                throw new ParsingException('Undeclared Outcome in ResponseProcessing');
            }
            $classname = '\\oat\\taoQtiItem\\model\\qti\\response\\interactionResponseProcessing\\'.$irps[$id]['class'];
            $irp = new $classname($response, $outcome);
            if($irp instanceof \oat\taoQtiItem\model\qti\response\interactionResponseProcessing\None && isset($irps[$id]['default'])){
                $irp->setDefaultValue($irps[$id]['default']);
            }
            $compositonRP->add($irp);
        }
        $returnValue = $compositonRP;

        return $returnValue;
    }

    /**
     * Short description of method buildCustomResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    protected function buildCustomResponseProcessing(DOMElement $data){

        // Parse to find the different response rules
        $responseRules = array();

        $data = simplexml_import_dom($data);

        $returnValue = new Custom($responseRules, $data->asXml());

        return $returnValue;
    }

    /**
     * Short description of method buildExpression
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\response\Rule
     */
    protected function buildExpression(DOMElement $data){
        $data = simplexml_import_dom($data);
        return ExpressionParserFactory::build($data);
    }

    protected function getModalFeedback($identifier){
        foreach($this->item->getModalFeedbacks() as $feedback){
            if($feedback->getIdentifier() == $identifier){
                return $feedback;
            }
        }
        throw new ParsingException('cannot found the modal feedback with identifier '.$identifier);
    }

    protected function getOutcome($identifier){
        foreach($this->item->getOutcomes() as $outcome){
            if($outcome->getIdentifier() == $identifier){
                return $outcome;
            }
        }
        throw new ParsingException('cannot found the outcome with identifier '.$identifier);
    }

    protected function getResponse($identifier){
        foreach($this->item->getResponses() as $response){
            if($response->getIdentifier() == $identifier){
                return $response;
            }
        }
        throw new ParsingException('cannot found the response with identifier '.$identifier);
    }

    /**
     * Short description of method buildTemplatedrivenResponse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @param  array interactions
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    protected function buildTemplatedrivenResponse(DOMElement $data, $interactions){

        $patternCorrectTAO = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/match/*[1]) = "variable" ] [name(./responseIf/match/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "sum" ] [name(./responseIf/setOutcomeValue/sum/*[1]) = "variable" ] [name(./responseIf/setOutcomeValue/sum/*[2]) = "baseValue"]';
        $patternMappingTAO = '/responseCondition [count(./*) = 1] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "not"] [name(./responseIf/not/*[1]) = "isNull"] [name(./responseIf/not/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "sum"] [name(./responseIf/setOutcomeValue/sum/*[1]) = "variable"] [name(./responseIf/setOutcomeValue/sum/*[2]) = "mapResponse"]';
        $patternMappingPointTAO = '/responseCondition [count(./*) = 1] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "not"] [name(./responseIf/not/*[1]) = "isNull"] [name(./responseIf/not/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "sum"] [name(./responseIf/setOutcomeValue/sum/*[1]) = "variable"] [name(./responseIf/setOutcomeValue/sum/*[2]) = "mapResponsePoint"]';

        $subPatternFeedbackOperatorIf = '[name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [contains(name(./responseIf/*[1]/*[1]), "map")] [name(./responseIf/*[1]/*[2]) = "baseValue" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $subPatternFeedbackElse = '[name(./*[2]) = "responseElse"] [count(./responseElse/*) = 1 ] [name(./responseElse/*[1]) = "setOutcomeValue"] [name(./responseElse/setOutcomeValue/*[1]) = "baseValue"]';
        $subPatternFeedbackCorrect = '[name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/*[1]/*[1]) = "variable" ] [name(./responseIf/*[1]/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $subPatternFeedbackIncorrect = '[name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "not" ] [count(./responseIf/not) = 1 ] [name(./responseIf/not/*[1]) = "match" ] [name(./responseIf/not/*[1]/*[1]) = "variable" ] [name(./responseIf/not/*[1]/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ]';
        $patternFeedbackOperator = '/responseCondition [count(./*) = 1 ]'.$subPatternFeedbackOperatorIf;
        $patternFeedbackOperatorWithElse = '/responseCondition [count(./*) = 2 ]'.$subPatternFeedbackOperatorIf.$subPatternFeedbackElse;
        $patternFeedbackCorrect = '/responseCondition [count(./*) = 1 ]'.$subPatternFeedbackCorrect;
        $patternFeedbackCorrectWithElse = '/responseCondition [count(./*) = 2 ]'.$subPatternFeedbackCorrect.$subPatternFeedbackElse;
        $patternFeedbackIncorrect = '/responseCondition [count(./*) = 1 ]'.$subPatternFeedbackIncorrect;
        $patternFeedbackIncorrectWithElse = '/responseCondition [count(./*) = 2 ]'.$subPatternFeedbackIncorrect.$subPatternFeedbackElse;

        $rules = array();
        $simpleFeedbackRules = array();
        $data = simplexml_import_dom($data);

        foreach($data as $responseRule){
            $subtree = new SimpleXMLElement($responseRule->asXML());

            if(count($subtree->xpath($patternCorrectTAO)) > 0){

                $responseIdentifier = (string) $subtree->responseIf->match->variable['identifier'];
                $rules[$responseIdentifier] = Template::MATCH_CORRECT;
            }elseif(count($subtree->xpath($patternMappingTAO)) > 0){

                $responseIdentifier = (string) $subtree->responseIf->not->isNull->variable['identifier'];
                $rules[$responseIdentifier] = Template::MAP_RESPONSE;
            }elseif(count($subtree->xpath($patternMappingPointTAO)) > 0){

                $responseIdentifier = (string) $subtree->responseIf->not->isNull->variable['identifier'];
                $rules[$responseIdentifier] = Template::MAP_RESPONSE_POINT;
            }elseif(count($subtree->xpath($patternFeedbackCorrect)) > 0 || count($subtree->xpath($patternFeedbackCorrectWithElse)) > 0){

                $responseIdentifier = (string) $subtree->responseIf->match->variable['identifier'];
                $feedbackOutcomeIdentifier = (string) $subtree->responseIf->setOutcomeValue['identifier'];
                $feedbackIdentifier = (string) $subtree->responseIf->setOutcomeValue->baseValue;

                try{

                    $response = $this->getResponse($responseIdentifier);
                    $outcome = $this->getOutcome($feedbackOutcomeIdentifier);
                    $feedbackThen = $this->getModalFeedback($feedbackIdentifier);

                    $feedbackElse = null;
                    if($subtree->responseElse->getName()){
                        $feedbackElseIdentifier = (string) $subtree->responseElse->setOutcomeValue->baseValue;
                        $feedbackElse = $this->getModalFeedback($feedbackElseIdentifier);
                    }

                    $feedbackRule = new SimpleFeedbackRule($outcome, $feedbackThen, $feedbackElse);
                    $feedbackRule->setCondition($response, 'correct');


                    if(!isset($simpleFeedbackRules[$responseIdentifier])){
                        $simpleFeedbackRules[$responseIdentifier] = array();
                    }
                    $simpleFeedbackRules[$responseIdentifier][] = $feedbackRule;
                }catch(ParsingException $e){
                    throw new UnexpectedResponseProcessing('Feedback resources not found. Not template driven, unknown rule');
                }
            }elseif(count($subtree->xpath($patternFeedbackIncorrect)) > 0 || count($subtree->xpath($patternFeedbackIncorrectWithElse)) > 0){

                $responseIdentifier = (string) $subtree->responseIf->not->match->variable['identifier'];
                $feedbackOutcomeIdentifier = (string) $subtree->responseIf->setOutcomeValue['identifier'];
                $feedbackIdentifier = (string) $subtree->responseIf->setOutcomeValue->baseValue;

                try{

                    $response = $this->getResponse($responseIdentifier);
                    $outcome = $this->getOutcome($feedbackOutcomeIdentifier);
                    $feedbackThen = $this->getModalFeedback($feedbackIdentifier);

                    $feedbackElse = null;
                    if($subtree->responseElse->getName()){
                        $feedbackElseIdentifier = (string) $subtree->responseElse->setOutcomeValue->baseValue;
                        $feedbackElse = $this->getModalFeedback($feedbackElseIdentifier);
                    }

                    $feedbackRule = new SimpleFeedbackRule($outcome, $feedbackThen, $feedbackElse);
                    $feedbackRule->setCondition($response, 'incorrect');


                    if(!isset($simpleFeedbackRules[$responseIdentifier])){
                        $simpleFeedbackRules[$responseIdentifier] = array();
                    }
                    $simpleFeedbackRules[$responseIdentifier][] = $feedbackRule;
                }catch(ParsingException $e){
                    throw new UnexpectedResponseProcessing('Feedback resources not found. Not template driven, unknown rule');
                }
            }elseif(count($subtree->xpath($patternFeedbackOperator)) > 0 || count($subtree->xpath($patternFeedbackOperatorWithElse)) > 0){

                $operator = '';
                $responseIdentifier = '';
                $value = '';
                foreach($subtree->responseIf->children() as $child){
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

                $feedbackOutcomeIdentifier = (string) $subtree->responseIf->setOutcomeValue['identifier'];
                $feedbackIdentifier = (string) $subtree->responseIf->setOutcomeValue->baseValue;

                try{

                    $response = $this->getResponse($responseIdentifier);
                    $outcome = $this->getOutcome($feedbackOutcomeIdentifier);
                    $feedbackThen = $this->getModalFeedback($feedbackIdentifier);
                    $feedbackElse = null;
                    if($subtree->responseElse->getName()){
                        $feedbackElseIdentifier = (string) $subtree->responseElse->setOutcomeValue->baseValue;
                        $feedbackElse = $this->getModalFeedback($feedbackElseIdentifier);
                    }

                    $feedbackRule = new SimpleFeedbackRule($outcome, $feedbackThen, $feedbackElse);
                    $feedbackRule->setCondition($response, $operator, $value);
                    if(!isset($simpleFeedbackRules[$responseIdentifier])){
                        $simpleFeedbackRules[$responseIdentifier] = array();
                    }
                    $simpleFeedbackRules[$responseIdentifier][] = $feedbackRule;
                }catch(ParsingException $e){
                    throw new UnexpectedResponseProcessing('Feedback resources not found. Not template driven, unknown rule');
                }
            }else{
                throw new UnexpectedResponseProcessing('Not template driven, unknown rule');
            }

            //add simple feedback rules parsing here
        }

        $responseIdentifiers = array();
        foreach($interactions as $interaction){
            $interactionResponse = $interaction->getResponse();
            $responseIdentifier = $interactionResponse->getIdentifier();
            $responseIdentifiers[] = $responseIdentifier;

            //create and set simple feedback rule here
            if(isset($simpleFeedbackRules[$responseIdentifier])){
                foreach($simpleFeedbackRules[$responseIdentifier] as $rule){
                    $interactionResponse->addFeedbackRule($rule);
                }
            }
        }

        if(count(array_diff($responseIdentifiers, array_keys($rules))) > 0 || count(array_diff(array_keys($rules), $responseIdentifiers)) > 0){
            throw new UnexpectedResponseProcessing('Not template driven, responseIdentifiers are '.implode(',', $responseIdentifiers).' while rules are '.implode(',', array_keys($rules)));
        }

        $templatesDrivenRP = new TemplatesDriven();
        foreach($interactions as $interaction){
            $pattern = $rules[$interaction->getResponse()->getIdentifier()];
            $templatesDrivenRP->setTemplate($interaction->getResponse(), $pattern);
        }
        $templatesDrivenRP->setRelatedItem($this->item);
        $returnValue = $templatesDrivenRP;

        return $returnValue;
    }

    /**
     * Short description of method buildObject
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DOMElement data
     * @return oat\taoQtiItem\model\qti\Object
     */
    private function buildObject(DOMElement $data){

        $attributes = $this->extractAttributes($data);
        $returnValue = new Object($attributes);

        if($data->hasChildNodes()){
            $nonEmptyChild = $this->getNonEmptyChildren($data);
            if(count($nonEmptyChild) == 1 && reset($nonEmptyChild)->nodeName == 'object'){
                $alt = $this->buildObject(reset($nonEmptyChild));
                $returnValue->setAlt($alt);
            }else{
                //get the node xml content
                $pattern = array("/^<{$data->nodeName}([^>]*)?>/i", "/<\/{$data->nodeName}([^>]*)?>$/i");
                $content = preg_replace($pattern, '', trim($this->saveXML($data)));
                $returnValue->setAlt($content);
            }
        }else{
            $alt = trim($data->nodeValue);
            if(!empty($alt)){
                $returnValue->setAlt($alt);
            }
        }

        return $returnValue;
    }

    private function buildImg(DOMElement $data){

        $attributes = $this->extractAttributes($data);
        $returnValue = new Img($attributes);

        return $returnValue;
    }

    private function buildMath(DOMElement $data){

        $ns = $this->getMathNamespace();
        $annotationNodes = $this->queryXPath(".//*[name(.)='".(empty($ns) ? '' : $ns.':')."annotation']", $data);
        $annotations = array();
        //need to extract the namespace, and clean it in the "bodydata"
        foreach($annotationNodes as $annotationNode){
            $attr = $this->extractAttributes($annotationNode);
            $encoding = isset($attr['encoding']) ? strtolower(trim($attr['encoding'])) : '';
            $str = $this->getBodyData($annotationNode);
            if(!empty($encoding) && !empty($str)){
                $annotations[$encoding] = $str;
                $this->deleteNode($annotationNode);
            }
        }

        $math = new Math($this->extractAttributes($data));
        $body = $this->getBodyData($data, true);
        $math->setMathML($body);
        $math->setAnnotations($annotations);

        return $math;
    }

    protected function getNonEmptyChildren(DOMElement $data){
        $returnValue = array();
        foreach($data->childNodes as $childNode){
            if($childNode->nodeName == '#text'){
                if(trim($childNode->nodeValue) != ''){
                    $returnValue[] = $childNode;
                }
            }else{
                $returnValue[] = $childNode;
            }
        }
        return $returnValue;
    }

    private function buildStylesheet(DOMElement $data){
        $returnValue = new Stylesheet(array(
            'href' => (string) $data->getAttribute('href'),
            'title' => $data->hasAttribute('title') ? (string) $data->getAttribute('title') : '',
            'media' => $data->hasAttribute('media') ? (string) $data->getAttribute('media') : 'screen',
            'type' => $data->hasAttribute('type') ? (string) $data->getAttribute('type') : 'text/css',
        ));

        return $returnValue;
    }

    private function buildRubricBlock(DOMElement $data){

        $returnValue = new RubricBlock($this->extractAttributes($data));
        $this->parseContainerStatic($data, $returnValue->getBody());

        return $returnValue;
    }

    private function buildFeedback(DOMElement $data, $interactive = false){

        $type = ucfirst($data->nodeName);
        $feedbackClass = '\\oat\\taoQtiItem\\model\\qti\\feedback\\'.$type;
        if(!class_exists($feedbackClass)){
            throw new ParsingException('The interaction class cannot be found: '.$feedbackClass);
        }

        $attributes = $this->extractAttributes($data);
        $myFeedback = new $feedbackClass($attributes, $this->item);


        if($data->nodeName == 'modalFeedback'){
            $myFeedback = new $feedbackClass($attributes, $this->item);
            $this->parseContainerStatic($data, $myFeedback->getBody());
        }else{

            //@todo: to be implemented: interactive feedback
            throw new UnsupportedQtiElement($data);

            if($interactive){
                $container = new ContainerFeedbackInteractive();
                $myFeedback = new $feedbackClass($container, $attributes, $this->item);
                $this->parseContainerInteractive($data, $myFeedback->getBody());
            }else{
                $container = new ContainerStatic();
                $myFeedback = new $feedbackClass($container, $attributes, $this->item);
                $this->parseContainerStatic($data, $myFeedback->getBody());
            }
        }

        return $myFeedback;
    }
    
    /**
     * Check if the node is dom element is a valid portable custom interaction one
     * 
     * @param DOMElement $data
     * @return boolean
     */
    private function isPciNode(DOMElement $data){

        $ns = $this->getPciNamespace();
        return (boolean) $this->queryXPathChildren(array('portableCustomInteraction'), $data, $ns)->length;
    }

    /**
     * Parse and build a custom interaction object
     * 
     * @param DOMElement $data
     * @return CustomInteraction
     * @throws ParsingException
     */
    private function buildCustomInteraction(DOMElement $data){

        $interaction = null;

        if($this->isPciNode($data)){
            
            //use tao's implementation of portable custom interaction
            $interaction = new PortableCustomInteraction($this->extractAttributes($data), $this->item);
            $interaction->feed($this, $data);
            
        }else{ 

            $ciClass = '';
            $classes = $data->getAttribute('class');
            $classeNames = split('/\s+/', $classes);
            foreach($classeNames as $classeName){
                $ciClass = CustomInteractionRegistry::getCustomInteractionByName($classeName);
                if($ciClass){
                    $interaction = new $ciClass($this->extractAttributes($data), $this->item);
                    $interaction->feed($this, $data);
                    break;
                }
            }
            
            if(!$ciClass){
                throw new ParsingException('unknown custom interaction to be build');
            }
            
        }

        return $interaction;
    }
    
    /**
     * Get the namespace of the portable custom interaction
     * 
     * @return string
     */
    public function getPciNamespace(){
        //@todo : implement this properly
        return 'pci';
    }
    
    /**
     * Check if the node is dom element is a valid portable info control one
     * 
     * @param DOMElement $data
     * @return boolean
     */
    private function isPicNode(DOMElement $data){

        $ns = $this->getPicNamespace();
        return (boolean) $this->queryXPathChildren(array('portableInfoControl'), $data, $ns)->length;
    }

    /**
     * Parse and build a info control
     * 
     * @param DOMElement $data
     * @return InfoControl
     * @throws ParsingException
     */
    private function buildInfoControl(DOMElement $data){

        $infoControl = null;

        if($this->isPicNode($data)){
            
            //use tao's implementation of portable custom interaction
            $infoControl = new PortableInfoControl($this->extractAttributes($data), $this->item);
            $infoControl->feed($this, $data);
            
        }else{ 

            $ciClass = '';
            $classes = $data->getAttribute('class');
            $classeNames = split('/\s+/', $classes);
            foreach($classeNames as $classeName){
                $ciClass = InfoControlRegistry::getInfoControlByName($classeName);
                if($ciClass){
                    $infoControl = new $ciClass($this->extractAttributes($data), $this->item);
                    $infoControl->feed($this, $data);
                    break;
                }
            }
            
            if(!$ciClass){
                throw new ParsingException('unknown info control to be build');
            }
            
        }

        return $infoControl;
    }
    
    /**
     * Get the namespace of the portable info control
     * 
     * @return string
     */
    public function getPicNamespace(){
        //@todo : implement this properly
        return 'pic';
    }

}
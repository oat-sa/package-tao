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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/scripts/class.MigrateLegacyItems.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.03.2011, 15:11:24 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-includes begin
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-includes end

/* user defined constants */
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-constants begin
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-constants end

/**
 * Short description of class taoItems_scripts_MigrateLegacyItems
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */
class taoItems_scripts_MigrateLegacyItems
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute item
     *
     * @access private
     * @var Resource
     */
    private $item = null;

    /**
     * Short description of attribute qtiItem
     *
     * @access private
     * @var taoItems_models_classes_QTI_Item
     */
    private $qtiItem = null;

    /**
     * Short description of attribute outputDir
     *
     * @access private
     * @var string
     */
    private $outputDir = '';

    /**
     * Short description of attribute styles
     *
     * @access private
     * @var array
     */
    private $styles = array();

    /**
     * Short description of attribute medias
     *
     * @access private
     * @var array
     */
    private $medias = array();

    /**
     * Short description of attribute xpath
     *
     * @access private
     * @var DOMXPath
     */
    private $xpath = null;

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function preRun()
    {
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D81 begin
        
    	if(isset($this->inputFormat['uri'])){
    		$this->item = $this->getResource($this->parameters['uri']);
    	}
    	if(is_null($this->item) && isset($this->parameters['addResource']) && $this->parameters['addResource'] === true){
    		$clazz = null;
    		if(isset($this->parameters['class'])){
    			$clazz = new core_kernel_classes_Class($this->parameters['class']);
    		}
    		$this->item = $this->createResource($clazz);
    	}
    	if ((isset($this->inputFormat['uri'])  || isset($this->parameters['addResource'])) && is_null($this->item )){
    		$this->err("Unable to create/retrieve item");
    	}
    	if(isset($this->parameters['output'])){
    		$this->outputDir = $this->parameters['output'];
    	}
    	else if(is_null($this->item)){
    		$this->outputDir = dirname($this->parameters['input']);
    	}
    	else{
    		$this->outputDir = sys_get_temp_dir();
    	}
    	$this->outputDir = preg_replace("/\/$/", '',trim($this->outputDir));
    	
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D81 end
    }

    /**
     * Short description of method run
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function run()
    {
        // section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6E begin
        
    	$resultContent = $this->qcm2Qti($this->parameters['input']);
    	
    	$filename = substr(basename($this->parameters['input']), 0, strripos(basename($this->parameters['input']), '.'));
    	
    	//create the QTI file
    	$qtiFilename = 'qti-'.$filename.'.xml';
    	$qtiFile = $this->outputDir. '/'.$qtiFilename;
    	file_put_contents($qtiFile, $resultContent);
    	if(!file_exists($qtiFile)){
    		$this->err("Unable to create $qtiFile", true);
    	}
    	
    	//create a package
    	if(isset($this->parameters['pack']) && $this->parameters['pack'] == true){
    		$path = $this->outputDir. '/'.$filename.'.zip';
    		
    		$zipArchive = new ZipArchive();
			if($zipArchive->open($path, ZipArchive::CREATE) !== true){
				$this->err('Unable to create archive at '.$path);
			}
			$zipArchive->addFile($this->outputDir. '/'.$qtiFilename, $qtiFilename);
			$relMedias = array();
			foreach($this->medias as $mediaPath){
				$relPath = str_replace(realpath($this->outputDir).'/', '', $mediaPath);
				$zipArchive->addFile($mediaPath, $relPath);
				$relMedias[] = $relPath;
			}
            $templateRenderer = new taoItems_models_classes_TemplateRenderer(BASE_PATH.'/models/classes/QTI/templates/imsmanifest.tpl.php', array(
				'qtiItem' 				=> $this->qtiItem,
				'qtiFilePath'			=> $qtiFilename,
				'medias'				=> $relMedias,
				'manifestIdentifier'	=> 'QTI-MANIFEST-'.tao_helpers_Display::textCleaner($this->qtiItem->getIdentifier(), '-')
        	));
        	$zipArchive->addFromString('imsmanifest.xml', $templateRenderer->render());
			
			$zipArchive->close();
    	}
    	
        // section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6E end
    }

    /**
     * Short description of method createResource
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    private function createResource( core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D76 begin
        
        if(is_null($clazz)){
        	$clazz = new core_kernel_classes_Class(TAO_ITEM_CLASS);
        }
        $itemService = taoItems_models_classes_ItemsService::singleton();
        if($itemService->isItemClass($clazz)){
        	$returnValue = $clazz->createInstance();
        }
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D76 end

        return $returnValue;
    }

    /**
     * Short description of method getResource
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    private function getResource($uri)
    {
        $returnValue = null;

        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D7E begin
        
        $itemService = taoItems_models_classes_ItemsService::singleton();
     
        $returnValue = new core_kernel_classes_Resource($uri);
        
        $isItem = false;
        foreach($returnValue->getType() as $type){
        	if($itemService->isItemClass($type)){
        			 $isItem = true;
        			 break;
        	}
        }
        if(!$isItem){
        	$returnValue = null;
        }
        
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D7E end

        return $returnValue;
    }

    /**
     * Short description of method qcm2Qti
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string inputFile
     * @return string
     */
    private function qcm2Qti($inputFile)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D78 begin
        
	    try{
		    $dom = new DOMDocument();
		    $dom->load($inputFile);
	    	$this->xpath = new DOMXPath($dom);
		    	
		    //create default structure
		    $itemIdentifier = null;
		    if(!is_null($this->item)){
		    	$itemIdentifier = tao_helpers_Uri::getUniqueId($this->item->getUri());
		    }
		    
		    $label = '';
		    foreach($this->xpath->query("rdfs:LABEL") as $labelNode){
		    	$label = $labelNode->nodeValue;
		    	break;
		    }
		    $options = array(
		    	'title' 		=> $label,
		    	'adaptive'		=> 'false',
		    	'timeDependent' => 'false'
		    );
		    $this->qtiItem = new taoItems_models_classes_QTI_Item($itemIdentifier, $options);
			
			
		
	    	//get inqueries in the right order in the legacy doc ( inquery => interaction)
	    	$inqueries = array();
	    	foreach($this->xpath->query("//tao:INQUIRY") as $inquery){
	    		$added = false;
	    		if($inquery->hasAttribute('order')){
	    			$order = (int)$inquery->getAttribute('order');
	    			if(!array_key_exists($order, $inqueries)){
	    				$inqueries[(int)$inquery->getAttribute('order')] = $inquery;
	    				$added = true;
	    			}
	    		}
	    		if(!$added){
	    			foreach(array_keys($inqueries) as $index){
	    				if(!isset($inqueries[$index + 1])){
	    					$inqueries[$index + 1] = $inquery; 
	    					break;
	    				}
	    			}
	    		}
	    	}
	    	ksort($inqueries);
	    	
	    	
	    	(count($inqueries)<=1) ? $responseId = 'RESPONSE' : $responseId = null;
	    	
	    	//build interactions from the inqueries
	    	$interactions = array();
	    	foreach($inqueries as $inquery){
	    		
	    		$interaction = $this->inquery2Interaction($inquery);
		    	if(!is_null($interaction)){
		    		if(!is_null($responseId) && !is_null($interaction->getResponse())){
		    			$interaction->getResponse()->setIdentifier($responseId);
		    		}
		    		$interactions[] = $interaction;
		    	}
		    }
	    	
	    	
	    	//create the Item's data from the ITEMPRESENTATION 
	    	$data = '';
	    	foreach($this->xpath->query("tao:ITEMPRESENTATION") as $presentationNode){
	    		foreach($this->xpath->query("./xul/box[@id='itemContainer_box']", $presentationNode) as $containerNode){
	    			foreach($containerNode->childNodes as $child){
	    				
	    				switch(strtolower($child->nodeName)){
	    					case 'image':
	    						if($child->hasAttribute('src')){
	    							$data .= "<img src='".$child->getAttribute('src')."'";
									if($child->hasAttribute('width')){
										$data .= " width='".$child->getAttribute('width')."'";
									}  
	    							if($child->hasAttribute('height')){
										$data .= " height='".$child->getAttribute('height')."'";
									}  				
									$alt = basename($child->getAttribute('src'));
									$alt = substr($alt, 0, strripos($alt, '.'));
	    							$data .= " alt='$alt' />";
	    						}
	    						break;
	    					case 'label':
	    						if($child->hasAttribute('value') && $child->hasAttribute('id') && preg_match("/^problem_textbox/", strtolower($child->getAttribute('id')))){
	    							$multi = false;
	    							if($child->hasAttribute('multiline') && strtolower($child->getAttribute('multiline')) == 'true'){
	    								$multi = true;	
	    							}
	    							
	    							if($multi){
	    								$data .= "<div>";
	    							}
	    							else{
	    								$data .= "<span>";
	    							}
	    							
	    							$data .= $this->cleanUp($child->getAttribute('value'));
									 							
	    							if($multi){
	    								$data .= "</div>";
	    							}
	    							else{
	    								$data .= "</span>";
	    							}
	    						}
	    						break;
	    					case 'box':
	    						if($child->hasAttribute('id') && strtolower($child->getAttribute('id')) == 'inquirycontainer_box'){
		    						foreach($interactions as $interaction){
							    		$data .= '{'.$interaction->getSerial().'}';	
							    	}
	    						}
	    						break;
	    				}
	    			}
	    		}
	    	}
	    	$hasResponse = false;
	    	foreach($this->qtiItem->getInteractions() as $interaction){
	    		if(!is_null($interaction->getResponse())){
	    			$hasResponse = true;
	    			break;
	    		}
	    	}
	    	
	    	//add default responseProcessing:
			$this->qtiItem->setOutcomes(array(
				new taoItems_models_classes_QTI_Outcome('SCORE', array('baseType' => 'integer', 'cardinality' => 'single'))
			));
	
	    	$this->qtiItem->setInteractions($interactions);
	    	$this->qtiItem->setData($data);
			if($hasResponse){
				$this->qtiItem->setResponseProcessing(
					taoItems_models_classes_QTI_response_TemplatesDriven::create($this->qtiItem)
				);
			}
	    	
	    	$this->createStyleSheet();
	    
	    	$output = $this->qtiItem->toQTI();
	    	
	    	$resDirName = 'res-'.$this->qtiItem->getSerial();
        	$resDirPattern = "/".preg_quote($resDirName, '/')."/";
        	$relativeBase = dirname($this->parameters['input']);
	    	
	    	//retrieve and format all the external medias (images, videos)
	    	$matches = array();
			if(preg_match_all("/(href|src|data|\['imagePath'\])\s*=\s*[\"\'](.+?)[\"\']/is", $output, $matches) > 0){
				if(isset($matches[2])){
					foreach($matches[2] as $uri){
						
						if(preg_match("/^http:\/\//", $uri) ){
							$mediaData = '';
							try{
							 	$mediaData = tao_helpers_Request::load($uri);
							}
							catch(Exception $e){
								continue;	
							}
							if(!empty($mediaData)){
								$this->createMedia(basename($uri), $mediaData);
							}
						}
						else if($uri != '#' && !preg_match($resDirPattern, $uri)){
							$file = realpath($relativeBase.'/'.$uri);
							if(file_exists($file)){
								$this->createMedia(basename($file), file_get_contents($file));
								$mediaPath = $this->medias[basename($file)];
								$relPath = str_replace(realpath($this->outputDir).'/', '', $mediaPath);
								$output = str_replace($uri, $relPath, $output);
							}
						}
					}
				}
			}
			
			$returnValue = $output;
    	}
    	catch(DomainException $de){
			$this->err($de, true);    		
    	}
    	
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D78 end

        return (string) $returnValue;
    }

    /**
     * Short description of method inquery2Interaction
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  DOMNode inquery
     * @return taoItems_models_classes_QTI_Interaction
     */
    private function inquery2Interaction( DOMNode $inquery)
    {
        $returnValue = null;

        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E6E begin
        
    	foreach($this->xpath->query(".//tao:PROPOSITIONTYPE", $inquery) as $propositionType){
			if(preg_match("/^Exclusive Choice$/i", trim($propositionType->nodeValue))){
   				$returnValue = $this->inquery2ChoiceInteraction($inquery);
   			}
   			else if(preg_match("/^Multiple Choice$/i", trim($propositionType->nodeValue))){
   				$returnValue = $this->inquery2ChoiceInteraction($inquery, true);
   			}
   			else if(preg_match("/^Text$/i", trim($propositionType->nodeValue))){
    			$returnValue = $this->inquery2TextInteraction($inquery);
    		}
    	}
        
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E6E end

        return $returnValue;
    }

    /**
     * Short description of method inquery2ChoiceInteraction
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  DOMNode inquery
     * @param  boolean multiple
     * @return taoItems_models_classes_QTI_Interaction
     */
    private function inquery2ChoiceInteraction( DOMNode $inquery, $multiple = false)
    {
        $returnValue = null;

        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E72 begin
        
        $interaction  = new taoItems_models_classes_QTI_Interaction('choice');
    	if($multiple){
    		$interaction->setOption("maxChoices", '0');
    	}
    	else{
    		$interaction->setOption("maxChoices", '1');
    	}
    	
    	foreach($this->xpath->query("tao:QUESTION", $inquery) as $question){
    		$interaction->setPrompt($this->cleanUp($question->nodeValue));
   		}
		    		
    	//get the proposition of an inquery (proposition => choice)
    	$hasOrder = false;
    	$choices = array();
    	foreach($this->xpath->query(".//tao:PROPOSITION", $inquery) as $proposition){
    		$identifier = null;
    		if($proposition->hasAttribute('Id')){
    			$identifier = $proposition->getAttribute('Id');
    		}
    		
    		$choice = new taoItems_models_classes_QTI_Choice($identifier);
	    	$choice->setType('simpleChoice');
	    	 
	    	$choice->setData($this->cleanUp($proposition->nodeValue));
	    		
    		$added = false;
	    	if($proposition->hasAttribute('order')){
	    		$hasOrder = true;
	    		$order = (int)$proposition->getAttribute('order');
	    		if(!array_key_exists($order, $choices)){
	    			$choices[(int)$proposition->getAttribute('order')] = $choice;
	    			$added = true;
	    		}
	    	}
	    	if(!$added){
	    		foreach(array_keys($choices) as $index){
	    			if(!isset($choices[$index + 1])){
	    				$choices[$index + 1] = $choice; 
	    				break;
	    			}
	    		}
	    	}
    	}
    	ksort($choices);
    	$interaction->setChoices($choices);
    		    		
   		if(!$hasOrder){
   			$interaction->setOption('shuffle', 'true');
   		}
   		else{
    		$interaction->setOption('shuffle', 'false');
    	}
    		
    	$data = '';
   		foreach($choices as $choice){
   			$data .= '{'.$choice->getSerial().'}';	
   		}
   		$interaction->setData($data);
   		
   		//build the response
    	$correctResponses = array();
    	foreach($this->xpath->query(".//tao:HASANSWER", $inquery) as $answer){
    		$vector = (string)$answer->nodeValue;
    		for($i=0; $i < strlen($vector); $i++){
    			if($vector[$i] == '1' && isset($choices[$i])){
    				$correctChoice = $choices[$i];
    				if($correctChoice instanceof taoItems_models_classes_QTI_Choice){
    					$correctResponses[] = $correctChoice->getIdentifier();
    				}
    			}
    		}
    	}
    		
    	$options = array('baseType' => 'identifier');
    	(count($correctResponses) > 1) ? $options['cardinality'] = 'multiple' :  $options['cardinality'] = 'single';
    		
    	$response = new taoItems_models_classes_QTI_Response(null, $options);
		$response->setCorrectResponses($correctResponses);
		$interaction->setResponse($response);
    		
    	$returnValue = $interaction;
        
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E72 end

        return $returnValue;
    }

    /**
     * Short description of method inquery2TextInteraction
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  DOMNode inquery
     * @return taoItems_models_classes_QTI_Interaction
     */
    private function inquery2TextInteraction( DOMNode $inquery)
    {
        $returnValue = null;

        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E7B begin
        
        $interaction  = new taoItems_models_classes_QTI_Interaction('extendedText');
    	
    	foreach($this->xpath->query("tao:QUESTION", $inquery) as $question){
    		$interaction->setPrompt($this->cleanUp($question->nodeValue));
   		}
   		
   		$options = array(
   			'baseType' 		=> 'string',
   			'cardinality' 	=> 'multiple'
   		);
    		
    	$response = new taoItems_models_classes_QTI_Response(null, $options);
   		$interaction->setResponse($response);
   		
   		$returnValue = $interaction;
        
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E7B end

        return $returnValue;
    }

    /**
     * Short description of method cleanUp
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string htmlString
     * @return string
     */
    private function cleanUp($htmlString)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E7E begin
        
    	$htmlString = html_entity_decode($htmlString, ENT_COMPAT, 'UTF-8');
    	
    	$tidy = new tidy();
    	$cleaned = $tidy->repairString($htmlString, array(
				'output-xhtml'			=> true,
				'alt-text' 				=> true,
				'quote-nbsp' 			=> true,
				'indent' 				=> 'auto',
				'preserve-entities' 	=> true,
				'quote-ampersand' 		=> true,
				'uppercase-attributes' 	=> false,
				'uppercase-tags' 		=> false,
				'clean'					=> true,
				'join-styles'			=> false,
    			'hide-comments'			=> true
			),
		'UTF8');
		

		$stylePattern = "/^(.*)?\s?{(.*)?}$/";
		
    	$xml = simplexml_load_string($cleaned); 
    	$styleRules = array();
		foreach($xml->xpath("//*[name(.) = 'style']") as $styleNode){
			$styleContents = explode("\n",(string)$styleNode);
			foreach($styleContents as $styleRule){
				if(preg_match($stylePattern, trim($styleRule))){
					$styleRules[] = trim($styleRule);
				}
			}
		}
    	foreach($xml->xpath("//*[name(.) = 'body']") as $bodyNode){
    		if(count($bodyNode->children()) > 0){
	       		$returnValue .= preg_replace(array("/^<body([^>]*)?>/i", "/<\/body([^>]*)?>$/i"), "", trim($bodyNode->asXML()));
       		}
       		else{
       			$returnValue .= (string)$bodyNode;
       		}
		}
        
		foreach($styleRules as  $styleRule){
			$class = uniqid('c');
			
			$tokens = explode(' ',$styleRule);
			$oldClass=  str_replace('span.', '', trim($tokens[0]));
			
			$this->styles[] = str_replace('span.'.$oldClass, 'span.'.$class, $styleRule);
			$returnValue = str_replace('class="'.$oldClass.'"', 'class="'.$class.'"', $returnValue);
		}
		
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E7E end

        return (string) $returnValue;
    }

    /**
     * Short description of method createStyleSheet
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    private function createStyleSheet()
    {
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E81 begin
        
    	if(count($this->styles) > 0){
    		if($this->createMedia('style.css', implode("\n", $this->styles))){
    			if(isset($this->medias['style.css'])){
	    			$styleSheet = basename(dirname($this->medias['style.css'])).'/style.css';
	    			$this->qtiItem->setStylesheets(array(array(
	    				'title'=> 'style.css',
	    				'media'=> 'screen',
	    				'href' => $styleSheet,
	    				'type' => 'text/css'
	    			)));
    			}
    		}
    	}
    	
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E81 end
    }

    /**
     * Short description of method createMedia
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  string content
     * @return boolean
     */
    public function createMedia($name, $content)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E83 begin
        
        $dirName = 'res-'.$this->qtiItem->getSerial();
        $resDir = $this->outputDir.'/'.$dirName;
        if(!is_dir($resDir)){
        	mkdir($resDir);
        }
        if(file_put_contents($resDir.'/'.$name, $content)){
        	$this->medias[$name] = realpath($resDir.'/'.$name);
        	if(file_exists($this->medias[$name])){
        		$returnValue = true;
        	}
        }
        
        // section 127-0-1-1--77ddac51:12e9ae2b491:-8000:0000000000002E83 end

        return (bool) $returnValue;
    }

} /* end of class taoItems_scripts_MigrateLegacyItems */

?>
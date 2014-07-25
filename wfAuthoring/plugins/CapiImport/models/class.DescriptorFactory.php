<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - class.QuestionnaireDescriptor.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatic generated with ArgoUML 0.24 on 03.12.2008, 14:52:55
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

require_once('class.ConditionDescriptor.php');

/**
 * Short description of class QuestionnaireDescriptor
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class DescriptorFactory{
	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute filepath
	 *
	 * @access public
	 * @var string
	 */
	private $filepath = '';

	/**
	 * Short description of attribute validationError
	 *
	 * @access public
	 * @var string
	 */
	private $validationError = '';

	/**
	 * Short description of attribute importError
	 *
	 * @access public
	 * @var string
	 */
	public $importError = '';


	/**
	 * Short description of attribute isValidated
	 *
	 * @access private
	 * @var boolean
	 */
	private $isValidated = false;

	// --- OPERATIONS ---

	/**
	 * Short description of method __construct
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param string
	 * @return core_kernel_classes_Session_void
	 */
	public function __construct(){
		
	}

	private function getIdentifier(DOMElement $element)
	{
		$localId = "NOID";
		$parentId="NOID";
		$grandParentId="NOID";
		if ($element->hasAttribute('id')) {	
			$localId = $element->attributes->getNamedItem('id')->value;				
		}
		if ($element->parentNode->hasAttribute('id')) {	
			$parentId = $element->parentNode->attributes->getNamedItem('id')->value;				
		}
		
		return "$localId inside $parentId ";
	}
	/**
	 * Short description of method getCapiDescriptor
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return CapiDescriptor
	 */
	public function getCapiDescriptor()
	{

		if($this->isValidated) {
			$xml = new XMLReader();
			$xml->open($this->filepath);

			//        	echo '$xml->nodeType | $xml->name|$xml->value|$xml->localName <br/>' ;
			$capiDecriptor = new CapiDescriptor(__METHOD__);
			
				//debug
				//				echo $xml->nodeType . '|' . $xml->name . '|' . $xml->value . '|' . $xml->localName . '<br/>';

			if(RDFLIST_COMLPIANT) {

				while ($xml->read()){
					if($xml->localName == 'list' && $xml->nodeType == XMLREADER::ELEMENT) {
						
						
						$listDom = $xml->expand();	
						$capiDecriptor->list->add($this->getListDescriptor($listDom));
						while($xml->next('list')){
							$listDom = $xml->expand();	
							$capiDecriptor->list->add($this->getListDescriptor($listDom));
						
						}
						break;
						
					
					}
				}
			}
			else {
					
				while($xml->read()){
					if(($xml->localName == 'itemGroup' || $xml->localName == 'tank') && $xml->nodeType == XMLREADER::ELEMENT){
	
						$itemGroupDom = $xml->expand();	
						//sic... itemgroup tagname is also used in references... 
						if(!($itemGroupDom->hasAttribute('ref')))
						{
							$capiDecriptor->itemGroup->add($this->getItemGroupDescriptor($itemGroupDom));	
						}
					}
				}
			}



		}
		else {
			$this->importError = 'Could not import non valid file';

		}

		return $capiDecriptor;


	}
	
	
	private function getListDescriptor(DOMElement $listDom) {
		$listDescriptor = new ListDescriptor(__METHOD__);
		$listDescriptor->name = $listDom->attributes->getNamedItem('name')->value;
		if($listDom->hasChildNodes()){	
			foreach ($listDom->childNodes as $node){
				if($node->nodeName == 'selector'){
					$listDescriptor->selector =$node->attributes->getNamedItem('type')->value;
					if($node->hasAttribute("parent")) {
						$listDescriptor->selectorType =$node->attributes->getNamedItem('parent')->value;
					}
					
				}
				if($node->nodeName == 'list'){
					$listDescriptor->sublist->add($this->getListDescriptor($node));	
				}
				if($node->nodeName == 'itemGroup'){
					$listDescriptor->itemGroup->add($this->getItemGroupDescriptor($node));
				}
			}
			
		}

		return $listDescriptor;
	}
	
	/**
	 * Short description of method getItemGroupDescriptor
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return ItemGroupDescriptor
	 */
	private function getItemGroupDescriptor(DOMElement $itemGroupDom){
		$itemGroupDescriptor = new ItemGroupDescriptor(__METHOD__);

		$itemGroupDescriptor->id = $itemGroupDom->attributes->getNamedItem('id')->value;
		
		if($itemGroupDom->hasAttribute('responseCondition')){	
			$itemGroupDescriptor->responseCondition = $itemGroupDom->attributes->getNamedItem('responseCondition')->value;
		}	
		if($itemGroupDom->hasAttribute('displayCalendar')){	
			$itemGroupDescriptor->displayCalendar = $itemGroupDom->attributes->getNamedItem('displayCalendar')->value;
		}
		
		if($itemGroupDom->hasAttribute('hidden'))
		{
			if ($itemGroupDom->attributes->getNamedItem('hidden')->value == 'true')
			{
				$itemGroupDescriptor->hidden = true;
			}
			else
			{
				$itemGroupDescriptor->hidden = false;
			}
		}
		else
		{
			$itemGroupDescriptor->hidden = false;
		}
		
		if($itemGroupDom->hasAttribute('layout')){	
			$itemGroupDescriptor->layout = $itemGroupDom->attributes->getNamedItem('layout')->value;				
		}

		if($itemGroupDom->hasChildNodes()){		
			$itemDomList =$itemGroupDom->getElementsByTagName('item');
			foreach ($itemDomList as $itemDom){
				$itemGroupDescriptor->items->add($this->getItemDescriptor($itemDom));			
			}
			$tableInfoDomList =$itemGroupDom->getElementsByTagName('tableInfo');
			if($tableInfoDomList->length>0){
				if($tableInfoDomList->item(0)->hasChildNodes()) {
					$infos = $tableInfoDomList->item(0)->getElementsByTagName('info');
					foreach ($infos as $info) {
						if($info->hasAttribute('id')) {
							$itemGroupDescriptor->tableInfo[] = ($info->attributes->getNamedItem('id')->value);
						}
					}
				}
			}

			foreach ($itemGroupDom->childNodes as $childNode) {
				if ($childNode->nodeName == "routing")
				{
					$itemGroupDescriptor->routing = $this->getRoutingDescriptor($childNode);
				}
				if ($childNode->nodeName == "inferenceRule")
				{
					$itemGroupDescriptor->inferenceRules[] = $this->getInferenceDescriptor($childNode);
				}
				if ($childNode->nodeName == "consistencyCheck")
				{
					$itemGroupDescriptor->consistencyRules[] = $this->getConsistencyCheckDescriptor($childNode);
				}
				if ($childNode->nodeName == "service")
				{
					
					$itemGroupDescriptor->service = $this->getServiceDescriptor($childNode);
					
				}
			}



		}
		//        	var_dump($itemGroupDescriptor);
		return $itemGroupDescriptor;
	}
	/**
	 * replace Markups
	 **/
	private function replaceMarkups(DOMElement $itemDom)
	{
		$string ="";
		$explicitHtmlBr = false;
		foreach ($itemDom->childNodes as $element)
		{	

			if (get_Class($element)=="DOMText")
			{	//seems that encoding is lost when using the DOM ??? 
				$string.= htmlspecialchars($element->wholeText);

			}
			if (get_Class($element)=="DOMElement")
			{
				switch ($element->tagName)
				{
					case "toggleRead":
						$class = '';
						
						if ($itemDom->tagName == 'question'){
							$class = 'dontread';
						}
						else{
							$class = 'read';
						}
						// This code retrieve what is in the <toggleRead> tag but
						// add a trailing carriage return (I don't know why) so
						// I remove it.
						$nodeValue = $element->nodeValue;
						
						$string.= "<html:span class=\"" . $class . "\">".$nodeValue."</html:span>";
					break;
					case "html:br":
						$explicitHtmlBr = true ;
						$string.= "<html:br/>";
					break;
					default:
						if ($element->getAttribute("html:class") == "")
							{
								$class = $element->getAttribute("class");
							}
							else
							{
								$class = $element->getAttribute("html:class");
							}
						
						$string.= "<".$element->tagName. " class=\"".$class."\">".$element->nodeValue."</".$element->tagName.">"; 
						//echo "<big>";echo $string;echo "</big>";
					break;
				}
			}
		}

		//$string = str_replace("<toggleRead>","<html:span class=\"read\">",$string);
		//$string = str_replace("</toggleRead>","</html:span>",$string);
		//remove any starting or ending starting line
		$string = trim($string);
		if (!$explicitHtmlBr)
		{	
			$string = $string;
			//echo $string;
			$string = str_replace("\n", '<html:br/>', $string);
			
		}
		return $string;
	}
	

	/**
	 * Short description of method getItemDescriptor
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return ItemDescriptor
	 */
	private function getServiceDescriptor(DOMElement $serviceDom){
		$serviceDescriptor = new ServiceDescriptor(__METHOD__);
		if($serviceDom->hasAttribute('id')){
			$serviceDescriptor->id = $serviceDom->attributes->getNamedItem('id')->value;
		}

		foreach ($serviceDom->childNodes as $parameter)
			{
				if ($parameter->nodeName =="param")
				{

					
						$serviceDescriptor->variables[$parameter->attributes->getNamedItem('name')->value] = $parameter->attributes->getNamedItem('value')->value;
						
				}
			}
		return $serviceDescriptor;

	}

	/**
	 * Short description of method getItemDescriptor
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return ItemDescriptor
	 */
	private function getItemDescriptor(DOMElement $itemDom){
		$itemDescriptor = new ItemDescriptor(__METHOD__);
		if($itemDom->hasAttribute('id')){
			$itemDescriptor->id = $itemDom->attributes->getNamedItem('id')->value;
		}
		if($itemDom->hasAttribute('state')){
			$itemDescriptor->status = $itemDom->attributes->getNamedItem('state')->value;
			
		}
		
		if($itemDom->hasAttribute('mandatory')){
			$itemDescriptor->mandatory = $itemDom->attributes->getNamedItem('mandatory')->value == true;
		}
		if($itemDom->hasChildNodes()){	
			
			$questionDomList =$itemDom->getElementsByTagName('predicate');
			if($questionDomList->length>0) {
				$itemDescriptor->question = $this->replaceMarkups($questionDomList->item(0));
			}
			
			$questionDomList =$itemDom->getElementsByTagName('question');
			if($questionDomList->length>0) {
				$itemDescriptor->question = $this->replaceMarkups($questionDomList->item(0));
			}

			$instructionDomList =$itemDom->getElementsByTagName('instruction');
			if($instructionDomList->length>0) {
				$itemDescriptor->instruction = $this->replaceMarkups($instructionDomList->item(0));

			}

			$helpDomList =$itemDom->getElementsByTagName('help');
			if($helpDomList->length>0) {
				$itemDescriptor->help = $this->replaceMarkups($helpDomList->item(0));
			}
			$responsesDomList =$itemDom->getElementsByTagName('responses');
			foreach ($responsesDomList as $responsesDom) {
				$itemDescriptor->responses = $this->getResponsesGroupDescritor($responsesDom);		
			}
			$dynamicDomList = $itemDom->getElementsByTagName('dynamicText');
			foreach ($dynamicDomList as $dynamicDom) {

				$itemDescriptor->dynamicText->add($this->getDynamicTextDescriptor($dynamicDom));
			}

		}
		//    	var_dump($itemDescriptor);
		return $itemDescriptor;
	}
	/**
	 * Short description of method getResponsesGroupDescritor
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return ItemDescriptor
	 */
	private function getResponsesGroupDescritor(DOMElement $responsesDom){
		$responseGroupDecriptor = new ResponsesGroupDescriptor(__METHOD__);
		if($responsesDom->hasAttribute('choice')){
			$responseGroupDecriptor->choice = $responsesDom->attributes->getNamedItem('choice')->value;
		}
		if($responsesDom->hasAttribute('layout')){
			$responseGroupDecriptor->layout = $responsesDom->attributes->getNamedItem('layout')->value;
		}

		if($responsesDom->hasAttribute('minValue')){
			$responseGroupDecriptor->minValue = $responsesDom->attributes->getNamedItem('minValue')->value;
		}

		if($responsesDom->hasAttribute('maxValue')){
			$responseGroupDecriptor->maxValue = $responsesDom->attributes->getNamedItem('maxValue')->value;
		}
		if($responsesDom->hasAttribute('minLength')){
			$responseGroupDecriptor->minLength = $responsesDom->attributes->getNamedItem('minLength')->value;
		}

		if($responsesDom->hasAttribute('maxLength')){
			$responseGroupDecriptor->maxLength = $responsesDom->attributes->getNamedItem('maxLength')->value;
		}

		

		if($responsesDom->hasChildNodes()){	
			$responseDomList =$responsesDom->getElementsByTagName('response');
			foreach ($responseDomList as $response) {
				$responseDescriptor = new ResponseDescriptor(__METHOD__);
				if($response->hasAttribute('freeTextEntry')){
					if ($response->attributes->getNamedItem('freeTextEntry')->value == "true")
					{
						$responseDescriptor->freeTextEntryPosition = "after";
					}
				}
				
				if($response->hasAttribute('freeTextEntryPosition')){
					$responseDescriptor->freeTextEntryPosition = $response->attributes->getNamedItem('freeTextEntryPosition')->value;
				}
				
				if($response->hasAttribute('freeTextEntrySize')){
					$responseDescriptor->freeTextEntrySize = $response->attributes->getNamedItem('freeTextEntrySize')->value;
				}
				
				if ($response->hasAttribute('freeTextEntryHeight')){
					$responseDescriptor->freeTextEntryHeight = $response->attributes->getNamedItem('freeTextEntryHeight')->value;
				}
				
				if($response->hasAttribute('code')){
					$responseDescriptor->code = $response->attributes->getNamedItem('code')->value;
				}

				$responseDescriptor->value = $this->replaceMarkups($response);
				$responseGroupDecriptor->responses->add($responseDescriptor);
			}
		}
		return $responseGroupDecriptor;
	}

	public static function getAssignDescriptor(DOMElement $assignDom) {
		
		if ($assignDom->hasChildNodes())
		{
			if ($assignDom->childNodes->item(0)->nodeName =="assignment")
			{
				$assignDom  = $assignDom->childNodes->item(0);
			}
			else
			{
				
				return NULL;
				throw new common_Exception('First eleemnt of an assignment should be an assignment node');
			}
		}
		else
		{
			throw new common_Exception('there is no children in a then node of an inference rule');
		}

		$assignDescriptor = new AssignDescriptor(__METHOD__);
		if ($assignDom->hasChildNodes())
		{
			$isAssigned = true;
			foreach ($assignDom->childNodes as $variable)
			{
				if ($variable->nodeName =="variable")
				{

					if ($isAssigned) {
						$assignDescriptor->leftVariable = $variable->attributes->getNamedItem('name')->value;
						$isAssigned=false;
					}
					else
					{
						$assignDescriptor->rightVariable = $variable->attributes->getNamedItem('name')->value;
					}
				}
				if ($variable->nodeName =="constant")
				{
					$assignDescriptor->rightConstant = $variable->textContent;
				}
				if ($variable->nodeName == "operator")
				{

					$assignDescriptor->rightOperation = self::getConditionDescriptor($variable);

				}
			}


		}
		else
		{
			throw new common_Exception('assignation in inference rule is not correctly described (no elements)');
		}





		return $assignDescriptor;

	}

	/**
	 * Enter description here...
	 *
	 * @param DOMElement $inferenceDom
	 * @return InferenceDescriptor
	 */
	private function getInferenceDescriptor(DOMElement $inferenceDom) {
		$inferenceDescriptor = new InferenceDescriptor(__METHOD__);

		if($inferenceDom->hasChildNodes()) {

			if (isset($inferenceDom->attributes->getNamedItem('order')->value))
			{
				if ($inferenceDom->attributes->getNamedItem('order')->value == "before")

				{
					$inferenceDescriptor->isPostCondition = false;
					echo "One precondition found\n";
				}

			}
			foreach ($inferenceDom->childNodes as $childNode) {
				
				switch ($childNode->nodeName)
				{
					case "condition":
					{	
						$inferenceDescriptor->condition = $this->getConditionDescriptor($childNode);break;
					}
					case "then":
					{

						$inferenceDescriptor->thenAssignDescription = $this->getAssignDescriptor($childNode);break;
					}
					case "else":
					{
						if($childNode->hasChildNodes()){
							foreach ($childNode->childNodes as $childNodeSon) {
								if($childNodeSon->nodeName == 'assignment') {
									$inferenceDescriptor->elseAssignDescription = $this->getAssignDescriptor($childNode);
								}
								if($childNodeSon->nodeName == 'inferenceRule') {
									$inferenceDescriptor->elseInferenceRecursive = $this->getInferenceDescriptor($childNodeSon);
								}
							}
						}
						break;						
					}
					case "#text":
					{
						
						break;						
					}

					default:{trigger_error("Unknown nodename in inferencerule :".$childNode->nodeName." with value ".$childNode->nodeValue." located in".$this->getIdentifier($inferenceDom));}
				}

			}
		}
		if (is_null($inferenceDescriptor->condition)){			
		trigger_error("An inference rule in the xml is not correct or a subInferencerule (problem with condition) , in the element :".$this->getIdentifier($inferenceDom)."");
		return NULL;
		}
		if (is_null($inferenceDescriptor->thenAssignDescription)){			
		trigger_error("The inference rule in the xml is not correct or a subInferencerule  (problem with assign), in the element :".$this->getIdentifier($inferenceDom)."");
		return NULL;
		}
		return $inferenceDescriptor;
	}


	/**
	 * Enter description here...
	 *
	 * @param DOMElement $conditionDom
	 * @return ConditionDescriptor
	 */
	public static function getConditionDescriptor(DOMElement $conditionDom) {
		$conditionDescriptor = new ConditionDescriptor(__METHOD__);

		//base case, the first time it is called the domelement is not an operation but a condition, shift it to the unqieu operation in it then call recursively
		if ($conditionDom->nodeName == "condition")
		{
			// echo "\nA new condition is being created :\n "."";
			foreach ($conditionDom->childNodes as $operation)
			{
				if ($operation->nodeName == "operator")
				{
					return self::getConditionDescriptor($operation);
				}
			}

		}

		if (!(isset($conditionDom->attributes->getNamedItem('type')->value))) 
			 {
				// trigger_error("No operator in condition was found in the element :".$this->getIdentifier($conditionDom)."");
				trigger_error("No operator in condition was found in the element");
				return $conditionDescriptor;
			  }

		//two cases either we have an arithmetical comparison X > 3 , Y = 3, in this case this is a terminal condition, either we have a boolean operator like A and B, in this case call recursively on subOperations

		
		//terminal case (I added answered but the import does not do anything with it)
		if (in_array($conditionDom->attributes->getNamedItem('type')->value,array("equal","notEqual","greater","greaterEqual","less","lessEqual","+","-","*","/",".","answered")))
		{	
			// echo "<i>".$conditionDom->attributes->getNamedItem('type')->value."</i>(";

			//check if we are comparing terms , feed cmp operator
			if (in_array($conditionDom->attributes->getNamedItem('type')->value,array("equal","notEqual","greater","greaterEqual","less","lessEqual","answered")))
			{
				$conditionDescriptor->cmp = $conditionDom->attributes->getNamedItem('type')->value;
			}
			//check if it is an arithmetic operator, feed arithmeticOperator 
			else 
			{
				$conditionDescriptor->arithmeticOperator = $conditionDom->attributes->getNamedItem('type')->value;
			}

			$leftPart = true;
			foreach ($conditionDom->childNodes as $variable)
			{
				if ($variable->nodeName == "variable")
				{
					if ($leftPart)
					{
						$conditionDescriptor->leftPart = $variable->attributes->getNamedItem('name')->value;
						// echo $conditionDescriptor->leftPart.",";
						$conditionDescriptor->leftPartType="variable";
						$leftPart = false;
					}
					else
					{
						$conditionDescriptor->rightPart = $variable->attributes->getNamedItem('name')->value;
						$conditionDescriptor->rightPartType="variable";
						// echo $conditionDescriptor->rightPart."";
					}
				}
				if ($variable->nodeName == "constant")
				{
					if ($leftPart)
					{
						$conditionDescriptor->leftPart = $variable->textContent;
						// echo $conditionDescriptor->leftPart;
						$conditionDescriptor->leftPartType="constant";
						$leftPart=false;
						// echo $conditionDescriptor->leftPart.",";
					}
					else
					{
						$conditionDescriptor->rightPart = $variable->textContent;
						$conditionDescriptor->rightPartType="constant";
						// echo $conditionDescriptor->rightPart;
					}
				}

				if ($variable->nodeName == "operator")
				{
					if ($leftPart)
					{
						$conditionDescriptor->leftPart = self::getConditionDescriptor($variable);
						$conditionDescriptor->leftPartType="aroperation";

						$leftPart=false;
					}
					else
					{
						$conditionDescriptor->rightPart = self::getConditionDescriptor($variable);
						$conditionDescriptor->rightPartType="aroperation";
					}
				}
			}
			// echo ")";
		}
		else //boolean operator and or, not, 
		{
			// echo "<b>".$conditionDom->attributes->getNamedItem('type')->value."</b> (";
			$conditionDescriptor->bool = $conditionDom->attributes->getNamedItem('type')->value;
			foreach ($conditionDom->childNodes as $booleanOperation) {
				if ($booleanOperation->nodeName == "operator")
				{
					$subConditionDescriptor = self::getConditionDescriptor($booleanOperation);
					$conditionDescriptor->subConditionsList[]=$subConditionDescriptor;
					// echo ",";
				}
			}	
			// echo ")";
		}
		return $conditionDescriptor;
	}




	/**
	 * Enter description here...
	 *
	 * @param DOMElement $routingDom
	 * @return RoutingDescriptor
	 */
	private function getRoutingDescriptor(DOMElement $routingDom) {
		$routingDescriptor =  new RoutingDescriptor(__METHOD__);


		//todo change that be carreful here if there are embedded ifs .... 
		if($routingDom->hasChildNodes()) {

			foreach ($routingDom->childNodes as $childNode) {


				if ($childNode->nodeName == "condition")
				{
					$routingDescriptor->condition = $this->getConditionDescriptor($childNode);//conditionNode to "descriptor"
				}
				if ($childNode->nodeName == "then")
				{

					foreach ($childNode->childNodes as $thenNode) {
						if ($thenNode->nodeName == "goto")
						{
							$routingDescriptor->then = $thenNode->attributes->getNamedItem('itemGroup')->value;
						}
						if ($thenNode->nodeName == "routing")
						{
							//the followowing would work fine but since the routing in then is not supported in BQ I deactivate it
							//$routingDescriptor->then = $this->getRoutingDescriptor($thenNode);
							trigger_error("To activate the subrouting in then please uncomment, the import would be ok, but not the BQ".__LINE__);
						}
					}



				}
				if ($childNode->nodeName == "else")
				{

					foreach ($childNode->childNodes as $elseNode) {
						if ($elseNode->nodeName == "goto")
						{
							$routingDescriptor->else = $elseNode->attributes->getNamedItem('itemGroup')->value;
						}
						if ($elseNode->nodeName == "routing")
						{
							//recursive call
							$routingDescriptor->else = $this->getRoutingDescriptor($elseNode);

						}
					}


				}

			}
		}


		return $routingDescriptor;
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMElement $cCheckDescriptorDom
	 * @return ConsistencyCheckDescriptor
	 */
	private function getConsistencyCheckDescriptor(DOMElement $cCheckDescriptorDom){

		$cCheckDescriptor = new ConsistencyCheckDescriptor(__METHOD__);
		if($cCheckDescriptorDom->hasAttribute('blocking')){	
			$cCheckDescriptor->blocking = $cCheckDescriptorDom->attributes->getNamedItem('blocking')->value;
		}

		if($cCheckDescriptorDom->hasChildNodes()) {

			foreach ($cCheckDescriptorDom->childNodes as $childNode) {


				if ($childNode->nodeName == "condition")
				{
					$cCheckDescriptor->condition = $this->getConditionDescriptor($childNode);
				}
				if ($childNode->nodeName == "itemGroup")
				{
					if($childNode->hasAttribute('ref')){	

						//here we could try to put direct reference to the itemGroupDescriptor object ...
						$cCheckDescriptor->itemGroupList[] =$childNode->attributes->getNamedItem('ref')->value;
					}	

				}
				if ($childNode->nodeName == "message")
				{
					$cCheckDescriptor->message = $childNode->nodeValue;
				}


			}
		}

		return $cCheckDescriptor;
	}





	/**
	 * Short description of method validate
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param string
	 * @return boolean
	 */
	public function validate($schema)
	{
		$returnValue = (bool) false;

		// section 10-13-1--99--220c702e:11df82e7d86:-8000:0000000000000F16 begin

		$myDocument = new DomDocument();
		if (!$myDocument->load($this->filepath)) {
			common_Logger::d('Error Loading Document', array('ImportCapi'));
			return false;
		}
		libxml_use_internal_errors(true);

		if (!$myDocument->schemaValidate($schema)) {
			$errors = libxml_get_last_error();
			common_Logger::d('Error Parsing Document', array('ImportCapi'));
			$returnValue = false;
			common_Logger::d($errors->message, array('ImportCapi'));
			print_r($errors);
			$this->validationError = $errors->message; 

		}
		else {
			$returnValue = true;
			$this->isValidated = true;
		}


		// section 10-13-1--99--220c702e:11df82e7d86:-8000:0000000000000F16 end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method getValidationError
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return string
	 */
	public function getValidationError()
	{
		$returnValue = (string) '';

		// section 10-13-1--99--220c702e:11df82e7d86:-8000:0000000000000F42 begin
		$returnValue = $this->validationError;
		// section 10-13-1--99--220c702e:11df82e7d86:-8000:0000000000000F42 end

		return (string) $returnValue;
	}

} 

?>

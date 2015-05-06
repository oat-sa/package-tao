<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage\xml\marshalling;

use qtism\data\QtiComponent;
use qtism\data\content\BodyElement;
use qtism\data\storage\xml\Utils;
use \DOMDocument;
use \DOMElement;
use \RuntimeException;
use \InvalidArgumentException;
use \ReflectionClass;

abstract class Marshaller {
	
	/**
	 * The DOMCradle is a DOMDocument object which will be used as a 'DOMElement cradle'. It
	 * gives the opportunity to marshallers to create DOMElement that can be imported in an
	 * exported document later on.
	 * 
	 * @var DOMDocument
	 */
	private static $DOMCradle = null;
	
	/**
	 * A reference to the Marshaller Factory to use when creating other marshallers
	 * from this marshaller.
	 * 
	 * @var MarshallerFactory
	 */
	private $marshallerFactory = null;
	
	/**
	 * Get a DOMDocument to be used by marshaller implementations in order to create
	 * new nodes to be imported in a currenlty exported document.
	 * 
	 * @return DOMDocument A unique DOMDocument object.
	 */
	protected static function getDOMCradle() {
		if (empty(self::$DOMCradle)) {
			self::$DOMCradle = new DOMDocument('1.0', 'UTF-8');
		}
		
		return self::$DOMCradle;
	}
	
	/**
	 * Set the MarshallerFactory object to use to create other Marshaller objects.
	 * 
	 * @param MarshallerFactory $marshallerFactory A MarshallerFactory object.
	 */
	public function setMarshallerFactory(MarshallerFactory $marshallerFactory = null) {
		$this->marshallerFactory = $marshallerFactory;
	}
	
	/**
	 * Return the MarshallerFactory object to use to create other Marshaller objects.
	 * If no MarshallerFactory object was previously defined, a default 'raw' MarshallerFactory
	 * object will be returned.
	 * 
	 * @return MarshallerFactory A MarshallerFactory object.
	 */
	public function getMarshallerFactory() {
		if ($this->marshallerFactory === null) {
			$this->setMarshallerFactory(new MarshallerFactory());
		}
		
		return $this->marshallerFactory;
	}
	
	public function __call($method, $args) {
		if ($method == 'marshall' || $method == 'unmarshall') {
			if (count($args) >= 1) {
				if ($method == 'marshall') {
					$component = $args[0];
					if ($this->getExpectedQtiClassName() === '' || ($component->getQtiClassName() == $this->getExpectedQtiClassName())) {
						return $this->marshall($component);
					}
					else {
					    throw new RuntimeException("No marshaller implementation found while marshalling component with class name '" . $component->getQtiClassName());
					}
				}
				else {
					$element = $args[0];
					if ($this->getExpectedQtiClassName() === '' || ($element->localName == $this->getExpectedQtiClassName())) {
						return call_user_func_array(array($this, 'unmarshall'), $args);
					}
					else {
					    $nodeName = (($prefix = $element->prefix) === null) ? $element->localName : "${prefix}:" . $element->localName;
						throw new RuntimeException("No Marshaller implementation found while unmarshalling element '${nodeName}'.");
					}
				}
			}
			else {
				throw new RuntimeException("Method '${method}' only accepts a single argument.");
			}
		}
			
		throw new RuntimeException("Unknown method Marshaller::'${method}'.");
	}
	
	/**
	 * Get the attribute value of a given DOMElement object, cast in a given datatype.
	 * 
	 * @param DOMElement $element The element the attribute you want to retrieve the value is bound to.
	 * @param string $attribute The attribute name.
	 * @param string $datatype The returned datatype. Accepted values are 'string', 'integer', 'float', 'double' and 'boolean'.
	 * @throws InvalidArgumentException If $datatype is not in the range of possible values.
	 * @return mixed The attribute value with the provided $datatype, or null if the attribute does not exist in $element.
	 */
	public static function getDOMElementAttributeAs(DOMElement $element, $attribute, $datatype = 'string') {
		$attr = $element->getAttribute($attribute);
		
		if ($attr !== '') {
			switch ($datatype) {
				case 'string':
					return $attr;
				break;
				
				case 'integer':
					return intval($attr);
				break;
				
				case 'float':
					return floatval($attr);
				break;
				
				case 'double':
					return doubleval($attr);
				break;
				
				case 'boolean':
					return ($attr == 'true') ? true : false;
				break;
				
				default:
					throw new InvalidArgumentException("Unknown datatype '${datatype}'.");
				break;
			}
		}
		else {
			return null;
		}
	}
	
	/**
	 * Set the attribute value of a given DOMElement object. Boolean values will be transformed
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @param string $attribute An XML attribute name.
	 * @param mixed $value A given value.
	 */
	public static function setDOMElementAttribute(DOMElement $element, $attribute, $value) {
		switch (gettype($value)) {
			case 'boolean':
				$element->setAttribute($attribute, ($value === true) ? 'true' : 'false');
			break;
			
			default:
				$element->setAttribute($attribute, '' . $value);
			break;
		}
	}
	
	/**
	 * Set the node value of a given DOMElement object. Boolean values will be transformed as 'true'|'false'.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @param mixed $value A given value.
	 */
	public static function setDOMElementValue(DOMElement $element, $value) {
		switch (gettype($value)) {
			case 'boolean':
				$element->nodeValue = ($value === true) ? 'true' : 'false';
			break;
			
			default:
				$element->nodeValue = $value;
			break;
		}
	} 
	
	/**
	 * Get the first child DOM Node with nodeType attribute equals to XML_ELEMENT_NODE.
	 * This is very useful to get a sub-node without having to exclude text nodes, cdata,
	 * ... manually.
	 * 
	 * @param DOMElement $element A DOMElement object
	 * @return DOMElement|boolean A DOMElement If a child node with nodeType = XML_ELEMENT_NODE or false if nothing found.
	 */
	public static function getFirstChildElement($element) {
		$children = $element->childNodes;
		for ($i = 0; $i < $children->length; $i++) {
			$child = $children->item($i);
			if ($child->nodeType === XML_ELEMENT_NODE) {
				return $child;
			}
		}
		
		return false;
	}
	
	/**
	 * Get the children DOM Nodes with nodeType attribute equals to XML_ELEMENT_NODE.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @param boolean $withText Wether text nodes must be returned or not.
	 * @return array An array of DOMNode objects.
	 */
	public static function getChildElements($element, $withText = false) {
		$children = $element->childNodes;
		$returnValue = array();
		
		for ($i = 0; $i < $children->length; $i++) {
			if ($children->item($i)->nodeType === XML_ELEMENT_NODE || ($withText === true && ($children->item($i)->nodeType === XML_TEXT_NODE || $children->item($i)->nodeType === XML_CDATA_SECTION_NODE))) {
				$returnValue[] = $children->item($i);
			}
		}
		
		return $returnValue;
	}
	
	/**
	 * Get the child elements of a given element by tag name. This method does
	 * not behave like DOMElement::getElementsByTagName. It only returns the direct
	 * child elements that matches $tagName but does not go recursive.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @param mixed $tagName The name of the tags you would like to retrieve or an array of tags to match.
	 * @param boolean $exclude Wether the $tagName parameter must be considered as a blacklist.
	 * @return array An array of DOMElement objects.
	 */
	public static function getChildElementsByTagName($element, $tagName, $exclude = false) {
		if (!is_array($tagName)) {
			$tagName = array($tagName);
		}
		
		$rawElts = self::getChildElements($element);
		$returnValue = array();
		
		foreach ($rawElts as $elt) {
			if (in_array($elt->localName, $tagName) === !$exclude) {
				$returnValue[] = $elt;
			}
		}
		
		return $returnValue;
	}
	
	/**
	 * Get the string value of the xml:base attribute of a given $element. The method
	 * will return false if no xml:base attribute is defined for the $element or its value
	 * is empty.
	 * 
	 * @param DOMElement $element A DOMElement object you want to get the xml:base attribute value.
	 * @return false|string The value of the xml:base attribute or false if it could not be retrieved.
	 */
	public static function getXmlBase(DOMElement $element) {
	    
	    $returnValue = false;
	    if (($xmlBase = $element->getAttributeNS('http://www.w3.org/XML/1998/namespace', 'base')) !== '') {
	        $returnValue = $xmlBase;
	    }
	    return $returnValue;
	}
	
	/**
	 * Set the value of the xml:base attribute of a given $element. If a value is already
	 * defined for the xml:base attribute of the $element, the current value will be
	 * overriden by $xmlBase.
	 * 
	 * @param DOMElement $element The $element you want to set a value for xml:base.
	 * @param string $xmlBase The value to be set to the xml:base attribute of $element.
	 */
	public static function setXmlBase(DOMElement $element, $xmlBase) {
	    $element->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'base', $xmlBase);
	}
	
	/**
	 * Fill $bodyElement with the following bodyElement:
	 *
	 * * id
	 * * class
	 * * lang
	 * * label
	 *
	 * @param BodyElement $bodyElement The bodyElement to fill.
	 * @param DOMElement $element The DOMElement object from where the attribute values must be retrieved.
	 * @throws UnmarshallingException If one of the attributes of $element is not valid.
	 */
	protected static function fillBodyElement(BodyElement $bodyElement, DOMElement $element) {
	
	    try {
	        $bodyElement->setId($element->getAttribute('id'));
	        $bodyElement->setClass($element->getAttribute('class'));
	        $bodyElement->setLang($element->getAttributeNS('http://www.w3.org/XML/1998/namespace', 'lang'));
	        $bodyElement->setLabel($element->getAttribute('label'));
	    }
	    catch (InvalidArgumentException $e) {
	        $msg = "An error occured while filling the bodyElement attributes (id, class, lang, label).";
	        throw new UnmarshallingException($msg, $element, $e);
	    }
	}
	
	/**
	 * Fill $element with the attributes of $bodyElement.
	 *
	 * @param DOMElement $element The element from where the atribute values will be
	 * @param BodyElement $bodyElement The bodyElement to be fill.
	 */
	protected function fillElement(DOMElement $element, BodyElement $bodyElement) {
	
	    if (($id = $bodyElement->getId()) !== '') {
	        $element->setAttribute('id', $id);
	    }
	
	    if (($class = $bodyElement->getClass()) !== '') {
	        $element->setAttribute('class', $class);
	    }
	
	    if (($lang = $bodyElement->getLang()) !== '') {
	        $element->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:lang', $lang);
	    }
	
	    if (($label = $bodyElement->getLabel()) != '') {
	        $element->setAttribute('label', $label);
	    }
	}
	
	/**
	 * Marshall a QtiComponent object into its QTI-XML equivalent.
	 * 
	 * @param QtiComponent $component A QtiComponent object to marshall.
	 * @return DOMElement A DOMElement object.
	 * @throws MarshallingException If an error occurs during the marshalling process.
	 */
	abstract protected function marshall(QtiComponent $component);
	
	/**
	 * Unmarshall a DOMElement object into its QTI Data Model equivalent.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A QtiComponent object.
	 */
	abstract protected function unmarshall(DOMElement $element);
	
	/**
	 * Get the class name/tag name of the QtiComponent/DOMElement which can be handled
	 * by the Marshaller's implementation. 
	 * 
	 * Return an empty string if the marshaller implementation does not expect a particular
	 * QTI class name. 
	 * 
	 * @return string A QTI class name or an empty string.
	 */
	abstract public function getExpectedQtiClassName();
}

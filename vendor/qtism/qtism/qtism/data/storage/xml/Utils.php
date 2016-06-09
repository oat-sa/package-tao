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


namespace qtism\data\storage\xml;

use \DOMDocument;
use \DOMElement;
use \SplStack;

/**
 * A class providing XML utility methods.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
	
	/**
	 * Get the XML schema to use for a given QTI version.
	 *
	 * @return string A filename pointing at an XML Schema file.
	 */
	public static function getSchemaLocation($version = '2.1') {
		$dS = DIRECTORY_SEPARATOR;
	
		if ($version === '2.1') {
			$filename = dirname(__FILE__) . $dS . 'schemes' . $dS . 'imsqti_v2p1.xsd';
		}
		else {
			$filename = dirname(__FILE__) . $dS . 'schemes' . $dS . 'imsqti_v2p0.xsd';
		}
		
		return $filename;
	}
	
	/**
	 * Infer the QTI version of a given DOMDocument.
	 * 
	 * @param DOMDocument $document A DOMDocument object.
	 * @return string|boolean A QTI version (e.g. '2.1') or false if it could not be infered.
	 */
	public static function inferQTIVersion(DOMDocument $document) {
		$root = $document->documentElement;
		if (empty($root)) {
			return false;
		}
		else {
			switch (trim($root->lookupNamespaceURI(null))) {
				case 'http://www.imsglobal.org/xsd/imsqti_v2p1':
					return '2.1';
				break;
				
				case 'http://www.imsglobal.org/xsd/apip/apipv1p0/qtiitem/imsqti_v2p1':
                    return '2.1';				    
				break;
				
				case 'http://www.imsglobal.org/xsd/imsqti_v2p0':
					return '2.0';
				break;
				
				default:
					return false;
				break;
			}
		}
	}
	
	/**
	 * Change the name of $element into $name.
	 * 
	 * @param DOMElement $element A DOMElement object you want to change the name.
	 * @param string $name The new name of $element.
	 */
	public static function changeElementName(DOMElement $element, $name) {
		$newElement = $element->ownerDocument->createElement($name);
		
	    foreach ($element->childNodes as $child){
	        $child = $element->ownerDocument->importNode($child, true);
	        $newElement->appendChild($child);
	    }
	    
	    foreach ($element->attributes as $attrName => $attrNode) {
	    	if ($attrNode->namespaceURI === null) {
	    		$newElement->setAttribute($attrName, $attrNode->value);
	    	}
	    	else {
	    		$newElement->setAttributeNS($attrNode-> $namespaceURI, $attrNode->prefix . ':' . $attrName, $attrNode->value);
	    	}
	        
	    }
	    
	    $newElement->ownerDocument->replaceChild($newElement, $element);
	    return $newElement;
	}
	
	/**
	 * Anonimize a given DOMElement. By 'anonimize', we mean remove
	 * all namespace membership of an element and its child nodes.
	 * 
	 * For instance, <m:math display="inline"><m:mi>x</m:mi></m:math> becomes
	 * <math display="inline"><mi>x</mi></math>.
	 * 
	 * @param DOMElement $element The DOMElement to be anonimized.
	 * @return DOMElement The anonimized DOMElement copy of $element.
	 */
	public static function anonimizeElement(DOMElement $element) {
	    
	    $stack = new SplStack();
	    $traversed = array();
	    $children = array();
	    
	    $stack->push($element);
	    
	    while ($stack->count() > 0) {
	        $node = $stack->pop();
	        
	        if ($node->nodeType === XML_ELEMENT_NODE && $node->childNodes->length > 0 && in_array($node, $traversed, true) === false) {
	            array_push($traversed, $node);
	            $stack->push($node);
	            
	            for ($i = 0; $i < $node->childNodes->length; $i++) {
	                $stack->push($node->childNodes->item($i));
	            }
	        }
	        else if ($node->nodeType === XML_ELEMENT_NODE && $node->childNodes->length > 0 && in_array($node, $traversed, true) === true) {
	            // Build hierarchical node copy from the current node. All the attributes
	            // of $node must be copied into $newNode.
	            $newNode = $node->ownerDocument->createElement($node->localName);
	            
	            // Copy all attributes.
	            foreach ($node->attributes as $attr) {
	                $newNode->setAttribute($attr->localName, $attr->value);
	            }
	            
	            for ($i = 0; $i < $node->childNodes->length; $i++) {
	                $newNode->appendChild(array_pop($children));
	            }
	            
	            array_push($children, $newNode);
	        }
	        else {
	            array_push($children, $node->cloneNode());
	        }
	    }
	    
	    return $children[0];
	}
	
	/**
	 * Import all the child nodes of DOMElement $from to DOMElement $into.
	 * 
	 * @param DOMElement $from The source DOMElement.
	 * @param DOMElement $into The target DOMElement.
	 * @param boolean $deep Whether or not to import the whole node hierarchy.
	 */
	public static function importChildNodes(DOMElement $from, DOMElement $into, $deep = true) {
	    
	    for ($i = 0; $i < $from->childNodes->length; $i++) {
	        $node = $into->ownerDocument->importNode($from->childNodes->item($i), $deep);
	        $into->appendChild($node);
	    }
	}
	
	/**
	 * Import (gracefully i.e. by respecting namespaces) the attributes of DOMElement $from to
	 * DOMElement $into.
	 * 
	 * @param DOMElement $from The source DOMElement.
	 * @param DOMElement $into The target DOMElement.
	 */
	public static function importAttributes(DOMElement $from, DOMElement $into) {
	    
	    for ($i = 0; $i < $from->attributes->length; $i++) {
	        $attr = $from->attributes->item($i);
	        
	        if ($attr->localName !== 'schemaLocation') {
	    
	            if (empty($attr->namespaceURI) === false) {
	                $into->setAttributeNS($attr->namespaceURI, $attr->prefix . ':' . $attr->localName, $attr->value);
	            }
	            else {
	                $into->setAttribute($attr->localName, $attr->value);
	            }
	        }
	    }
	}
}

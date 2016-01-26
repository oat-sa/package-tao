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


use qtism\data\content\xhtml\Img;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for Img.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ImgMarshaller extends Marshaller {
	
	/**
	 * Marshall an Img object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An Img object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement('img');
        
        self::setDOMElementAttribute($element, 'src', $component->getSrc());
        self::setDOMElementAttribute($element, 'alt', $component->getAlt());

        if ($component->hasWidth() === true) {
            self::setDOMElementAttribute($element, 'width', $component->getWidth());
        }
        
        if ($component->hasHeight() === true) {
            self::setDOMElementAttribute($element, 'height', $component->getHeight());
        }
        
        if ($component->hasLongdesc() === true) {
            self::setDOMElementAttribute($element, 'longdesc', $component->getLongdesc());
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        self::fillElement($element, $component);
        
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to an XHTML img element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent n Img object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		
	    if (($src = self::getDOMElementAttributeAs($element, 'src')) !== null) {
	        
	        if (($alt = self::getDOMElementAttributeAs($element, 'alt')) === null) {
	            // The XSD does not force the 'alt' attribute to be non-empty,
	            // thus we consider the 'alt' attribute value as an empty string ('').
                $alt = '';
	        }
	        
	        $component = new Img($src, $alt);
	         
	        if (($longdesc = self::getDOMElementAttributeAs($element, 'longdesc')) !== null) {
	            $component->setLongdesc($longdesc);
	        }
	         
	        if (($height = self::getDOMElementAttributeAs($element, 'height', 'string')) !== null) {
	            if (stripos($height, '%') === false) {
	                $component->setHeight(intval($height));
	            }
	            else {
	                $component->setHeight($height);
	            }
	        }
	         
	        if (($width = self::getDOMElementAttributeAs($element, 'width', 'string')) !== null) {
	            if (stripos($width, '%') === false) {
	                $component->setWidth(intval($width));
	            }
	            else {
	                $component->setWidth($width);
	            }
	        }
	         
	        if (($xmlBase = self::getXmlBase($element)) !== false) {
	            $component->setXmlBase($xmlBase);
	        }
	         
	        self::fillBodyElement($component, $element);
	        return $component;
	    }
	    else {
	        $msg = "The 'mandatory' attribute 'src' is missing from element 'img'.";
	        throw new UnmarshallingException($msg, $element);
	    }
	}
	
	public function getExpectedQtiClassName() {
		return 'img';
	}
}
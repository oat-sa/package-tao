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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\IdentifierCollection;
use oat\taoQtiItem\model\qti\IdentifiedElement;
use oat\taoQtiItem\model\qti\exception\QtiModelException;

/**
 * The QTI_Item object represent the assessmentItem.
 * It's the main QTI object, it contains all the other objects and is the main
 * point
 * to render a complete item.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#section10042
 
 */
class IdentifierCollection
{
    /**
	 * The multi dimension array containing identified elements
	 * array(identifier => serial => oat\taoQtiItem\model\qti\IdentifiedElement)
	 * 
	 * 
	 */
	protected $elements = array();
	
    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  string identifier
     * @param  array options
     * @return mixed
     */
    public function __construct($identifiedElements = array())
    {
		$this->elements = array();
		if(is_array($identifiedElements)){
			$this->addMultiple($identifiedElements);
		}
    }
	
	public function add(IdentifiedElement $element){
		$identifier = $element->getIdentifier(false);
		if(!empty($identifier)){
			if(!isset($this->elements[$identifier])){
				$this->elements[$identifier] = array();
			}
			$this->elements[$identifier][$element->getSerial()] = $element;
		}
	}
	
	public function exists($identifier){
		
		$returnValue = false;
		
		if(is_string($identifier)){
			$returnValue = isset($this->elements[$identifier]);
		}else{
			throw new InvalidArgumentException('the identifier must be a string');
		}
		
		return $returnValue;
	}
	
	public function get($identifier = ''){
		
		$returnValue = array();
		
		if(empty($identifier)){
			$returnValue = $this->elements;
		}elseif($this->exists($identifier)){
			$returnValue = $this->elements[$identifier];
		}
		
		return $returnValue;
	}
	
	public function getUnique($identifier, $elementClass = ''){
		
		$returnValue = null;
		
		if($this->exists($identifier)){
			if(empty($elementClass)){
				if(count($this->elements[$identifier]) > 1){
					throw new QtiModelException('More than one identifier found, please try specifying the class of the element');
				}elseif(!empty($this->elements[$identifier])){
					$returnValue = reset($this->elements[$identifier]);
				}
			}else{
				$found = array();
				foreach($this->elements[$identifier] as $elt){
					if($elt instanceof $elementClass){
						$found[] = $elt;
					}
				}
				if(count($found) > 1){
					throw new QtiModelException('More than one identifier found with the class: '.$elementClass);
				}elseif(count($found) == 1){
					$returnValue = reset($found);
				}
			}
		}
		
		return $returnValue;
		
	}
	
	public function addMultiple($identifiedElements){
		
		if(!is_array($identifiedElements)){
			throw new InvalidArgumentException('the argument "identifiedElements" must be an array');
		}
		
		foreach($identifiedElements as $identifiedElement){
			if($identifiedElement instanceof IdentifiedElement){
				$this->add($identifiedElement);
			}elseif(is_array($identifiedElement)){
				$this->addMultiple($identifiedElement);
			}else{
				throw new InvalidArgumentException('must be either an identifier or an array');
			}
		}
	}
	
	public function merge(IdentifierCollection $identifierCollection){
		
		foreach($identifierCollection->get() as $elements){
			$this->addMultiple($elements);
		}
		
	}
	
}
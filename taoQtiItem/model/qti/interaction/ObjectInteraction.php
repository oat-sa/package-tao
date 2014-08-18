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
 */

namespace oat\taoQtiItem\model\qti\interaction;

use oat\taoQtiItem\model\qti\interaction\ObjectInteraction;
use oat\taoQtiItem\model\qti\interaction\BlockInteraction;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\Object;

/**
 * The QTI object interaction is a subclass of the QTI block interaction 
 * that primarily has a QTI Object as his content
 * It is not specifically described in the QTI standard as such, 
 * but is simply a way to group interactions that have the same content type
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class ObjectInteraction
    extends BlockInteraction
{
	/**
	 * The main content of an ObjectInteraction is a QTI object
	 * 
	 * @var oat\taoQtiItem\model\qti\Object 
	 */
	protected $object = null;
	
	public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
		parent::__construct($attributes, $relatedItem, $serial);
		$this->object = new Object();
	}
	
	public function setObject(Object $object){
		$this->object = $object;
	}
	
	public function getObject(){
		return $this->object;
	}
    
	public function toArray($filterVariableContent = false, &$filtered = array()){
	    $returnValue = parent::toArray($filterVariableContent, $filtered);
	    $returnValue['object'] = $this->object->toArray($filterVariableContent, $filtered);
	    return $returnValue;
	}
	
	protected function getTemplateQtiVariables(){
	    $variables = parent::getTemplateQtiVariables();
	    $variables['object'] = $this->object->toQTI();
	    return $variables;
	}
}
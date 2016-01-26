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

namespace oat\taoQtiItem\model\qti\choice;

use oat\taoQtiItem\model\qti\choice\GapImg;
use oat\taoQtiItem\model\qti\choice\Choice;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\Object;

/**
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class GapImg extends Choice
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'gapImg';

    /**
     * The image object of a gapImg must have a image MIME type
     * 
     * @var oat\taoQtiItem\model\qti\Object
     */
    protected $object = null;
    
    public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
		parent::__construct($attributes, $relatedItem, $serial);
		$this->object = new Object();
	}
    
    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\ObjectLabel'
                )
        );
    }

    public function setContent($content){
        if($content instanceof Object){
            $this->setObject($content);
        }else{
            throw new InvalidArgumentException('a GapImg can contain taoQTI_models_classes_QTI_Object only');
        }
    }

    public function getContent(){
        return $this->getObject();
    }

    public function setObject(Object $imgObject){
        //@todo: check MIME type
        $this->object = $imgObject;
    }

    public function getObject(){
        return $this->object;
    }

    protected function getTemplateQtiVariables(){
        //use the default qti.element.tpl.php
        $variables = parent::getTemplateQtiVariables();
        $variables['body'] = $this->object->toQTI();
        return $variables;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $returnValue = parent::toArray($filterVariableContent, $filtered);
        $returnValue['object'] = $this->object->toArray($filterVariableContent, $filtered);
        return $returnValue;
    }
}

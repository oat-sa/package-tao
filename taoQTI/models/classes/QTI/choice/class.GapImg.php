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

/**
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_choice_GapImg extends taoQTI_models_classes_QTI_choice_Choice
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
     * @var taoQTI_models_classes_QTI_Object
     */
    protected $object = null;
    
    public function __construct($attributes = array(), taoQTI_models_classes_QTI_Item $relatedItem = null, $serial = ''){
		parent::__construct($attributes, $relatedItem, $serial);
		$this->object = new taoQTI_models_classes_QTI_Object();
	}
    
    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_ObjectLabel'
                )
        );
    }

    public function setContent($content){
        if($content instanceof taoQTI_models_classes_QTI_Object){
            $this->setObject($content);
        }else{
            throw new InvalidArgumentException('a GapImg can contain taoQTI_models_classes_QTI_Object only');
        }
    }

    public function getContent(){
        return $this->getObject();
    }

    public function setObject(taoQTI_models_classes_QTI_Object $imgObject){
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

    public function toArray(){
        $returnValue = parent::toArray();
        $returnValue['object'] = $this->object->toArray();
        return $returnValue;
    }
}
/* end of class taoQTI_models_classes_QTI_choice_GapImg */
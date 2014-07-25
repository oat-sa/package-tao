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
 * The QTI RubricBlock
 *
 * @access public
 * @author Sam Sipasseuth, <sam.sipasseuth@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10252
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_RubricBlock extends taoQTI_models_classes_QTI_Element implements taoQTI_models_classes_QTI_container_FlowContainer
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'rubricBlock';

    /**
     * The content body of the rubric block
     * 
     * @var taoQTI_models_classes_QTI_container_ContainerStatic 
     */
    protected $body = null;

    public function __construct($attributes = array(), taoQTI_models_classes_QTI_Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->body = new taoQTI_models_classes_QTI_container_ContainerStatic();
    }

    public function getBody(){
        return $this->body;
    }

    protected function getUsedAttributes(){
        return array(
            'taoQTI_models_classes_QTI_attribute_View', //@todo: the cardinality actually is [0..*], make it this way!
            'taoQTI_models_classes_QTI_attribute_Use'
        );
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Sam Sipasseuth, <sam@taotesting.com>
     * @return string
     */
    public function toQTI(){
        throw new taoQTI_models_classes_QTI_QtiModelException('to be implemented');
    }

    /**
     * Serialize item object into json format, handy to be used in js
     * 
     */
    public function toArray(){
        throw new taoQTI_models_classes_QTI_QtiModelException('to be implemented');
    }

}
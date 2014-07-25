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
 * The QTI container interaction is a subclass of the QTI block interaction.
 * It is not specifically described in the QTI standard as such,
 * but is simply a way to group interactions that share the same content type,
 * basically a taoQTI_models_classes_QTI_container_ContainerStatic
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
abstract class taoQTI_models_classes_QTI_interaction_ContainerInteraction extends taoQTI_models_classes_QTI_interaction_BlockInteraction implements taoQTI_models_classes_QTI_container_FlowContainer
{

    /**
     * The body of such a ContainerInteraction is a QTI Container
     * 
     * @var taoQTI_models_classes_QTI_container_Container 
     */
    protected $body = null;

    /**
     * Define the class of taoQTI_models_classes_QTI_container_Container used
     * 
     * @var string
     */
    static protected $containerType = '';

    public function __construct($attributes = array(), taoQTI_models_classes_QTI_Item $relatedItem = null, $serial = ''){

        parent::__construct($attributes, $relatedItem, $serial);

        if(class_exists(static::$containerType)){
            $this->body = new static::$containerType('', $relatedItem);
        }else{
            throw new taoQTI_models_classes_QTI_QtiModelException('The container class does not exist: '.static::$containerType);
        }
    }

    public function getBody(){
        return $this->body;
    }
    
    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.containerInteraction.tpl.php';
    }
}
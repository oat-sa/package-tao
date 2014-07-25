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

namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\ResponseProcessing;
use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\response\TakeoverFailedException;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\ContentVariable;
use \common_Exception;

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoQTI
 
 */
abstract class ResponseProcessing extends Element implements ContentVariable
{
    
    protected static $qtiTagName = 'responseProcessing';
    
    /**
     * Short description of method create
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Item item
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    public static function create(Item $item){

        throw new common_Exception('create not implemented for '.get_called_class());

        return $returnValue;
    }

    /**
     * Short description of method takeoverFrom
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return oat\taoQtiItem\controller\QTIform\ResponseProcessing
     */
    public static function takeoverFrom(ResponseProcessing $responseProcessing, Item $item){

        throw new TakeoverFailedException('takeoverFrom not implemented for '.get_called_class());

        return $returnValue;
    }

    /**
     * Short description of method getForm
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Response response
     * @return tao_helpers_form_Form
     */
    public function getForm(ResponseDeclaration $response){
        return null;
    }

    /**
     * Short description of method takeNoticeOfAddedInteraction
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfAddedInteraction(Interaction $interaction, Item $item){
        
    }

    /**
     * Short description of method takeNoticeOfRemovedInteraction
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfRemovedInteraction(Interaction $interaction, Item $item){
        
    }

    /**
     * 
     * @return array
     */
    protected function getUsedAttributes(){
        //currently not used
        return array();
    }
    
    public function toFilteredArray(){
        return $this->toArray(true);
    }

}
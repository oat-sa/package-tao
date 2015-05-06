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

namespace oat\taoQtiItem\model\qti\feedback;

use oat\taoQtiItem\model\qti\feedback\Feedback;
use oat\taoQtiItem\model\qti\IdentifiedElement;
use oat\taoQtiItem\model\qti\container\FlowContainer;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\container\ContainerStatic;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use oat\taoQtiItem\model\qti\ContentVariable;

/**
 * The QTI_Feedback object represent one of the three available feedbackElements
 * (feedbackInline, feedbackBlock, feedbackModal
 *
 * @access public
 * @author Sam Sipasseuth, <sam.sipasseuth@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10243

 */
abstract class Feedback extends IdentifiedElement implements FlowContainer, ContentVariable
{

    protected $body = null;

    public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->body = new ContainerStatic('', $relatedItem); //@todo: implement interactive container
    }

    public function getBody(){
        return $this->body;
    }

    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\OutcomeIdentifier',
            'oat\\taoQtiItem\\model\\qti\\attribute\\ShowHideTemplateElement'
        );
    }

    /**
     * Check if the given new identifier is valid in the current state of the qti element
     * 
     * @param string $newIdentifier
     * @return booean
     * @throws InvalidArgumentException
     */
    public function isIdentifierAvailable($newIdentifier){

        $returnValue = false;

        if(empty($newIdentifier) || is_null($newIdentifier)){
            throw new InvalidArgumentException("newIdentifier must be set");
        }

        if(!empty($this->identifier) && $newIdentifier == $this->identifier){
            $returnValue = true;
        }else{
            $relatedItem = $this->getRelatedItem();
            if(is_null($relatedItem)){
                $returnValue = true; //no restriction on identifier since not attached to any qti item
            }else{

                $collection = $relatedItem->getIdentifiedElements();

                try{
                    $feedback = $collection->getUnique($newIdentifier, 'oat\\taoQtiItem\\model\\qti\\feedback\\Feedback');
                    if(is_null($feedback)){
                        $returnValue = true;
                    }
                }catch(QtiModelException $e){
                    //return false
                }
            }
        }

        return $returnValue;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){

        $data = parent::toArray($filterVariableContent, $filtered);

        if($filterVariableContent){
            $filtered[$this->getSerial()] = $data;
            $data = array(
                'serial' => $data['serial'],
                'qtiClass' => $data['qtiClass']
            );
        }

        return $data;
    }

    public function toFilteredArray(){
        return $this->toArray(true);
    }

}
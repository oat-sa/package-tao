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

use oat\taoQtiItem\model\qti\RubricBlock;
use oat\taoQtiItem\model\qti\Element;
use oat\taoQtiItem\model\qti\container\FlowContainer;
use oat\taoQtiItem\model\qti\ContentVariable;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\container\ContainerStatic;

/**
 * The QTI RubricBlock
 *
 * @access public
 * @author Sam Sipasseuth, <sam.sipasseuth@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10252
 
 */
class RubricBlock extends Element implements FlowContainer, ContentVariable
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
     * @var oat\taoQtiItem\model\qti\container\ContainerStatic 
     */
    protected $body = null;

    public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->body = new ContainerStatic();
    }

    public function getBody(){
        return $this->body;
    }

    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\View', //@todo: the cardinality actually is [0..*], make it this way!
            'oat\\taoQtiItem\\model\\qti\\attribute\\UseAttribute'
        );
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
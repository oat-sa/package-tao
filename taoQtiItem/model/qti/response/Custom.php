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
use oat\taoQtiItem\model\qti\response\Rule;
use oat\taoQtiItem\helpers\QtiSerializer;

/**
 * Short description of class oat\taoQtiItem\model\qti\response\Custom
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI

 */
class Custom extends ResponseProcessing implements Rule
{

    /**
     * contains the raw qti rule xml
     *
     * @access protected
     * @var string
     */
    protected $data = '';

    /**
     * Short description of attribute responseRules
     *
     * @access protected
     * @var array
     */
    protected $responseRules = array();

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule(){
        $returnValue = (string) '';

        foreach($this->responseRules as $responseRule){
            $returnValue .= $responseRule->getRule();
        }

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array responseRules
     * @param  string xml
     * @return mixed
     */
    public function __construct($responseRules, $xml){
        $this->responseRules = $responseRules;
        parent::__construct();
        $this->setData($xml, false);
    }

    public function setData($xml){
        $this->data = $xml;
    }

    public function getData(){
        return $this->data;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI(){
        return (string) $this->getData();
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){

        $returnValue = parent::toArray($filterVariableContent, $filtered);
        
        $rpSerialized = QtiSerializer::parseResponseProcessingXml(simplexml_load_string($this->data));
        $protectedData = array(
            'processingType' => 'custom',
            'data' => $this->data,
            'responseRules' => $rpSerialized['responseRules']
        );

        if($filterVariableContent){
            $filtered[$this->getSerial()] = $protectedData;
        }else{
            $returnValue = array_merge($returnValue, $protectedData);
        }

        return $returnValue;
    }

}
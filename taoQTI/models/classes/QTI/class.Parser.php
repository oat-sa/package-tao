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
 * The QTI Parser enables you to parse QTI item xml files and build the
 * objects
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_Parser extends tao_models_classes_Parser
{

    /**
     * Run the validation process
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = ''){
        
        if(empty($schema) || !file_exists($schema)){
            $schema = dirname(__FILE__).'/data/qtiv2p1/imsqti_v2p1.xsd';
        }
        $returnValue = parent::validate($schema);

        return (bool) $returnValue;
    }

    /**
     * load the file content, parse it  and build the a QTI_Item instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return taoQTI_models_classes_QTI_Item
     */
    public function load(){
        
        $returnValue = null;

        if(!$this->valid){
            libxml_use_internal_errors(true); //retrieve errors if no validation has been done previously
        }

        //load it using the DOMDocument library
        $xml = new DOMDocument();
        switch($this->sourceType){
            case self::SOURCE_FILE:
                $xml->load($this->source);
                break;
            case self::SOURCE_URL:
                $xmlContent = tao_helpers_Request::load($this->source, true);
                $xml->loadXML($xmlContent);
                break;
            case self::SOURCE_STRING:
                $xml->loadXML($this->source);
                break;
        }

        if($xml !== false){

            //build the item from the xml
            $parserFactory = new taoQTI_models_classes_QTI_ParserFactory($xml);
            try{
                $returnValue = $parserFactory->load();
            }catch(taoQTI_models_classes_QTI_UnsupportedQtiElement $e){
                $this->addError($e);
            }

            if(!$this->valid){
                $this->valid = true;
                libxml_clear_errors();
            }
        }else if(!$this->valid){
            $this->addErrors(libxml_get_errors());
            libxml_clear_errors();
        }

        return $returnValue;
    }

    protected function addError($error){

        $this->valid = false;

        if($error instanceof taoQTI_models_classes_QTI_UnsupportedQtiElement){
            $this->errors[] = array(
                'message' => '[Unsupported Qti Type] '.__('the following Qti Element is currently not supported in TAO').': '.$error->getType()
            );
        }else{
            parent::addError($error);
        }
    }

}
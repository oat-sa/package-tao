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

use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\qti\ParserFactory;
use oat\taoQtiItem\model\qti\exception\UnsupportedQtiElement;
use oat\taoQtiItem\model\ValidationService;
use \tao_models_classes_Parser;
use \DOMDocument;
use \tao_helpers_Request;

/**
 * The QTI Parser enables you to parse QTI item xml files and build the
 * objects
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
 
 */
class Parser extends tao_models_classes_Parser
{

    /**
     * Run the validation process
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  string $schema
     *
     * @return bool
     * @throws \Exception
     * @throws \common_Exception
     */
    public function validate($schema = ''){
        
        if (empty($schema)) {
            
            // Let's detect NS in use...
            $dom = new DOMDocument('1.0', 'UTF-8');

            switch($this->sourceType){
                case self::SOURCE_FILE:
                    $dom->load($this->source);
                    break;
                case self::SOURCE_URL:
                    $xmlContent = tao_helpers_Request::load($this->source, true);
                    $dom->loadXML($xmlContent);
                    break;
                case self::SOURCE_STRING:
                    $dom->loadXML($this->source);
                    break;
            }

            // Retrieve Root's namespace.
            if( $dom->documentElement == null ){
                $this->addError('dom is null and could not be validate');
                $returnValue = false;
            } else {
                $ns = $dom->documentElement->lookupNamespaceUri(null);
                $servicemanager = $this->getServiceManager();
                $validationService = $servicemanager->get(ValidationService::SERVICE_ID);
                $schemas = $validationService->getContentValidationSchema($ns);
                \common_Logger::i("The following schema will be used to validate: '" . $schemas[0] . "'.");

                $validSchema = $this->validateMultiple($schemas);
                $returnValue = $validSchema !== '';
            }
        } elseif(!file_exists($schema)) {
            throw new \common_Exception('no schema found in the location '.$schema);
        } else {
            $returnValue = parent::validate($schema);
        }

        return (bool) $returnValue;
    }
    
    /**
     * load the file content, parse it  and build the a QTI_Item instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param boolean resolveXInclude
     * @return \oat\taoQtiItem\model\qti\Item
     */
    public function load($resolveXInclude = false){
        
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

            $basePath = '';
            if($this->sourceType == self::SOURCE_FILE || $this->sourceType == self::SOURCE_URL){
                $basePath = dirname($this->source).'/';
            }
            //build the item from the xml
            $parserFactory = new ParserFactory($xml, $basePath);
            try{
                $returnValue = $parserFactory->load();
            }catch(UnsupportedQtiElement $e){
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

        if($error instanceof UnsupportedQtiElement){
            $this->errors[] = array(
                'message' => '[Unsupported Qti Type] '.__('the following Qti Element is currently not supported in TAO').': '.$error->getType()
            );
        }else{
            parent::addError($error);
        }
    }

    protected function getServiceManager(){
        return ServiceManager::getServiceManager();
    }
}
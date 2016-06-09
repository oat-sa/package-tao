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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\qti\ManifestParser;
use oat\taoQtiItem\model\qti\ManifestParserFactory;
use oat\taoQtiItem\model\ValidationService;
use \tao_models_classes_Parser;
use \tao_helpers_Request;

/**
 * Enables you to parse and validate an imsmanifest.xml file. 
 * You can load a list QTI_Resources from the parsed file.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @author Joel Bout <joel@taotesting.com>
 * @author Somsack Sipasseuth <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
 
 */
class ManifestParser
    extends tao_models_classes_Parser
{

    /**
     * Validate the manifest against an XML Schema Definition.
     *
     * @access public
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = '')
    {
		if (empty($schema)) {

			// Let's detect NS in use...
			$dom = new \DOMDocument('1.0', 'UTF-8');

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
				$schemas = $validationService->getManifestValidationSchema($ns);

				$validSchema = $this->validateMultiple($schemas);
				$returnValue = $validSchema !== '';
			}
		} elseif(!file_exists($schema)) {
			throw new \common_Exception('no schema found in the location '.$schema);
		} else {
			\common_Logger::i("The following schema will be used to validate imsmanifest.xml: '" . $schema . "'.");
			$returnValue = parent::validate($schema);
		}

		return (bool) $returnValue;
    }

    /**
     * Extract the resources informations about the items 
     * and build a list a QTI_Resource
     *
     * @access public
     * @return array
     */
    public function load()
    {
        $returnValue = array();

    	//load it using the SimpleXml library
        $xml = false;
    	switch($this->sourceType){
    	    
    		case self::SOURCE_FILE:
    		    $xml = simplexml_load_file($this->source);
    		break;
    		
    		case self::SOURCE_URL:
    			$xmlContent = tao_helpers_Request::load($this->source, true);
    			$xml = simplexml_load_string($xmlContent);
    		break;
    		
    		case self::SOURCE_STRING:
    			$xml = simplexml_load_string($this->source);
    		break;
    	}
    	
    	if ($xml !== false) {
    		
    		//get the QTI Item's resources from the imsmanifest.xml
    		$returnValue = ManifestParserFactory::getResourcesFromManifest($xml);
    		
    		if (!$this->valid) {
    			$this->valid = true;
    			libxml_clear_errors();
    		}
    	}
    	else if (!$this->valid) {
    		$this->addErrors(libxml_get_errors());
    		libxml_clear_errors();
    	}
        
        return (array) $returnValue;
    }

	protected function getServiceManager(){
		return ServiceManager::getServiceManager();
	}

}
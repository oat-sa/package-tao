<?php
/**  
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
*/

/**
 *  QTI test Creator Controller.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package taoQtiTest
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_Creator extends tao_actions_CommonModule {

        /**
         * Render the creator base view
         */
	public function index(){
            
            $labels = array();
            $testUri   =  $this->getRequestParameter('uri');
            $testModel = new taoQtiTest_models_classes_TestModel();

            $items = $testModel->getItems(new core_kernel_classes_Resource(tao_helpers_Uri::decode($testUri)));
            foreach($items as $item){
                $labels[$item->getUri()] = $item->getLabel();
            }
            $this->setData('labels', json_encode(tao_helpers_Uri::encodeArray($labels, tao_helpers_Uri::ENCODE_ARRAY_KEYS)));
            
            $this->setData('loadUrl', _url('getTest', null, null, array('uri' => $testUri)));
            $this->setData('saveUrl', _url('saveTest', null, null, array('uri' => $testUri)));
            $this->setData('itemsUrl', _url('get', 'Items'));
            $this->setData('identifierUrl', _url('getIdentifier', null, null, array('uri' => $testUri)));
            
            $this->setView('creator.tpl');
	}
        
        /**
         * Get json's test content, the uri of the test must be provided in parameter 
         */
        public function getTest(){
            $test = $this->getCurrentTest();
            $qtiTestService = taoQtiTest_models_classes_QtiTestService::singleton();
            
            $this->setContentHeader('application/json', 'UTF-8');
            echo $qtiTestService->getJsonTest($test);
        }
        
        /**
         * Save a test, test uri and 
         * The request must use the POST method and contains
         * the test uri and a json string that represents the QTI Test in the model parameter.
         */
        public function saveTest(){
            $saved = false;
            if($this->isRequestPost() && $this->getRequest()->accept('application/json')){
                if($this->hasRequestParameter('model')){

                    $parameters = $this->getRequest()->getRawParameters();

                    $test = $this->getCurrentTest();
                    $qtiTestService = taoQtiTest_models_classes_QtiTestService::singleton();

                    $saved = $qtiTestService->saveJsonTest($test, $parameters['model']);
                }
            }
            $this->setContentHeader('application/json', 'UTF-8');
            echo json_encode(array('saved' => $saved));
        }
        
        
        public function getIdentifier(){
            $response = array();
            if($this->getRequest()->accept('application/json')){
                
                //we need the model as well to keep consistency with the client
                if($this->hasRequestParameter('model') && $this->hasRequestParameter('qti-type')){
                    
                    $parameters = $this->getRequest()->getRawParameters();
                    
                    $qtiTestService = taoQtiTest_models_classes_QtiTestService::singleton();
                    $doc = $qtiTestService->fromJson($parameters['model']);
                    
                    $identifier = $qtiTestService->getIdentifierFor($doc, $this->getRequestParameter('qti-type'));
                    
                    $response = array('identifier' => $identifier);
                }
            }
            $this->setContentHeader('application/json', 'UTF-8');
            echo json_encode($response);
        }
	
	
	/**
	 * Returns the test that is being authored
	 * 
	 * @throws tao_models_classes_MissingRequestParameterException
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentTest(){
	    if (!$this->hasRequestParameter('uri')) {
	        throw new tao_models_classes_MissingRequestParameterException('uri');
	    }
	    return new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	}
	
}

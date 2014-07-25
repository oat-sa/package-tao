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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Author an QTI test
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQtiTest
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_Authoring extends tao_actions_CommonModule {

    /**
     * Display a very basic authoring interface
     */
	public function index()
	{
	    $test = $this->getCurrentTest();
            $genericTestService = taoTests_models_classes_TestsService::singleton();
            $qtiTestService = taoQtiTest_models_classes_QtiTestService::singleton();

            $itemSequence = array();
            $itemUris = array();
            $i = 0;
            foreach($qtiTestService->getItems($test) as $item){
                    $itemUris[] = $item->getUri();
                    $itemSequence[$i] = array(
                            'uri'   => tao_helpers_Uri::encode($item->getUri()),
                            'label' => $item->getLabel()
                    );
                    $i++;
            }

            // data for item sequence, terrible solution
            // @todo implement an ajax request for labels or pass from tree to sequence
            $allItems = array();
            foreach($genericTestService->getAllItems() as $itemUri => $itemLabel){
                    $allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
            }

            //get the test options
            $testOptions = $qtiTestService->getQtiTestOptions($test);
            //prefix the test's options
            foreach($testOptions as $key => $value){
                $this->setData('options_'.$key, $value);
            }

            //reformat duration to user friendly strings
            foreach($testOptions as $key => $value){
                if($value instanceof qtism\common\datatypes\Duration){
                    $testOptions[$key] = $this->durationToTime($value);
                } 
            }

            $this->setData('uri', $test->getUri());
            $this->setData('allItems', json_encode($allItems));
            $this->setData('itemSequence', $itemSequence);
            
            foreach($testOptions as $key => $value){
                $this->setData('option_'.$key, $value);
            }
            
            // data for generis tree form
            $this->setData('relatedItems', json_encode(tao_helpers_Uri::encodeArray($itemUris)));
            $openNodes = tao_models_classes_GenerisTreeFactory::getNodesToOpen($itemUris, new core_kernel_classes_Class(TAO_ITEM_CLASS));
            
            $this->setData('rootNode', TAO_ITEM_CLASS);
            $this->setData('openNodes', $openNodes);
            $this->setData('saveUrl', _url('saveItems'));
            $this->setData('itemsUrl', _url('getItems', 'Tests', 'taoQtiTest'));
            $this->setData('qtiItemModel', tao_helpers_Uri::encode(TAO_ITEM_MODEL_QTI));
            
            $this->setView('authoring.tpl');
	}
	
	/**
	 * Create a QTI test with the specified items
	 */
	public function saveItems()
	{
	    $test = $this->getCurrentTest();
	    $items = array();
	    foreach (tao_helpers_form_GenerisTreeForm::getSelectedInstancesFromPost() as $uri) {
	        $items[] = new core_kernel_classes_Resource($uri);
	    }
        $testOptions = array();
	    foreach ($this->getRequestParameters() as $key => $value) {
        //the items URIs
            if (substr($key, 0, strlen('instance_')) == 'instance_') {
	            $items[] = new core_kernel_classes_Resource(tao_helpers_Uri::decode($value));
	        
                //the times 
                } else if (preg_match ("/-time$/", $key)) {
                    $testOptions[$key] = $this->timetoDuration($value);
                    
                //other options
                } else {
                    $testOptions[$key] = $value;
                }
	    }
	    
	    $qtiTestService = taoQtiTest_models_classes_QtiTestService::singleton();
	    $success = $qtiTestService->saveQtiTest($test, $items, $testOptions);
	    
	    echo json_encode(array('saved'	=> $success));
	}
        
        /**
         * Converts a time string to an ISO8601 duration
         * @todo should be part of an helper or a utility lib
         * @param string $time as hh:mm:ss 
         * @return string 
         */
        private function timetoDuration($time){
            $duration = 'PT';
            if(preg_match ("/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/", $time)){
                $timeTokens = explode(':', $time);
                $duration .= intval($timeTokens[0]).'H'
                            .intval($timeTokens[1]).'M'
                            .intval($timeTokens[2]).'S';
            } else {
                $duration .= '0S';
            }
            return $duration;
        }
        
        /**
         * Format a duration to a string time
         * @todo should be part of an helper or a utility lib
         * @param Duration $duration
         * @return string time hh:mm:ss 
         */
        private function durationToTime(qtism\common\datatypes\Duration $duration){
            $time = '';
            if(!is_null($duration) && $duration->getSeconds(true) > 0){
                $hours = ($duration->getHours() > 9) ? $duration->getHours() : '0'.$duration->getHours();
                $minutes = ($duration->getMinutes() > 9) ? $duration->getMinutes() : '0'.$duration->getMinutes();
                $seconds = ($duration->getSeconds() > 9) ? $duration->getSeconds() : '0'.$duration->getSeconds();
                $time = $hours.':'.$minutes.':'.$seconds;
            } 
            return $time;
        }
	
	/**
	 * Returns the test that is being authored
	 * 
	 * @throws tao_models_classes_MissingRequestParameterException
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentTest()
	{
	    if (!$this->hasRequestParameter('uri')) {
	        throw new tao_models_classes_MissingRequestParameterException('uri');
	    }
	    return new core_kernel_classes_Resource($this->getRequestParameter('uri'));
	}
	
}

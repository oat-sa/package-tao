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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoTestLinear\controller;

use oat\tao\helpers\TreeHelper;
use tao_actions_CommonModule;
use core_kernel_classes_Resource;
use core_kernel_classes_Class;
use oat\taoTestLinear\model\TestModel;
use tao_helpers_Uri;
use tao_helpers_Request;
use tao_helpers_form_GenerisTreeForm;

/**
 * Controller for actions related to the authoring of the linear test model
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoTests
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class Authoring extends tao_actions_CommonModule {

	/**
     * Renders the auhtoring for simple tests
     */
	public function index(){

        $test = new \core_kernel_classes_Resource($this->getRequestParameter('uri'));
        
        $model = new TestModel();

        $itemSequence = array();
        $itemUris = array();
        $counter = 1;
        foreach($model->getItems($test) as $item){
            $itemUris[] = $item->getUri();
            $itemSequence[$counter] = array(
                'uri' 	=> tao_helpers_Uri::encode($item->getUri()),
                'label' => $item->getLabel()
            );
            $counter++;
        }
        
		// data for item sequence, terrible solution
		// @todo implement an ajax request for labels or pass from tree to sequence
		$allItems = array();
		foreach(\taoTests_models_classes_TestsService::singleton()->getAllItems() as $itemUri => $itemLabel){
			$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
		}


        $config = $model->getConfig($test);
        $checked = (isset($config['previous']))? $config['previous'] : false;
        $testConfig['previous'] = array('label' => __('Allow test-taker to go back in test'), 'checked' => $checked);

		$this->setData('uri', $test->getUri());
    	$this->setData('allItems', json_encode($allItems));
		$this->setData('itemSequence', $itemSequence);
		$this->setData('testConfig', $testConfig);

		// data for generis tree form
		$this->setData('relatedItems', json_encode(tao_helpers_Uri::encodeArray($itemUris)));

		$openNodes = TreeHelper::getNodesToOpen($itemUris, new core_kernel_classes_Class(TAO_ITEM_CLASS));
		$this->setData('itemRootNode', TAO_ITEM_CLASS);
		$this->setData('itemOpenNodes', $openNodes);
		$this->setData('saveUrl', _url('saveItems', 'Authoring', 'taoTestLinear'));
        $this->setView('Authoring/index.tpl');
    }

	/**
	 * save the related items from the checkbox tree or from the sequence box
	 * @return void
	 */
	public function saveItems()
	{
	    $test = new \core_kernel_classes_Resource($this->getRequestParameter('uri'));
	     
		if(!tao_helpers_Request::isAjax()){
			throw new \Exception("wrong request mode");
		}

        $itemUris = tao_helpers_form_GenerisTreeForm::getSelectedInstancesFromPost();
        foreach($this->getRequestParameters() as $key => $value) {
            if(preg_match("/^instance_/", $key)){
                $itemUris[] = tao_helpers_Uri::decode($value);
            }
        }

        $config = array('previous' => ($this->getRequestParameter('previous') === "true"));
        $testContent = array('itemUris' => $itemUris, 'config' => $config);
        $model = new TestModel();
        $saved = $model->save($test, $testContent);
        $this->returnJson(array('saved'	=> $saved));
    }

}

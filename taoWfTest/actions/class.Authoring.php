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
 * 
 */

/**
 * Controller for actions related to the authoring of the simple test model
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoTests
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoWfTest_actions_Authoring extends tao_actions_SaSModule {

	/**
	 * (non-PHPdoc)
	 * @see tao_actions_SaSModule::getClassService()
	 */
	protected function getClassService() {
		return taoTests_models_classes_TestsService::singleton();
	}
	
	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct()
	{

		parent::__construct();

		//the service is initialized by default
		$this->service = taoWfTest_models_classes_WfTestService::singleton();
	}

	/**
	 * save the related items from the checkbox tree or from the sequence box
	 * @return void
	 */
	public function saveItems()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;

        $candidates = tao_helpers_form_GenerisTreeForm::getSelectedInstancesFromPost();
		foreach($this->getRequestParameters() as $key => $value) {
		    if(preg_match("/^instance_/", $key)){
		        $candidates[] = tao_helpers_Uri::decode($value);
		    }
		}
		$items = array();
		foreach($candidates as $uri){
			$item = new core_kernel_classes_Resource($uri);
			$itemModel = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			$supported = false;
			if (!is_null($itemModel)) {
				foreach ($itemModel->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODELTARGET_PROPERTY)) as $targeturi) {
					if ($targeturi == TAO_ITEM_ONLINE_TARGET) {
						$supported = true;
						break;
					}
				}
			}
			if ($supported) {
				array_push($items, $item);
			} else {
				throw new common_Exception($item->getLabel().' cannot be added to a test');
			}
		}
		if($this->service->setTestItems($this->getCurrentInstance(), $items)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}

}
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
 * Copyright (c) 2013 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 * 
 */
 
/**
 * This module runs the items
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 */
class taoItems_actions_ItemRunner extends tao_actions_ServiceModule {
	
	public function index(){

		$userId = common_session_SessionManager::getSession()->getUserUri();
		if(is_null($userId)){
			throw new common_exception_Error('No user is logged in');
		}
		$lang = core_kernel_classes_Session::singleton()->getDataLanguage();

		
		if ($this->hasRequestParameter('serviceCallId')) {
    		$serviceCallId = $this->getRequestParameter('serviceCallId');
            $variableData = tao_models_classes_service_state_Service::singleton()->get($userId, $serviceCallId);
    		$this->setData('storageData', array(
    			'serial'	=> $serviceCallId,
    			'data'		=> is_null($variableData) ? array() : $variableData
    		));
		}
		
		$directoryResource = new core_kernel_file_File(tao_helpers_Uri::decode($this->getRequestParameter('itemPath')));
		$basepath = $directoryResource->getAbsolutePath().DIRECTORY_SEPARATOR;
		if (!file_exists($basepath.$lang) && file_exists($basepath.DEFAULT_LANG)) {
		    $lang = DEFAULT_LANG;
		}

		$baseUrl = taoDelivery_models_classes_RuntimeAccess::getAccessProvider()->getAccessUrl($directoryResource);
		
		$this->setData('itemPath', $baseUrl.$lang.'/index.html');
		$this->setData('itemId', $this->getRequestParameter('itemUri'));
		$this->setData('resultJsApi', $this->getResultServerApi());
		$this->setData('resultJsApiPath', $this->getResultServerApiPath());
		
		$this->selectView();
		$this->selectWebFolder();	
	}
	
	public function access() {
		$provider = new tao_models_classes_fsAccess_ActionAccessProvider();
		$filename = $provider->decodeUrl($_SERVER['REQUEST_URI']);
		if (file_exists($filename)) {
			$mimeType = tao_helpers_File::getMimeType($filename);
			header('Content-Type: '.$mimeType);
			$fp = fopen($filename, 'rb');
 			fpassthru($fp);
		} else {
			throw new tao_models_classes_FileNotFoundException($filename);
		}
	}
	
	
	/**
	 * Get the ResultServer API call to be used by the item.
	 * 
	 * @return string A string representing JavaScript instructions.
	 */
	protected function getResultServerApi() {
	    return taoResultServer_helpers_ResultServerJsApi::getServiceApi();
	}
	
	/**
	 * Get the path from ROOT_URL where the ResultServerApi implementation is found on the server.
	 * 
	 * @return string
	 */
	protected function getResultServerApiPath() {
	    return 'taoResultServer/views/js/ResultServerApi.js';
	}
	
	/**
	 * The implementation of this method calls ItemRunner::setView in order to
	 * select the view to be displayed.
	 */
	protected function selectView() {
	    $this->setView('runtime/item_runner.tpl', 'taoItems');
	}
	
	/**
	 * The implementation of this method calls ItemRunner::setData with key
	 * 'webFolder' to give the web folder path to the view.
	 * 
	 */
	protected function selectWebFolder() {
	    $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
	    $this->setData('webFolder', $ext->getConstant('BASE_WWW'));
	}
}

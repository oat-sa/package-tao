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
 * 
 */

/**
 * The Main Module of tao development tools
 * 
 * @package taoDevTools
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
class taoDevTools_actions_WfEngineTool extends tao_actions_Main {
    
    public function __construct() {
        parent::__construct();
        // load the wfEngine extension
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('wfEngine');
    }
    
	public function deleteProcessesExecutions() {

	    $processExecutionUri = '';
	    if(isset($_GET['processExecutionUri'])){
	        $processExecutionUri = tao_helpers_Uri::decode($_GET['processExecutionUri']);
	    }
	    
	    $finishedOnly = false;
	    if(isset($_GET['finishedOnly'])){
	        if($_GET['finishedOnly'] != 'false'){
	            $finishedOnly = (bool) $_GET['finishedOnly'];
	        }
	    }
	    
	    $deleteDeliveryHistory = false;
	    if(isset($_GET['deliveryHistory'])){
	        if($_GET['deliveryHistory'] != 'false'){
	            $deleteDeliveryHistory = (bool) $_GET['deliveryHistory'];
	        }
	    }
	    
	    $processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
	    $result = false;
	    if(!empty($processExecutionUri)){
	        if($processExecutionUri=='all' || $processExecutionUri='*'){
	            $result = $processExecutionService->deleteProcessExecutions(array(), $finishedOnly);
	    
	            if($deleteDeliveryHistory){
	                //delete all delivery history:
	                $deliveryHistoryClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAODelivery.rdf#History');
	                foreach($deliveryHistoryClass->getInstances() as $history){
	                    $history->delete();
	                }
	            }
	        }else{
	            $processExecution = new core_kernel_classes_Resource($processExecutionUri);
	            $result = $processExecutionService->deleteProcessExecution($processExecution, $finishedOnly);
	        }
	    }
	    
	    if($result === true){
	        echo 'deletion completed';
	    }else{
	        echo 'deletion failed';
	    }
	     
	}
}
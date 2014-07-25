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
?>
<?
/*
two parameters accepted via GET:
- processExecutionUri	: the url encoded uri of the process execution resource to be deleted (encoded with the static method 'tao_helpers_Uri::encode'). 
						'*' or 'all' targets ALL process executions in the ontology (default: empty)
- finishedOnly			: if set to true, removes the targeted executions only if they are in the 'finished' state (default: false)

exemples:
To delete all process intance resources
http://localhost/wfEngine/scripts/deleteProcessExecutions.php?processExecutionUri=*

To delete a single instance, which must be finished:
http://localhost/wfEngine/scripts/deleteProcessExecutions.php?processExecutionUri=http%3A%2F%2Flocalhost%2Fmytao__rdf%23i1302606160013308900&finishedOnly=1
*/
require_once dirname(__FILE__).'/../includes/raw_start.php';

$userService = core_kernel_users_Service::singleton();
$userService->login(SYS_USER_LOGIN, SYS_USER_PASS, new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole'));

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
?>
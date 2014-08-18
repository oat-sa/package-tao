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
<?php

require_once dirname(__FILE__).'/../includes/raw_start.php';

class wfDebugger{
        
        protected $diplayPropertyLabels = true;
        protected $propertyLabels = array();
        protected $localNS = '';
        protected $order = 'resource';
        protected $unserialize = 0;
        protected $br = '<br/>';
        
        public function __construct($options = array()){
                common_user_auth_Service::singleton()->startSession(new core_kernel_users_GenerisUser(new core_kernel_classes_Resource(TAO_INSTALLATOR)));
				$this->processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
                $this->localNS = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
                $this->unserialize = (isset($options['unserialize']))? (int)$options['unserialize']:0;
        }
        
        public function getPropertyLabels(){
                return $this->propertyLabels;
        }
        
        protected function data_dump($data, $sortProperty = null){
                if($data instanceof core_kernel_classes_Resource){

                        $propertyValues = array();
                        $epoch = '';
                        
                        foreach($data->getRdfTriples()->getIterator() as $triple){

                                $key = $triple->predicate;
                                
                                if(!is_null($sortProperty) && $sortProperty->getUri() == $key){
                                        $epoch = $triple->epoch;
                                }
                                
                                if($this->diplayPropertyLabels){
                                        if(!isset($this->propertyLabels[$key])){
                                                $property = new core_kernel_classes_Property($key);
                                                $this->propertyLabels[$key] = trim(strip_tags($property->getLabel()));
                                        }
                                        
                                        if(!empty($this->propertyLabels[$key])){
                                                $key = $this->propertyLabels[$key];
                                        }
                                }
                                
                                $value = $triple->object;
                                
                                if($this->unserialize && preg_match('/^a:[0-9]{1,2}:{/i', $value)){
                                        $unserializedValue = unserialize($value);
                                        if(!is_null($unserializedValue)){
                                                $desc = (is_array($unserializedValue))?'array '.count($unserializedValue).': '.  implode(', ', array_keys($unserializedValue)):'object';
                                                if($this->unserialize == 2){
                                                        $value = array("unserialized ({$desc})" => $unserializedValue);
                                                }else{
                                                        $value = array("unserialized ({$desc})" => $value);
                                                }
                                        }
                                }
                                        
                                if(isset($propertyValues[$key])){
                                        if(is_array($propertyValues[$key])){
                                                $propertyValues[$key][] = $value;
                                        }else{

                                                $propertyValues[$key] = array($propertyValues[$key], $value);
                                        }
                                }else{
                                        $propertyValues[$key] = $value;
                                }
                                
                        }

                        $returnValue = array(
                            'resource'  => "{$data->getLabel()} ({$data->getUri()})",
                            'properties' => $propertyValues
                        );
                        
                        if(!empty($epoch)){
                                $returnValue['epoch'] = $epoch;
                        }
                            
                        var_dump($returnValue);
                }else{
                        var_dump($data);
                }
        }

        public function getData($key){

                $returnValue = null;

                if(isset($_GET[$key])){
                        if(preg_match('/^i/i', $_GET[$key])){
                                $returnValue = new core_kernel_classes_Resource($this->localNS.$_GET[$key]);
                        }else{
                                $returnValue = new core_kernel_classes_Resource(urldecode($_GET[$key]));
                        }
                }

                return $returnValue;
        }
		
		public function debugProcessExecution(core_kernel_classes_Resource $processExecution){
			
			$activityExecutionsData = $this->processExecutionService->getAllActivityExecutions($processExecution);
			var_dump($activityExecutionsData);
		}
		
        public function debugProcessInstance(core_kernel_classes_Resource $processInstance){

               $apiModel = core_kernel_impl_ApiModelOO::singleton();

               if(!is_null($processInstance)){

                        echo 'process instance:'.$this->br;
                        $this->data_dump($processInstance);

                        $processInstances = $processInstance->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTTOKEN));
                        echo "tokens ({$processInstances->count()}):".$this->br;
                        $sortedProcessInstances = array();
                        foreach($processInstances->getIterator() as $token){
                                $sortedProcessInstances[$token->getUri()] = $token;
                        }
                        krsort($sortedProcessInstances);
                        foreach($sortedProcessInstances as $token){
                                $this->data_dump($token);
                        }

                        $activityExecutions = $apiModel->getSubject(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION, $processInstance->getUri());
                        echo "activity executions ({$activityExecutions->count()}):".$this->br;
                        $sortedActivityExecutions = array();
                        $timeSortingProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY);
                        
                        $i = 0;
                        foreach($activityExecutions->getIterator() as $activityExecution){
                                if(!is_null($timeSortingProperty)){
                                    $created = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED));
                                    $key = is_null($created) ? $i : (string)$created; 
                                    $sortedActivityExecutions[$key] = $activityExecution;
                                }else{
                                    $sortedActivityExecutions[$activityExecution->getUri()] = $activityExecution;
                                }
                        }
                        krsort($sortedActivityExecutions);
                        
                        
                        foreach($sortedActivityExecutions as $time => $activityExecution){
                                echo $this->br;
                                echo date('Y-m-d H:i:s', $time).":";
                                $this->data_dump($activityExecution);
                        }
                        
                }

        }

}


$options = array(
    'unserialize' => (isset($_GET['unserialize']))? intval($_GET['unserialize']):0 
);

$wfDebugger = new wfDebugger($options);
$processInstance = $wfDebugger->getData('processInstance');
$processDefinition = $wfDebugger->getData('processDefinition');
$activityExecution = $wfDebugger->getData('activityExecution');

echo "Debugging : ".$processInstance->getLabel();

if(!is_null($processInstance)){
	$wfDebugger->debugProcessExecution($processInstance);
}

//echo "Property labels:";
//var_dump($wfDebugger->getPropertyLabels());

?>

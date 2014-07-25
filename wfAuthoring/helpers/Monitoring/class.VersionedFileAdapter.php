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
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_helpers_Monitoring_VersionedFileAdapter
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        
		
		if (isset($this->data[$rowId])) {

			if (isset($this->data[$rowId][$columnId])) {
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		} else {
			
			if(common_Utils::isUri($rowId)){
				
				$this->data[$rowId] = array();
				
				$activityExecution = new core_kernel_classes_Resource($rowId);

				$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
				$processVariableService = wfEngine_models_classes_VariableService::singleton();
				$unit = $processVariableService->get('unitUri', $activityExecution);
				$countryCode = (string) $processVariableService->get('countryCode', $activityExecution);
				$languageCode = (string) $processVariableService->get('languageCode', $activityExecution);

				if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
					
					$activity = $activityExecutionService->getExecutionOf($activityExecution);

					//check if it is the translation activity or not:
					$xliff = null;
					$vff = null;
					if($activity->getLabel() == 'Translate'){
						$xliffWorkingProperty = $this->getTranslationFileProperty('xliff_working', $countryCode, $languageCode);
						if(!is_null($xliffWorkingProperty)){
							$xliff = $unit->getOnePropertyValue($xliffWorkingProperty);
						}
						$vffWorkingProperty = $this->getTranslationFileProperty('vff_working', $countryCode, $languageCode);
						if(!is_null($vffWorkingProperty)){
							$vff = $unit->getOnePropertyValue($vffWorkingProperty);
						}
					}else{
						$xliffProperty = $this->getTranslationFileProperty('xliff', $countryCode, $languageCode);
						if(!is_null($xliffProperty)){
							$xliff = $unit->getOnePropertyValue($xliffProperty);
						}
						$vffProperty = $this->getTranslationFileProperty('vff', $countryCode, $languageCode);
						if(!is_null($vffProperty)){
							$vff = $unit->getOnePropertyValue($vffProperty);
						}
					}
					
					if($xliff instanceof core_kernel_classes_Resource){
						$xliff = new core_kernel_versioning_File($xliff);
						$this->data[$rowId]['xliff'] = $xliff->getUri();
						$this->data[$rowId]['xliff_version'] = (string) $processVariableService->get('xliff', $activityExecution);
					}else{
						$this->data[$rowId]['xliff'] = 'n/a';
						$this->data[$rowId]['xliff_version'] = 'n/a';
					}
					
					if($vff instanceof core_kernel_classes_Resource){
						$vff = new core_kernel_versioning_File($vff);
						$this->data[$rowId]['vff'] = $vff->getUri();
						$this->data[$rowId]['vff_version'] = (string) $processVariableService->get('vff', $activityExecution);
					}
					else{
						$this->data[$rowId]['vff'] = 'n/a';
						$this->data[$rowId]['vff_version'] = 'n/a';
					}
				}else{
					$this->data[$rowId] = array(
						'xliff' => 'n/a',
						'xliff_version' => 'n/a',
						'vff' => 'n/a',
						'vff_version' => 'n/a'
						);
				}
				
				if (isset($this->data[$rowId][$columnId])) {
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
			
		}
        

        return $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_VersionedFileAdapter */

?>
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class wfAuthoring_helpers_Monitoring_TranslationMetaAdapter
    extends tao_helpers_grid_Cell_Adapter
{
	public function getValue($rowId, $columnId, $data = null)
    {
		$returnValue = null;
		 
		if(isset($this->data[$rowId])){
			
			//return values:
			if(isset($this->data[$rowId][$columnId])){
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		}else{
		
			if(common_Utils::isUri($rowId)){
				
				$processInstance = new core_kernel_classes_Resource($rowId);
				
				//TODO: property uris need to be set in the constant files:
				$unit = $processInstance->getOnePropertyValue(TranslationProcessHelper::getProperty('unitUri'));
				$countryCode = $processInstance->getOnePropertyValue(TranslationProcessHelper::getProperty('CountryCOde'));
				$langCode = $processInstance->getOnePropertyValue(TranslationProcessHelper::getProperty('LanguageCode'));
				
				$this->data[$rowId] = array(
					'unit' => is_null($unit)?'n/a':$unit->getLabel(),
					'country' => ($countryCode instanceof core_kernel_classes_Literal)?$countryCode->literal:'n/a',
					'language' => ($langCode instanceof core_kernel_classes_Literal)?$langCode->literal:'n/a'
				);
				
				if(isset($this->data[$rowId][$columnId])){
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
			
		}
		
		return $returnValue;
	}
}

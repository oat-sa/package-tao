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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_models_grids_adaptors_UserProperty
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 
 */
class tao_models_grids_adaptors_UserProperty
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        
		
		//@TODO : to be delegated to the LazyAdapter : columnNames, adapterOptions, excludedProperties
		if (isset($this->data[$rowId])) {

			//return values:
			if (isset($this->data[$rowId][$columnId])) {
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		} else {
			
			if (common_Utils::isUri($rowId)) {
				
				$user = new core_kernel_classes_Resource($rowId);
				$this->data[$rowId] = array();
				
				$fastProperty = array(
					RDFS_LABEL,
					PROPERTY_USER_LOGIN,
					PROPERTY_USER_FIRSTNAME,
					PROPERTY_USER_LASTNAME,
					PROPERTY_USER_MAIL,
					PROPERTY_USER_UILG,
					PROPERTY_USER_DEFLG
				);
				
				$properties = array();
				$propertyUris = array_diff($fastProperty, $this->excludedProperties);
				foreach($propertyUris as $activityExecutionPropertyUri){
					$properties[] = new core_kernel_classes_Property($activityExecutionPropertyUri);
				}
				
				$propertiesValues = $user->getPropertiesValues($properties);
				
				foreach($propertyUris as $propertyUri){
					
					$value = null;
					if(isset($propertiesValues[$propertyUri]) && count($propertiesValues[$propertyUri])){
						$value = reset($propertiesValues[$propertyUri]);
					}
					
					switch($propertyUri){
						case RDFS_LABEL:
						case PROPERTY_USER_LOGIN:
						case PROPERTY_USER_FIRSTNAME:
						case PROPERTY_USER_LASTNAME:
						case PROPERTY_USER_MAIL:
						case PROPERTY_USER_LOGIN:
						case PROPERTY_USER_UILG:
						case PROPERTY_USER_DEFLG:{
							$this->data[$rowId][$propertyUri] = ($value instanceof core_kernel_classes_Resource) ? $value->getLabel() : (string) $value;
							break;
						}
					}	
				}
				
				//get roles:
				if(!in_array('roles', $this->excludedProperties)){
					$i=0;
					foreach ($user->getTypes() as $role) {
						if ($role instanceof core_kernel_classes_Resource) {
							if($i){
								$this->data[$rowId]['roles'] .= ', ';
							}else{
								$this->data[$rowId]['roles'] = '';
							}
							$this->data[$rowId]['roles'] .= $role->getLabel();
						}
						$i++;
					}
				}
				
				if (isset($this->data[$rowId][$columnId])) {
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
		}
		
        

        return $returnValue;
    }

}

?>
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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 */

namespace oat\tao\model\lock\implementation;

use oat\tao\model\lock\Lock;
use core_kernel_classes_Resource;
use common_exception_InconsistentData;

/**
 * Implements Lock using a simple property in the ontology for the lock storage
 *
 **/
class OntoLockData extends SimpleLock {
    
    /**
     * 
     * @param string $json
     * @throws common_exception_InconsistentData
     * @return tao_models_classes_LockData
     */
    public static function getLockData($json){
    	$array = json_decode($json, true);
    	if(isset($array['resource']) && isset($array['owner']) && isset($array['epoch'])){
    		$resource = new core_kernel_classes_Resource($array['resource']);
    		$ownerId = $array['owner'];
    		$epoch = $array['epoch'];
    		return new self($resource,$ownerId,$epoch);
    	}
    	else {
    		throw new common_exception_InconsistentData('LockData should contain a resource, owner and epoch, one data is missing');
    	}
    }

    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return core_kernel_classes_Resource
     */
    public function getOwner(){
        return new core_kernel_classes_Resource($this->getOwnerId());
    }
    
    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return string
     */
    public function toJson(){  	
    	return json_encode( 
    		array(
    			'resource' => $this->getResource()->getUri(), 
    			'owner' => $this->getOwnerId(),
    			'epoch' => $this->getCreationTime()	
    		)
    	);
    }
}

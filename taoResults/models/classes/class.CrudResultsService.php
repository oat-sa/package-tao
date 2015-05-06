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
 * 
 */

/**
 * .Crud services implements basic CRUD services, orginally intended for REST controllers/ HTTP exception handlers
 *  Consequently the signatures and behaviors is closer to REST and throwing HTTP like exceptions
 *  
 *
 * 
 */
class taoResults_models_classes_CrudResultsService extends tao_models_classes_CrudService {

    protected $resultClass = null;
    protected $resultService = null;

    public function __construct() {
        parent::__construct();
        $this->resultClass = new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
        $this->resultService = taoResults_models_classes_ResultsService::singleton();
    }

    public function getRootClass() {
        return $this->resultClass;
    }

    public function get($uri) {
        $returnData = array();
        foreach ($this->resultService->getVariables(new core_kernel_classes_Resource($uri), null, false) as $itemVariable => $variables) {
        
            foreach ($variables as $key=>$variable) {
                $returnData[$itemVariable][]=$this->resultService->getVariableData($variable);
            }
        };
        return $returnData;
        //return $this->resultService->getVariables(new core_kernel_classes_Resource($uri));
    }



    public function delete($resource) {
       throw new common_exception_NoImplementation();
        return true;
    }

    public function deleteAll() {
        throw new common_exception_NoImplementation();
    }

   

    public function update($uri = null, $propertiesValues = array()) {
        throw new common_exception_NoImplementation();
        //throw new common_exception_NotImplemented();
    }

}

?>

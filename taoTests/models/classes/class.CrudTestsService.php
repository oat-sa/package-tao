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
class taoTests_models_classes_CrudTestsService
    extends tao_models_classes_CrudService
{
   protected $testClass = null;

    public function __construct(){
		parent::__construct();
		$this->testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
    }

    public function getRootClass(){
		return $this->testClass;
	}

    
    public function delete( $resource){
        taoTests_models_classes_TestsService::singleton()->deleteTest(new core_kernel_classes_Resource($resource));
        return true;
    }

    /**
     * @param array parameters an array of property uri and values
     */
    public function createFromArray(array $propertiesValues){
	
		if (!isset($propertiesValues[RDFS_LABEL])) {
			$propertiesValues[RDFS_LABEL] = "";
		}
		$type = isset($propertiesValues[RDF_TYPE]) ? $propertiesValues[RDF_TYPE] : $this->getRootClass();
		$label = $propertiesValues[RDFS_LABEL];
		//hmmm
		unset($propertiesValues[RDFS_LABEL]);
		unset($propertiesValues[RDF_TYPE]);
		$resource =  parent::create($label, $type, $propertiesValues);
		return $resource;
    }


    
} /* end of class taoGroups_models_classes_GroupsService */

?>

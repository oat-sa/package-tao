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
class taoSubjects_models_classes_CrudSubjectsService
    extends tao_models_classes_CrudService
{
   protected $subjectClass = null;

    public function __construct(){
		parent::__construct();
		$this->subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
    }

    public function getRootClass(){
		return $this->subjectClass;
	}

    
    public function delete( $resource){
        taoSubjects_models_classes_SubjectsService::singleton()->deleteSubject(new core_kernel_classes_Resource($resource));
        //parent::delete($resource)
        return true;
    }

    /**
     * @param array parameters an array of property uri and values
     */
    public function createFromArray($propertiesValues =array()){
	
		//mandatory parameters
		if (!isset($propertiesValues[PROPERTY_USER_LOGIN])) {
			throw new common_exception_MissingParameter("login");
		}
		if (!isset($propertiesValues[PROPERTY_USER_PASSWORD])) {
			throw new common_exception_MissingParameter("password");
		}
		//default values
		if (!isset($propertiesValues[PROPERTY_USER_UILG])) {
			$propertiesValues[PROPERTY_USER_UILG] = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
		}
		if (!isset($propertiesValues[PROPERTY_USER_DEFLG])) {
			$propertiesValues[PROPERTY_USER_DEFLG] = tao_helpers_I18n::getLangResourceByCode(DEFAULT_LANG);
		}
		if (!isset($propertiesValues[RDFS_LABEL])) {
			$propertiesValues[RDFS_LABEL] = "";
		}
		//check if login already exists
		$userService = tao_models_classes_UserService::singleton();
		if ($userService->loginExists($propertiesValues[PROPERTY_USER_LOGIN])) {
			throw new common_exception_PreConditionFailure("login already exists");
		}
		$propertiesValues[PROPERTY_USER_PASSWORD] = core_kernel_users_AuthAdapter::getPasswordHash()->encrypt($propertiesValues[PROPERTY_USER_PASSWORD]);
		$type = isset($propertiesValues[RDF_TYPE]) ? $propertiesValues[RDF_TYPE] : $this->getRootClass();
		$label = $propertiesValues[RDFS_LABEL];
		//hmmm
		unset($propertiesValues[RDFS_LABEL]);
		unset($propertiesValues[RDF_TYPE]);

		$resource =  parent::create($label, $type, $propertiesValues);
		
		$roleProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
		$subjectRole = new core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY);
		$resource->setPropertyValue($roleProperty, $subjectRole);
		return $resource;
    }

	public function update($uri = null,$propertiesValues = array()){
		if (is_null($uri)){
		    throw new common_exception_MissingParameter("uri");
		}
		if (isset($propertiesValues[PROPERTY_USER_LOGIN])) {
			throw new common_exception_PreConditionFailure("login update not allowed");
		}
		if (isset($propertiesValues[PROPERTY_USER_PASSWORD])) {
			$propertiesValues[PROPERTY_USER_PASSWORD] = core_kernel_users_AuthAdapter::getPasswordHash()->encrypt($propertiesValues[PROPERTY_USER_PASSWORD]);
		}
		parent::update($uri, $propertiesValues);
		//throw new common_exception_NotImplemented();
	}
    
} /* end of class taoSubjects_models_classes_SubjectsService */

?>

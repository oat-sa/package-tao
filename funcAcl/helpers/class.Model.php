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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Helper to read/write the action/module model
 * of tao from/to the ontology
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class funcAcl_helpers_Model
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * creates a model of the modules and their actions
     * of an extentions in the ontology to be used to assign access rights
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Extension extension
     * @return mixed
     */
    public static function spawnExtensionModel( common_ext_Extension $extension)
    {
    	common_Logger::i('Spawning Module/Action model for extension '.$extension->getId());
		
    	$aclExt = self::addExtension($extension->getId());
    	foreach ($extension->getAllModules() as $moduleName => $moduleClass) {
			//Introspection, get public method
			try {
				$reflector = new ReflectionClass($moduleClass);
				$actions = array();
				foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
					if (!$m->isConstructor() && !$m->isDestructor() && is_subclass_of($m->class, 'Module') && $m->name != 'setView') {
						$actions[] = $m->name;
					}
				}
				if (count($actions) > 0) {
					$aclModule = self::addModule($aclExt, $moduleName);
					foreach ($actions as $action) {
						self::addAction($aclModule, $action);
					}
				}
			}
			catch (ReflectionException $e) {
			    common_Logger::w('ReflectionException in '.$moduleClass.' on line '.$e->getLine().' : '.$e->getMessage());
			}
		}
    }

    /**
     * adds a module to the ontology
     * and grant access to the role taomanager
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extension
     * @param  string name
     * @return core_kernel_classes_Resource
     */
    private static function addExtension($name)
    {
        $returnValue = null;

        $moduleClass = new core_kernel_classes_Class(CLASS_ACL_EXTENSION);
        $specialURI = FUNCACL_NS.'#e_'.$name;
        $resource = new core_kernel_classes_Resource($specialURI);
        if ($resource->exists()) {
        	$returnValue = $resource;
        } else {
	        $returnValue = $moduleClass->createInstance($name,'',$specialURI);
	        $returnValue->setPropertiesValues(array(
	        	PROPERTY_ACL_EXTENSION_ID			=> $name
	        ));
        }
        
        return $returnValue;
    }
    
    /**
     * adds a module to the ontology
     * and grant access to the role taomanager
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extension
     * @param  string name
     * @return core_kernel_classes_Resource
     */
    private static function addModule(core_kernel_classes_Resource $extension, $name)
    {
        $returnValue = null;

        
        $moduleClass = new core_kernel_classes_Class(CLASS_ACL_MODULE);
        list($prefix, $extensionName) = explode('_', substr($extension->getUri(), strrpos($extension->getUri(), '#')));
        
        $specialURI = FUNCACL_NS.'#m_'.$extensionName.'_'.$name;
        $returnValue = $moduleClass->createInstance($name,'',$specialURI);
         $returnValue->setPropertiesValues(array(
        	PROPERTY_ACL_MODULE_EXTENSION	=> $extension,
        	PROPERTY_ACL_MODULE_ID			=> $name
        ));
        

        return $returnValue;
    }


    /**
     * adds an action to the ontology
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource module
     * @param  string action
     * @return core_kernel_classes_Resource
     */
    private static function addAction( core_kernel_classes_Resource $module, $action)
    {
        $returnValue = null;

        
        $actionClass = new core_kernel_classes_Class(CLASS_ACL_ACTION);

        list($prefix, $extensionName, $moduleName) = explode('_', substr($module->getUri(), strrpos($module->getUri(), '#')));
        $specialURI = FUNCACL_NS.'#a_'.$extensionName.'_'.$moduleName.'_'.$action;
        $returnValue = $actionClass->createInstance($action,'',$specialURI);
        $returnValue->setPropertiesValues(array(
        	PROPERTY_ACL_ACTION_MEMBEROF	=> $module,
        	PROPERTY_ACL_ACTION_ID			=> $action
        ));
        

        return $returnValue;
    }

    /**
     * helper for getModules
     * @return Resource the acl representation of the extension
     */
    private static function getAclExtension($extensionID) {
    	$specialURI = FUNCACL_NS.'#e_'.$extensionID;
        return new core_kernel_classes_Resource($specialURI);
    }
    
    /**
     * returns the modules of an extension from the ontology
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extensionID
     * @return array
     */
    public static function getModules($extensionID)
    {
        $returnValue = array();

        
        $moduleClass = new core_kernel_classes_Class(CLASS_ACL_MODULE);
		$returnValue = $moduleClass->searchInstances(array(
			PROPERTY_ACL_MODULE_EXTENSION	=> self::getAclExtension($extensionID)
		), array( 
			'like'	=> false
		));
        

        return (array) $returnValue;
    }

    /**
     * returns the actions of a module from the ontology
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource module
     * @return array
     */
    public static function getActions( core_kernel_classes_Resource $module)
    {
        $returnValue = array();

        
        $moduleClass = new core_kernel_classes_Class(CLASS_ACL_ACTION);
		$returnValue = $moduleClass->searchInstances(array(PROPERTY_ACL_ACTION_MEMBEROF => $module));
        

        return (array) $returnValue;
    }

} /* end of class funcAcl_helpers_Model */

?>
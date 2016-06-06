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
use oat\tao\model\accessControl\func\FuncHelper;
use oat\tao\helpers\ControllerHelper;

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

    /**
     * returns the modules of an extension from the ontology
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extensionID
     * @return array
     */
    public static function getModules($extensionId)
    {
        $returnValue = array();
        foreach (ControllerHelper::getControllers($extensionId) as $controllerClassName) {
            $shortName = strpos($controllerClassName, '\\') !== false
                ? substr($controllerClassName, strrpos($controllerClassName, '\\')+1)
                : substr($controllerClassName, strrpos($controllerClassName, '_')+1)
            ;
            $uri = funcAcl_models_classes_AccessService::singleton()->makeEMAUri($extensionId, $shortName);
            $returnValue[$uri] = new core_kernel_classes_Resource($uri);
        }
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
        $controllerClassName = funcAcl_helpers_Map::getControllerFromUri($module->getUri());
        try {
            foreach (ControllerHelper::getActions($controllerClassName) as $actionName) {
                $uri = funcAcl_helpers_Map::getUriForAction($controllerClassName, $actionName);
                $returnValue[$uri] = new core_kernel_classes_Resource($uri);
            }
        } catch (ReflectionException $e) {
            // unknown controller, no actions returned
        }
        return (array) $returnValue;
    }

}
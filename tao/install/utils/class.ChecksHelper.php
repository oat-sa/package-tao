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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               

/**
 * A helper to get the required checks for an extension
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @access public
 * @package tao
 
 *
 */
class tao_install_utils_ChecksHelper {

    /**
     * Get the ComponentCollection corresponding to the distribution. It
     * the configuration checks to perform for all extensions involved in the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param array $extensionIds
     * @return common_configuration_ComponentCollection
     */
    public static function getConfigChecker($extensionIds)
    {
        $returnValue = new common_configuration_ComponentCollection();
    
        // resolve dependencies
        foreach (self::getRawChecks($extensionIds) as $c){
            $checkArray[] = $c;
            $comp = common_configuration_ComponentFactory::buildFromArray($c);

            if (!empty($c['value']['id'])){
                $componentArray[$c['value']['id']] = $comp;
            }

            $returnValue->addComponent($comp);

            if (!empty($c['value']['silent']) && $c['value']['silent'] == true){
                $returnValue->silent($comp);
            }
        }
    
        // Deal with the dependencies.
        foreach ($checkArray as $config){
            if (!empty($config['value']['dependsOn']) && is_array($config['value']['dependsOn'])){
                foreach ($config['value']['dependsOn'] as $d){
                    // Find the component it depends on and tell the ComponentCollection.
                    if (!empty($componentArray[$config['value']['id']]) && !empty($componentArray[$d])){
                        $returnValue->addDependency($componentArray[$config['value']['id']], $componentArray[$d]);
                    }
                }
            }
        }
    
        return $returnValue;
    }
    
    public static function getRawChecks($extensionIds) {
        $checks = array();
        
        // resolve dependencies
        $toCheck = array();
        while (!empty($extensionIds)) {
            $ext = array_pop($extensionIds);
            $manifestPath = dirname(__FILE__) . '/../../../' . $ext . '/manifest.php';
            $dependencies = common_ext_Manifest::extractDependencies($manifestPath);
            $extensionIds = array_unique(array_merge($extensionIds, array_diff($dependencies, $toCheck)));
            $toCheck[] = $ext;
        }
        
        // We extract the checks to perform from the manifests
        // depending on the distribution.
        $checkArray = array(); // merge of all arrays describing checks in the manifests.
        $componentArray = array(); // array of Component instances. array keys are the IDs.
        
        foreach ($toCheck as $ext){
            $manifestPath = dirname(__FILE__) . '/../../../' . $ext . '/manifest.php';
            $checks = array_merge($checks, common_ext_Manifest::extractChecks($manifestPath));
        }
        return $checks;
    }
}
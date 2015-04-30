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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model\qti\metadata\ontology;

use oat\taoQtiItem\model\qti\metadata\MetadataInjector;
use oat\taoQtiItem\model\qti\metadata\MetadataInjectionException;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;
use \InvalidArgumentException;

class OntologyMetadataInjector implements MetadataInjector
{
    private $injectionRules;
    
    public function __construct()
    {
        $this->setInjectionRules(array());
    }
    
    public function addInjectionRule(array $path, $propertyUri, $value = null, $ontologyValue = null)
    {
        if (count($path) === 0) {
            $msg = "The path argument must be a non-empty array.";
            throw new InvalidArgumentException($msg);
        }
        
        $injectionRules = $this->getInjectionRules();
        
        $pathKey = implode('->', $path);
        if (isset($injectionRules[$pathKey]) === false) {
            $injectionRules[$pathKey] = array();
        }
        
        $injectionRules[$pathKey][] = array($propertyUri, $value, $ontologyValue);
        $this->setInjectionRules($injectionRules);
    }
    
    protected function setInjectionRules(array $injectionRules)
    {
        $this->injectionRules = $injectionRules;
    }
    
    protected function getInjectionRules()
    {
        return $this->injectionRules;
    }
    
    public function inject($target, array $values)
    {
        if (!$target instanceof core_kernel_classes_Resource) {
            $msg = "The given target is not an instance of core_kernel_classes_Resource.";
            throw new MetadataInjectionException($msg);
        }
        
        $injectionRules = $this->getInjectionRules();
        
        foreach ($values as $metadataValues) {
            foreach ($metadataValues as $metadataValue) {
                $pathKey = implode('->', $metadataValue->getPath());
                $lang = $metadataValue->getLanguage() ?: DEFAULT_LANG;
                
                if (($rule = $this->getRuleByValue($metadataValue->getPath(), $metadataValue->getValue())) !== false) {
                    // Direct Mapping.
                    $target->setPropertyValueByLg(new core_kernel_classes_Property($rule[0]), $rule[2], $lang);
                } elseif (($rule = $this->getRuleByPath($metadataValue->getPath())) !== false) {
                    // Direct Injection.
                    $target->setPropertyValueByLg(new core_kernel_classes_Property($rule[0]), $metadataValue->getValue(), $lang);
                }
            }
        }
    }
    
    protected function getRuleByValue($path, $value) 
    {
        $pathKey = Implode('->', $path);
        $rules = $this->getInjectionRules();
        
        if (isset($rules[$pathKey]) === true) {
            foreach ($rules[$pathKey] as $rule) {
                if ($rule[1] === $value) {
                    return $rule;
                }
            }
        }
        
        return false;
    }
    
    protected function getRuleByPath($path) {
        $pathKey = Implode('->', $path);
        $rules = $this->getInjectionRules();
        if (isset($rules[$pathKey]) === true) {
            return $rules[$pathKey][0];
        }
        
        return false;
    }
}
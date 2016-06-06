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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

class core_kernel_classes_ResourceFormatter
{
    

    
    public function getResourceDescription(core_kernel_classes_Resource $resource,$fromDefinition = true)
    {
        $returnValue = new stdClass;
        $properties =array();
        if ($fromDefinition){
            $types = $resource->getTypes();    
            foreach ($types as $type){
                foreach ($type->getProperties(true) as $property){
                    //$this->$$property->getUri() = array($property->getLabel(),$this->getPropertyValues());
                    $properties[$property->getUri()] = $property;
                }
            }
            //var_dump($properties);
            $properties = array_unique($properties);
            $propertiesValues =  $resource->getPropertiesValues($properties);
            if (count($propertiesValues)==0) {
                throw new common_exception_NoContent();
            }
            $propertiesValuesStdClasses = $this->propertiesValuestoStdClasses($propertiesValues);
        }
        else	//get effective triples and map the returned information into the same structure
        {
            $triples = $resource->getRdfTriples();
            if (count( $triples)==0) {
                throw new common_exception_NoContent();
            }
            foreach ($triples as $triple){
                $properties[$triple->predicate][] = common_Utils::isUri($triple->object)
                ? new core_kernel_classes_Resource($triple->object)
                : new core_kernel_classes_Literal($triple->object);
            }
            $propertiesValuesStdClasses = $this->propertiesValuestoStdClasses($properties);
        }
       
        $returnValue->uri = $resource->getUri();
        $returnValue->properties = $propertiesValuesStdClasses;
        return $returnValue;
    }
    
    /**
     * small helper provide more convenient data structure for propertiesValues for exchange
     * @return array
     */
    private function propertiesValuestoStdClasses($propertiesValues = null)
    {
        $returnValue =array();
        foreach ($propertiesValues as $uri => $values) {
            $propStdClass = new stdClass;
            $propStdClass->predicateUri = $uri;
            foreach ($values as $value){
                $stdValue = new stdClass;
                $stdValue->valueType = (get_class($value)=="core_kernel_classes_Literal") ? "literal" : "resource";
                $stdValue->value = (get_class($value)=="core_kernel_classes_Literal") ? $value->__toString() : $value->getUri();
                $propStdClass->values[]= $stdValue;
            }
            $returnValue[]=$propStdClass;
        }
        return $returnValue;
    }
}

?>
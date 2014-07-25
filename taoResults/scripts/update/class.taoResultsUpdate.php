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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 */
/**
 * Description of taoResultsUpdate
 *
 * @author plichart
 */

/**
 *
 * @author Patrick plichart, <patrick@taotesting.com>
 * @package taoResults
 
 */
class taoResults_scripts_update_taoResultsUpdate extends tao_scripts_Runner {

    public function preRun(){
        
    }

    public function run(){
        self::migrateAllResults();
    }

    public function postRun(){
    }


    
    private static function migrateAllResults(){

    $variableClass = new core_kernel_classes_Class("http://www.tao.lu/Ontologies/TAOResult.rdf#Variable");
    $variables = $variableClass->getInstances(true);
    
    foreach ( $variables as $variable) {
            $value = $variable->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
 
            if ((@unserialize($value) !== false)) {
                $value = unserialize($value);
                $variable->editPropertyValues(new core_kernel_classes_Property(RDF_VALUE), array(base64_encode($value)));
            }                
        }

    }
    }
?>
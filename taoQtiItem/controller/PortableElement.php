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
 */

namespace oat\taoQtiItem\controller;

use \core_kernel_classes_Resource;
use \tao_actions_CommonModule;
use \common_exception_Error;

class PortableElement extends tao_actions_CommonModule
{
    /**
     * Add required resources for a custom interaction (css, js) in the item directory
     * 
     * @throws common_exception_Error
     */
    public function addRequiredResources(){
        
        $typeIdentifier = $this->getRequestParameter('typeIdentifier');
        $itemUri = urldecode($this->getRequestParameter('uri'));
        $registryClass = urldecode($this->getRequestParameter('registryClass'));
        $resources = array();
        
        $item = new core_kernel_classes_Resource($itemUri);
        if(class_exists($registryClass)){
            $registry = new $registryClass();
            $resources = $registry->addRequiredResources($typeIdentifier, $item);
        }
        
        $this->returnJson(array(
            'success' => true,
            'resources' => $resources
        ));
    }

}
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
 * Copyright (c) 2013-2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

namespace oat\taoQtiItem\model\apip;

use oat\taoQtiItem\helpers\Apip;
use \tao_models_classes_Service;
use \taoItems_models_classes_ItemsService;

/**
 * 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class ApipService extends tao_models_classes_Service
{
    public function storeApipAccessibilityContent(\core_kernel_classes_Resource $item, \DOMDocument $originalDoc)
    {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        
        if (($apipContent = Apip::extractApipAccessibility($originalDoc)) !== null) {
            // Call ApipService to store the data separately.
            $finalLocation = $itemService->getItemFolder($item) . 'apip.xml';
            file_put_contents($finalLocation, $apipContent->saveXML());
            
            \common_Logger::i("APIP content stored at '${finalLocation}'.");
        }
    }
    
    public function getApipAccessibilityContent(\core_kernel_classes_Resource $item)
    {
        $apipContent = null;
        
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $finalLocation = $itemService->getItemFolder($item) . 'apip.xml';
        
        if (is_readable($finalLocation) === true) {
            $apipContent = new \DOMDocument('1.0', 'UTF-8');
            $apipContent->load($finalLocation);
            
            \common_Logger::i("APIP content retrieved at '${finalLocation}'.");
        }
        
        return $apipContent;
    }
    
    public function getDefaultApipAccessibilityContent(\core_kernel_classes_Resource $item)
    {
        // $item not in used but will be. Namespaces might depend on the APIP version in use.
        $content = new \DOMDocument('1.0', 'UTF-8');
        $content->loadXML('<apipAccessibility xmlns="http://www.imsglobal.org/xsd/apip/apipv1p0/imsapip_qtiv1p0" xmlns:apip="http://www.imsglobal.org/xsd/apip/apipv1p0/imsapip_qtiv1p0"/>');
        return $content;
    }
}

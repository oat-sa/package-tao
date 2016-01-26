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
namespace oat\taoDevTools\actions;

use oat\taoItems\model\pack\Packer;
use oat\taoItems\model\asset\Loader;
/**
 * Package visualisation
 */
class ItemTools extends \tao_actions_CommonModule {
    
    public function __construct() {
        // load item constants
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
    }
    
    public function viewPackage() {
        $item = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        $packer = new Packer($item);
        $package = $packer->pack();
        
        $json = json_encode($package, JSON_PRETTY_PRINT);
        
        // private so copy/paste
        $data = $package->JsonSerialize();
        $assets = array();
        foreach ($data['assets'] as $type => $typeAssets) {
            $assets = array_merge($assets, $typeAssets);
        }
        
        $this->setData('id', $item->getUri());
        $this->setData('assets', $assets);
        $this->setData('jsonPackage', htmlentities($json));
        $this->setView('ItemTools/viewPackage.tpl');
    }
    
    public function getAsset() {
        $item = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        $assetPath = $this->getRequestParameter('asset');
        
        $mimeType = \tao_helpers_File::getMimeType($assetPath, true);
        header('Content-Type: ' . $mimeType);
        
        $loader = new Loader($item);
        $content = $loader->getAssetContent($assetPath);
        echo $content;
    }
}
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoItems\model\pack;

use \core_kernel_classes_Resource;
use \common_Exception;
use \taoItems_models_classes_ItemsService;

/**
 * To allow packing of Item. The goal of the packaging is to represent the data needed
 * to run an item (ie. an ItemPack).
 *
 * @package taoQtiItem
 */
abstract class ItemPacker
{

    /**
     * Determines what type of assets should be packed as well as packer
     * @example array('css'=>'base64')
     * @var array
     */
    protected $assetEncoders = array( 'js'    => 'none',
                                      'css'   => 'none',
                                      'font'  => 'none',
                                      'img'   => 'none',
                                      'audio' => 'none',
                                      'video' => 'none');

    protected $nestedResourcesInclusion;

    public function __construct($assetEncoders = array(), $nestedResourcesInclusion = true)
    {
        $this->assetEncoders = array_merge($this->assetEncoders, $assetEncoders);
        $this->nestedResourcesInclusion = $nestedResourcesInclusion;
    }

    /**
     * Create a pack for an item.
     *
     * @param core_kernel_classes_Resource $item the item to pack
     * @param string $lang
     * @return \oat\taoItems\model\pack\ItemPack
     */
    abstract public function packItem(core_kernel_classes_Resource $item, $lang);


    /**
     * @param core_kernel_classes_Resource $item
     * @param $lang
     * @return string
     * @throws common_Exception
     */
    protected function getPath(core_kernel_classes_Resource $item, $lang = "")
    {
        $path = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $lang);
        return $path;

    }

    /**
     * @return array
     */
    protected function getAssetEncoders()
    {
        return $this->assetEncoders;
    }

    /**
     * @param array $assetEncoders
     */
    public function setAssetEncoders(array $assetEncoders)
    {
        $this->assetEncoders = $assetEncoders;
    }

    /**
     * @return boolean
     */
    public function isNestedResourcesInclusion()
    {
        return $this->nestedResourcesInclusion;
    }

    /**
     * @param boolean $nestedResourcesInclusion
     */
    public function setNestedResourcesInclusion($nestedResourcesInclusion)
    {
        $this->nestedResourcesInclusion = $nestedResourcesInclusion;
    }
}
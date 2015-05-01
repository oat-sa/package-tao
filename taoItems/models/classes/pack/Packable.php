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
 */

namespace oat\taoItems\model\pack;

use \core_kernel_classes_Resource;
use oat\taoItems\model\pack\ItemPack;

/**
 * To allow packing of Item. The goal of the packaging is to reprensent the data needed
 * to run an item (ie. an ItemPack).
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
interface Packable
{
    /**
     * Create a pack for an item.
     *
     * @param core_kernel_classes_Resource $item the item to pack
     * @param string $path the path of the item folder
     * @return ItemPack
     */
    public function packItem(core_kernel_classes_Resource $item, $path);
}

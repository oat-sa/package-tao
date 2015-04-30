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
 */

namespace oat\taoTests\models\pack;

use \core_kernel_classes_Resource;
use oat\taoTests\models\pack\TestPack;

/**
 * To allow packing of test. The goal of the packing is to reprensent the data needed
 * to run an test (ie. an TestPack).
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
interface Packable
{
    /**
     * Create a pack for an item.
     *
     * @param core_kernel_classes_Resource $test the test to pack
     * @return TestPack
     */
    public function packTest(core_kernel_classes_Resource $test);
}

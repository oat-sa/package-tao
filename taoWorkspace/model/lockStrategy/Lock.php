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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 */

namespace oat\taoWorkspace\model\lockStrategy;

use core_kernel_classes_Resource;
use oat\tao\model\lock\implementation\SimpleLock;

/**
 * Implements Lock using a basic property in the ontology storing the lock data
 *
 * @note It would be preferably static but we may want to have the polymorphism on lock but it would be prevented by explicit class method static calls.
 * Also if you nevertheless call it statically you may want to avoid the late static binding for the getLockProperty
 */
class Lock extends SimpleLock
{
    /**
     * @var core_kernel_classes_Resource
     */
    private $workCopy;
    
    public function __construct(core_kernel_classes_Resource $resource, $ownerId, $epoch, core_kernel_classes_Resource $workCopy)
    {
        parent::__construct($resource, $ownerId, $epoch);
        $this->workCopy = $workCopy;
    }
    
    /**
     * @return core_kernel_classes_Resource
     */
    public function getWorkCopy() {
        return $this->workCopy;
    }
}

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

namespace oat\taoOutcomeUi\helper;

use \core_kernel_classes_Resource;

/**
 * Helps you to change the label of a a result.
 *  
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 *
 */
class ResultLabel {

    private $result;
    private $testTaker;
    private $delivery;
   
    /**
     * Build the label.
     * 
     * @param core_kernel_classes_Resource $result
     * @param core_kernel_classes_Resource $testTaker
     * @param core_kernel_classes_Resource $delivery
     */ 
    public function __construct (core_kernel_classes_Resource $result, core_kernel_classes_Resource $testTaker, core_kernel_classes_Resource $delivery){
        $this->result = $result;
        $this->testTaker = $testTaker;
        $this->delivery = $delivery;
    }

    /**
     * Get the formated label
     * 
     * @return string the formated label  
     */
    public function __toString()
    {
        $testTakerLabel = $this->testTaker->getLabel();
        $deliveryLabel = $this->delivery->getLabel();
        return "${testTakerLabel} - ${deliveryLabel}";
    } 
}

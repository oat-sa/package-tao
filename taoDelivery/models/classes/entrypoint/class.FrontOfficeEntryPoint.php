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
 *
 */

use oat\oatbox\Configurable;
use oat\tao\model\entryPoint\Entrypoint;

class taoDelivery_models_classes_entrypoint_FrontOfficeEntryPoint extends Configurable implements Entrypoint
{

    public function getId() {
        return 'deliveryServer';
    }
    
    public function getTitle() {
        return __('Test-Takers');
    }
    
    public function getLabel() {
        return __('TAO Delivery Server');
    }
    
    public function getDescription() {
        return __('Take or continue a test.');
    }
    
    public function getUrl() {
        return _url("index", "DeliveryServer", "taoDelivery");
    }

}
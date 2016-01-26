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

namespace oat\taoBackOffice\model\entryPoint;

use oat\oatbox\PhpSerializable;
use oat\oatbox\Configurable;
use oat\tao\model\entryPoint\Entrypoint;

class BackOfficeEntryPoint extends Configurable implements Entrypoint
{

    public function getId() {
        return 'backoffice';
    }
    
    public function getTitle() {
        return __('Test Developers and Administrators');
    }
    
    public function getLabel() {
        return __('TAO Back Office');
    }
    
    public function getDescription() {
        return __('Create items, manage item and test banks, organize cohorts and deliveries, prepare reports, set up workflows.');
    }
    
    public function getUrl() {
        return _url("index", "Main", "tao");
    }

}
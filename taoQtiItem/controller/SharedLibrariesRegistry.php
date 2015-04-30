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

namespace oat\taoQtiItem\controller;

use \tao_actions_CommonModule;
use oat\taoQtiItem\model\qti\Service;

/**
 * The SharedLibrariesRegistry module is the access point to get the mapping
 * between Portable Shared Libraries names and URLs.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SharedLibrariesRegistry extends tao_actions_CommonModule
{
    /**
     * Returns the JSON encoded map of availabled Portable Shared Libraries.
     * 
     * Example: consider that the 'IMSGlobal/jquery_2_1_1' and 'OAT/lodash' shared
     * libraries are registered on the platform. This action will return the following
     * JSON structure:
     * 
     * {
     *     "IMSGlobal/jquery_2_1_1": "http://platformhost/taoQtiItem/views/js/portableSharedLibraries/IMSGlobal/jquery_2_1_1.js",
     *     "OAT/lodash": "http://platformhost/taoQtiItem/views/js/portableSharedLibraries/OAT/lodash.js"    
     * }
     */
    public function index()
    {
        $registry = Service::singleton()->getSharedLibrariesRegistry();
        $this->returnJson($registry->getMapping(), 200);
    }
}
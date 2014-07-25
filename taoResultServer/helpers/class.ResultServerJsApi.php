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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 * @package taoResultServer
 * @subpackage helpers
 */
class taoResultServer_helpers_ResultServerJsApi
{
    
    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param core_kernel_classes_Resource $resultServer
     * @return string
     */
    public static function getServiceApi(core_kernel_classes_Resource $resultServer = null) {
        return 'new ResultServerApi('.tao_helpers_Javascript::buildObject(self::getEndpoint($resultServer)).')';
    }
    
    /**
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param core_kernel_classes_Resource $resultServer
     * @return Ambigous <, string>
     */
    private static function getEndpoint(core_kernel_classes_Resource $resultServer = null) {
        return _url('', 'ResultServerStateFull','taoResultServer');
    }
    
}
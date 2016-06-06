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
namespace oat\taoRevision\model;

use core_kernel_classes_Resource;
use common_session_SessionManager;
use oat\tao\model\lock\LockManager;
use oat\taoRevision\model\workspace\ApplicableLock;
use oat\oatbox\service\ServiceManager;

class RevisionService
{
    /**
     * 
     * @param core_kernel_classes_Resource $resource
     * @param string $message
     * @param string $version
     * @deprecated
     * @return \oat\taoRevision\model\Revision
     */
    static public function commit(core_kernel_classes_Resource $resource, $message, $version = null) {
        
        \common_Logger::w('Please register events to cause autocommits');
        
        $repositoryService = ServiceManager::getServiceManager()->get(Repository::SERVICE_ID);
        return $repositoryService->commit($resource->getUri(), $message, $version);
    }
}

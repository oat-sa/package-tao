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
namespace oat\taoWorkspace\model;

use core_kernel_classes_Resource;
use oat\oatbox\service\ConfigurableService;
use oat\taoRevision\model\Repository;
use oat\taoRevision\model\Revision;
use oat\tao\model\lock\LockManager;
use oat\taoRevision\model\workspace\ApplicableLock;

class RevisionWrapper extends ConfigurableService implements Repository
{
    const OPTION_INNER_IMPLEMENTATION = 'inner';
    
    /**
     * @return Repository
     */
    protected function getInner()
    {
        return $this->getServiceLocator()->get($this->getOption(self::OPTION_INNER_IMPLEMENTATION));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::getRevisions()
     */
    public function getRevisions($resourceId)
    {
        return $this->getInner()->getRevisions($resourceId);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::getRevision()
     */
    public function getRevision($resourceId, $version)
    {
        return $this->getInner()->getRevision($resourceId, $version);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::commit()
     */
    public function commit($resourceId, $message, $version = null)
    {
        $userId = \common_session_SessionManager::getSession()->getUser()->getIdentifier();
        if (is_null($userId)) {
            throw new \common_exception_Error('Anonymous User cannot commit resources');
        }
        $lockManager = LockManager::getImplementation();
        $resource = new \core_kernel_classes_Resource($resourceId);
        if ($lockManager->isLocked($resource)) {
            if ($lockManager instanceof ApplicableLock) {
                $lockManager->apply($resource, $userId, true);
            }
        }
        return $this->getInner()->commit($resourceId, $message, $version);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::restore()
     */
    public function restore(Revision $revision)
    {
        $lockManager = LockManager::getImplementation();
        $resource = new \core_kernel_classes_Resource($revision->getResourceId());
        if ($lockManager->isLocked($resource)) {
            $userId = \common_session_SessionManager::getSession()->getUser()->getIdentifier();
            $lockManager->releaseLock($resource, $userId);
        }
        return $this->getInner()->restore($revision);
    }
}

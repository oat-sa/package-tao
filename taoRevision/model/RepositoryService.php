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

use core_kernel_classes_Property;
use oat\taoRevision\helper\CloneHelper;
use oat\generis\model\data\ModelManager;
use oat\taoRevision\helper\DeleteHelper;
use oat\oatbox\service\ConfigurableService;

/**
 * A simple repository implementation that stores the information
 * in a dedicated rds table
 * 
 * @author bout
 */
class RepositoryService extends ConfigurableService implements Repository
{
    const OPTION_STORAGE = 'storage';
    
    private $storage = null;

    /**
     * @return RevisionStorage
     */
    protected function getStorage()
    {
        if(is_null($this->storage)) {
            $this->storage = $this->getServiceLocator()->get($this->getOption(self::OPTION_STORAGE));
        }
        return $this->storage;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::getRevisions()
     */
    public function getRevisions($resourceId)
    {
        return $this->getStorage()->getAllRevisions($resourceId);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::getRevision()
     */
    public function getRevision($resourceId, $version)
    {
        return $this->getStorage()->getRevision($resourceId, $version);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::commit()
     */
    public function commit($resourceId, $message, $version = null)
    {
        $user = \common_session_SessionManager::getSession()->getUser();
        $userId = is_null($user) ? null : $user->getIdentifier();
        $version = is_null($version) ? $this->getNextVersion($resourceId) : $version;
        $created = time();
        
        // save data
        $resource = new \core_kernel_classes_Resource($resourceId);
        $data = CloneHelper::deepCloneTriples($resource->getRdfTriples());
        
        $revision = $this->getStorage()->addRevision($resourceId, $version, $created, $userId, $message, $data);

        return $revision;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoRevision\model\Repository::restore()
     */
    public function restore(Revision $revision) {
        $resourceId = $revision->getResourceId();
        $data = $this->getStorage()->getData($revision);
        
        $resource = new \core_kernel_classes_Resource($revision->getResourceId());
        DeleteHelper::deepDelete($resource);
        
        foreach (CloneHelper::deepCloneTriples($data) as $triple) {
            ModelManager::getModel()->getRdfInterface()->add($triple);
        }

        return true;
    }
    
    /**
     * Helper to determin suitable next version nr
     *
     * @param string $resourceId
     * @return number
     */
    protected function getNextVersion($resourceId) {
        $candidate = 0;
        foreach ($this->getRevisions($resourceId) as $revision) {
            $version = $revision->getVersion();
            if (is_numeric($version) && $version > $candidate) {
                $candidate = $version;
            }
        }
        return $candidate + 1;
    }
}

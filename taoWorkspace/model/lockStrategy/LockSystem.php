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

use oat\taoRevision\helper\CloneHelper;
use core_kernel_classes_Resource;
use oat\generis\model\data\ModelManager;
use common_Utils;
use oat\taoWorkspace\model\generis\WrapperModel;
use \oat\tao\model\lock\LockSystem as LockSystemInterface;
use oat\oatbox\Configurable;
use oat\taoWorkspace\model\WorkspaceMap;
use oat\taoRevision\helper\DeleteHelper;
use oat\taoRevision\model\workspace\ApplicableLock;
use oat\tao\model\lock\ResourceLockedException;

/**
 * Implements Lock using a basic property in the ontology storing the lock data
 *
 * @note It would be preferably static but we may want to have the polymorphism on lock but it would be prevented by explicit class method static calls.
 * Also if you nevertheless call it statically you may want to avoid the late static binding for the getLockProperty
 */
class LockSystem extends Configurable
    implements LockSystemInterface, ApplicableLock
{
    public function getStorage() {
        return new SqlStorage();
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\lock\LockSystem::setLock()
     */
    public function setLock(core_kernel_classes_Resource $resource, $ownerId)
    {
        $lock = $this->getLockData($resource);
        if (is_null($lock)) {
            $clone = $this->deepClone($resource);
            SqlStorage::add($ownerId, $resource, $clone);
            WorkspaceMap::getCurrentUserMap()->reload();
        } elseif ($lock->getOwnerId() != $ownerId) {
            throw new ResourceLockedException($lock);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\lock\LockSystem::isLocked()
     */
    public function isLocked(core_kernel_classes_Resource $resource)
    {
        $lock = $this->getLockData($resource);
        return !is_null($lock);
    }
    
	/**
	 * (non-PHPdoc)
	 * @see \oat\tao\model\lock\LockSystem::releaseLock()
	 */
	public function releaseLock(core_kernel_classes_Resource $resource, $ownerId)
	{
	    $lock = $this->getLockData($resource);
	    if ($lock === false) {
	        return false;
	    }
	    if ($lock->getOwnerId() !== $ownerId) {
	        throw new \common_exception_Unauthorized ( "The resource is owned by " . $lock->getOwnerId ());
	    }
	    $this->release($lock);
	    return true;
	}
	
   /**
    * (non-PHPdoc)
    * @see \oat\tao\model\lock\LockSystem::forceReleaseLock()
    */
    public function forceReleaseLock(core_kernel_classes_Resource $resource)
    {
        $lock = $this->getLockData($resource);
        if ($lock === false) {
            return false;
        }
        return $this->release($lock);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\lock\LockSystem::getLockData()
     * @return Lock
     */
    public function getLockData(core_kernel_classes_Resource $resource)
    {
        return $this->getStorage()->getLock($resource);
    }
    
    public function apply(core_kernel_classes_Resource $resource, $ownerId, $release = true)
    {
        $lock = $this->getLockData($resource);
	    if ($lock === false) {
	        return false;
	    }
	    if ($lock->getOwnerId() !== $ownerId) {
	        throw new \common_exception_Unauthorized ( "The resource is owned by " . $lock->getOwnerId ());
	    }

	    \common_Logger::i('Applying changes to '.$resource->getUri());
	     
	    $innerModel = $this->getInnerModel();
	    $triples = $innerModel->getRdfsInterface()->getResourceImplementation()->getRdfTriples($resource);
	    // bypasses the wrapper
	    DeleteHelper::deepDeleteTriples($triples);
	    
	    $triples = $this->getWorkspaceModel()->getRdfsInterface()->getResourceImplementation()->getRdfTriples($lock->getWorkCopy());
	    $clones = CloneHelper::deepCloneTriples($triples);
	    
	    foreach ($clones as $triple) {
	        $triple->modelid = $innerModel->getNewTripleModelId();
	        $triple->subject = $resource->getUri();
	        $innerModel->getRdfInterface()->add($triple);
	    }

	    if ($release) {
	        $this->release($lock);
	    }
    }
    
    protected function release(Lock $lock) {

        $workCopy = $lock->getWorkCopy();

        // deletes the dependencies
        DeleteHelper::deepDelete($workCopy, $this->getWorkspaceModel());

        // deletes the workCopy
        $this->getWorkspaceModel()->getRdfsInterface()->getResourceImplementation()->delete($workCopy);

        SqlStorage::remove($lock);
        WorkspaceMap::getCurrentUserMap()->reload();

        return true;
    }
    
    protected function deepClone(core_kernel_classes_Resource $source) {
        $clonedTriples = CloneHelper::deepCloneTriples($source->getRdfTriples());
        $newUri = common_Utils::getNewUri();
        
        $wsModel = $this->getWorkspaceModel();
        $rdfInterface = $wsModel->getRdfInterface();
        foreach ($clonedTriples as $triple) {
            $triple->subject = $newUri;
            $triple->modelid = $wsModel->getNewTripleModelId();
            $rdfInterface->add($triple);
        }
        return new core_kernel_classes_Resource($newUri);
    }

    /**
     * 
     * @throws \common_exception_InconsistentData
     * @return oat\generis\model\data\Model
     */
    private function getInnerModel() {
        $model = ModelManager::getModel();
        if (!$model instanceof WrapperModel) {
            throw new \common_exception_InconsistentData('Unexpected ontology model');
        }
        return $model->getInnerModel();
    }
    
    /**
     * 
     * @throws \common_exception_InconsistentData
     * @return \core_kernel_persistence_smoothsql_SmoothModel
     */
    private function getWorkspaceModel() {
        $model = ModelManager::getModel();
        if (!$model instanceof WrapperModel) {
            throw new \common_exception_InconsistentData('Unexpected ontology model '.get_class($model));
        }
        $wsModel = $model->getWorkspaceModel();
        if (!$wsModel instanceof \core_kernel_persistence_smoothsql_SmoothModel) {
            throw new \common_exception_InconsistentData('Unexpected workspace model'.get_class($wsModel));
        } 
        return $wsModel;
    }
}

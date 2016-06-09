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
 * 
 */

namespace oat\taoRevision\controller;

use oat\tao\helpers\UserHelper;
use oat\taoRevision\model\Repository;

/**
 * Revision history management controller
 *
 * @author Open Assessment Technologies SA
 * @package taoRevision
 * @license GPL-2.0
 *
 */
class History extends \tao_actions_CommonModule {

    /**
     * @return Repository
     */
    protected function getRevisionService() {
        return $this->getServiceManager()->get(Repository::SERVICE_ID);
    }

    /**
     * @requiresRight id WRITE
     */
    public function index() {
        $resource = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        $revisions = $this->getRevisionService()->getRevisions($resource->getUri());

        $returnRevision = array();
        foreach($revisions as $revision){

            $returnRevision[] = array(
                'id'        => $revision->getVersion(),
                'modified'  => \tao_helpers_Date::displayeDate($revision->getDateCreated()),
                'author'    => UserHelper::renderHtmlUser($revision->getAuthorId()),
                'message'   => _dh($revision->getMessage()),
            );
        }
        
        $this->setData('resourceLabel', _dh($resource->getLabel()));
        $this->setData('id', $resource->getUri());
        $this->setData('revisions', $returnRevision);
        $this->setView('History/index.tpl');
    }

    /**
     * @requiresRight id WRITE
     */
    public function restoreRevision(){
        $resource = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        $oldVersion = $this->getRequestParameter('revisionId');
        $message = $this->getRequestParameter('message');
        
        $oldRevision = $this->getRevisionService()->getRevision($resource->getUri(), $oldVersion);
        
        $success = $this->getRevisionService()->restore($oldRevision);
        if ($success) {
            $newRevision = $this->getRevisionService()->commit($resource->getUri(), $message);
            $this->returnJson(array(
                'success'   => true,
                'id'        => $newRevision->getVersion(),
                'modified'  => \tao_helpers_Date::displayeDate($newRevision->getDateCreated()),
                'author'    => UserHelper::renderHtmlUser($newRevision->getAuthorId()),
                'message'   => $newRevision->getMessage()
            ));
        } else {
            $this->returnError(__('Unable to restore the selected version'));
        }
    }

    /**
     * @requiresRight id WRITE
     */
    public function commitResource(){

        $resource = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        // prevent escaping on input
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        
        $revision = $this->getRevisionService()->commit($resource->getUri(), $message);
        
        $this->returnJson(array(
            'success'       => true,
            'id'            => $revision->getVersion(),
            'modified'      => \tao_helpers_Date::displayeDate($revision->getDateCreated()),
            'author'        => UserHelper::renderHtmlUser($revision->getAuthorId()),
            'message'       => $revision->getMessage(),
            'commitMessage' => __('%s has been committed', $resource->getLabel())
        ));
    }
}
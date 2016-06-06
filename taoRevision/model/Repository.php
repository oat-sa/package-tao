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

interface Repository
{
    const SERVICE_ID = 'taoRevision/repository';
    
    /**
     * 
     * @param string $resourceId
     * @return array return an array of Revision objects
     */
    public function getRevisions($resourceId);
    
    /**
     * 
     * @param string $resourceId
     * @param string $version
     * @throws RevisionNotFound
     * @return Revision
     */
    public function getRevision($resourceId, $version);
    
    /**
     * 
     * @param string $resourceId
     * @param string $message
     * @param string $revisionId
     * @return Revision
     */
    public function commit($resourceId, $message, $version = null);
    
    /**
     * Restore a previous version
     * 
     * @param Revision $revision
     * @return boolean success
     */
    public function restore(Revision $revision);
}

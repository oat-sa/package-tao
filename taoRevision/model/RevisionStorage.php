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

interface RevisionStorage
{
    /**
     * 
     * @param string $resourceId
     * @param string $version
     * @param string $created
     * @param string $author
     * @param string $message
     * @param \core_kernel_classes_Triple[] $data
     * @return Revision
     */
    public function addRevision($resourceId, $version, $created, $author, $message, $data);
    
    /**
     *
     * @param string $resourceId
     * @param string $version
     * @return Revision
     */
    public function getRevision($resourceId, $version);
    
    /**
     * 
     * @param string $resourceId
     * @return Revision[]
     */
    public function getAllRevisions($resourceId);
    
    /**
     * 
     * @param Revision $revision
     * \core_kernel_classes_Triple[] $data
     */
    public function getData(Revision $revision);
}

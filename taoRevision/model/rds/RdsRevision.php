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

namespace oat\taoRevision\model\rds;

use oat\taoRevision\model\Revision;

/**
 * Adds the  Revision Identifier
 * 
 * @author bout
 */
class RdsRevision extends Revision
{
    /**
     * RDS specific identifier of revisions
     * @var int
     */
    private $id;
    
    /**
     * Create a RdsRevision, called by Storage only
     * 
     * @param int $id
     * @param string $resourceId
     * @param string $version
     * @param int $created
     * @param string $author
     * @param string $message
     */
    public function __construct($id, $resourceId, $version, $created, $author, $message) {
        $this->id = $id;
        parent::__construct($resourceId, $version, $created, $author, $message);
    }
    
    /**
     * Returns the RDS id of the revision
     */
    public function getId() {
        return $this->id;
    }
}

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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoRevision\scripts\install;

use oat\taoRevision\model\storage\RdsStorage as Storage;
use oat\taoRevision\model\RepositoryService;
use oat\taoRevision\model\Repository;

class SetupRevisions extends CreateTables {

    public function __invoke($params) {
        
        $persistenceId = count($params) > 0 ? reset($params) : 'default';
        
        // createTable
        parent::__invoke(array($persistenceId));
        
        $this->registerService('taoRevision/storage', new Storage(array('persistence' => $persistenceId)));
        $this->registerService(Repository::SERVICE_ID, new RepositoryService(array(RepositoryService::OPTION_STORAGE => 'taoRevision/storage')));
    }
}

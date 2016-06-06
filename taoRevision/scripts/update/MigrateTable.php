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
namespace oat\taoRevision\scripts\update;

use oat\taoRevision\model\rds\Storage;
use oat\taoRevision\model\Repository;
use common_report_Report as Report;

class MigrateTable extends \common_ext_action_InstallAction {

    public function __invoke($params) {
        
        $impl = $this->getServiceManager()->get(Repository::SERVICE_ID);
        if (!$impl instanceof \oat\taoRevision\model\rds\Repository) {
            return new Report(Report::TYPE_ERROR,'Current implementation '.get_class($impl).' cannot be migrated');
        }
        $persistenceId = count($params) > 0 ? reset($params) : 'default';
        $persistence = $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_KEY)->getPersistenceById($persistenceId);
        
        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        
        $fromSchema = clone $schema;
        // add new fields
        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        // migrate data
        $query = "UPDATE ".Storage::DATA_TABLE_NAME;
        $persistence->exec($query);
        
        $intermediateSchema = clone $schema;
        // remove old fields
        $queries = $persistence->getPlatform()->getMigrateSchemaSql($intermediateSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }
        
    }
}

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

class CreateTables extends \common_ext_action_InstallAction {

    public function __invoke($params) {
        
        $persistenceId = count($params) > 0 ? reset($params) : 'default';
        $persistence = $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_KEY)->getPersistenceById($persistenceId);
        
        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;
        
        try {
        
            $revisionTable = $schema->createtable(Storage::REVISION_TABLE_NAME);
            $revisionTable->addOption('engine', 'MyISAM');
        
            $revisionTable->addColumn(Storage::REVISION_RESOURCE, "string", array("notnull" => false, "length" => 255));
            $revisionTable->addColumn(Storage::REVISION_VERSION, "string", array("notnull" => false, "length" => 50));
            $revisionTable->addColumn(Storage::REVISION_USER, "string", array("notnull" => true, "length" => 255));
            $revisionTable->addColumn(Storage::REVISION_CREATED, "string", array("notnull" => true));
            $revisionTable->addColumn(Storage::REVISION_MESSAGE, "string", array("notnull" => true, "length" => 4000));
            $revisionTable->setPrimaryKey(array(Storage::REVISION_RESOURCE, Storage::REVISION_VERSION));
        
            $dataTable = $schema->createtable(Storage::DATA_TABLE_NAME);
            $dataTable->addOption('engine', 'MyISAM');
            $dataTable->addColumn(Storage::DATA_RESOURCE, "string", array("notnull" => false, "length" => 255));
            $dataTable->addColumn(Storage::DATA_VERSION, "string", array("notnull" => false, "length" => 50));
            $dataTable->addColumn(Storage::DATA_SUBJECT, "string", array("notnull" => true, "length" => 255));
            $dataTable->addColumn(Storage::DATA_PREDICATE, "string", array("length" => 255));
            // not compatible with oracle
            $dataTable->addColumn(Storage::DATA_OBJECT, "text", array("default" => null,"notnull" => false));
            $dataTable->addColumn(Storage::DATA_LANGUAGE, "string", array("length" => 50));
        
            $dataTable->addForeignKeyConstraint(
                $revisionTable,
                array(Storage::REVISION_RESOURCE, Storage::REVISION_VERSION),
                array(Storage::REVISION_RESOURCE, Storage::REVISION_VERSION)
            );
        
        } catch(SchemaException $e) {
            \common_Logger::i('Database Schema already up to date.');
        }
        
        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }        
    }
}

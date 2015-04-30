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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

use oat\taoDacSimple\model\DataBaseAccess;
use oat\generis\model\data\permission\PermissionManager;
use oat\taoDacSimple\model\AdminService;
use oat\generis\model\data\permission\implementation\FreeAccess;

$persistence = common_persistence_Manager::getPersistence('default');
$schema = $persistence->getDriver()->getSchemaManager()->createSchema();
$fromSchema = clone $schema;
$table = $schema->dropTable(DataBaseAccess::TABLE_PRIVILEGES_NAME);
$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
foreach ($queries as $query){
    $persistence->exec($query);
}

$impl = new FreeAccess();
PermissionManager::setPermissionModel($impl);

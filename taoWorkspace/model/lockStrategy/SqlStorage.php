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

namespace oat\taoWorkspace\model\lockStrategy;

use common_persistence_Manager;
use core_kernel_classes_Resource;

class SqlStorage
{
    const TABLE_NAME = 'workspace';
    const FIELD_OWNER = 'owner';
    const FIELD_RESOURCE = 'resource';
    const FIELD_WORKCOPY = 'workcopy';
    const FIELD_CREATED = 'created';
    
    static public function createTable() {
        $persistence = common_persistence_Manager::getPersistence('default');
        
        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;
        
        $tableResults = $schema->createtable(self::TABLE_NAME);
        $tableResults->addColumn(self::FIELD_OWNER,     "string", array("notnull" => true, "length" => 255));
        $tableResults->addColumn(self::FIELD_RESOURCE, "string", array("notnull" => true, "length" => 255));
        $tableResults->addColumn(self::FIELD_WORKCOPY, "string", array("notnull" => true, "length" => 255));
        $tableResults->addColumn(self::FIELD_CREATED,  "string", array("notnull" => true));
        $tableResults->setPrimaryKey(array(self::FIELD_OWNER, self::FIELD_RESOURCE));
        
        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }
    }
    
    public function getPersistence() {
        return common_persistence_Manager::getPersistence('default');
    }
    
    static public function getMap($userId) {
        $persistence = common_persistence_Manager::getPersistence('default');
        $query =  'SELECT * FROM '.self::TABLE_NAME.' WHERE '.self::FIELD_OWNER.' = ?';
        $result	= $persistence->query($query, array($userId));
        
        $map = array();
        while ($row = $result->fetch()) {
            $map[$row[self::FIELD_RESOURCE]] = $row[self::FIELD_WORKCOPY];
        }
        return $map;
    }
    
    static public function add($userId, core_kernel_classes_Resource $resource, core_kernel_classes_Resource $copy) {
        $persistence = common_persistence_Manager::getPersistence('default');
		$query = 'INSERT INTO "'.self::TABLE_NAME.'" ("'.self::FIELD_OWNER.'", "'.self::FIELD_RESOURCE.'", "'.self::FIELD_WORKCOPY.'", "'.self::FIELD_CREATED.'") VALUES (?,?,?,?)';
		$result = $persistence->exec($query,array($userId, $resource->getUri(), $copy->getUri(), time()));
    }
    
    static public function remove(Lock $lock) {
        $persistence = common_persistence_Manager::getPersistence('default');
        $query = 'DELETE FROM "'.self::TABLE_NAME.'" WHERE "'.self::FIELD_OWNER.'" = ? AND "'.self::FIELD_RESOURCE.'" = ?';
        $result = $persistence->exec($query,array($lock->getOwnerId(), $lock->getResource()->getUri()));
    }

    /**
     * @param Lock
     */
    public function getLock(core_kernel_classes_Resource $resource) {
        
        $query =  'SELECT * FROM '.self::TABLE_NAME.' WHERE '.self::FIELD_RESOURCE.' = ?';
        $result	= $this->getPersistence()->query($query, array($resource->getUri()));
        
        $locks = array();
        while ($row = $result->fetch()) {
            $locks[] = new Lock(
                new core_kernel_classes_Resource($row[self::FIELD_RESOURCE])
                , $row[self::FIELD_OWNER]
                , $row[self::FIELD_CREATED]
                , new core_kernel_classes_Resource($row[self::FIELD_WORKCOPY])
            );
        }
        if (count($locks) > 1) {
            throw new \common_exception_InconsistentData(count($locks).' locks found for resource '.$resource->getUri());
        }
        return count($locks) == 1 ? reset($locks) : null;
    }
    
    
}

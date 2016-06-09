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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 
 *
 */
abstract class common_persistence_sql_pdo_SchemaManager extends common_persistence_sql_SchemaManager
{

    /**
     *
     * @var unknown
     */
    private $driver;

    /**
     *
     * @var unknown
     */
    private $schemaManager = null;

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param common_persistence_sql_pdo_Driver $driver            
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return common_persistence_sql_pdo_Driver
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_SchemaManager::getSchemaManager()
     */
    public function getSchemaManager()
    {
        if ($this->schemaManager == null) {
            common_Logger::d('init dbal connection to get SchemaMangager');
            $config = new \Doctrine\DBAL\Configuration();
            $params = $this->getDriver()->getParams();
            $connection = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
            $this->schemaManager = $connection->getSchemaManager();
        }
        return $this->schemaManager;
    }
    

}
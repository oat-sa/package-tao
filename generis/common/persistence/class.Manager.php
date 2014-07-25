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
 */



 /**
 * A factory for our perstences
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 
 *
 */
class common_persistence_Manager
{
    /**
     * @var array
     */
    private static $persistences = array();

    /**
     * @var array
     */
    private static $driverMap = array(
        'dbal_pdo_mysql'  => 'common_persistence_sql_dbal_mysql_Driver',
        'dbal_pdo_sqlite' => 'common_persistence_sql_dbal_Driver',
        'dbal_pdo_pgsql'  => 'common_persistence_sql_dbal_Driver',
        'pdo_oci'    => 'common_persistence_sql_dbal_oracle_Driver',
        'dbal_pdo_ibm'    => 'common_persistence_sql_dbal_Driver',
        'pdo_sqlsrv' => 'common_persistence_sql_dbal_sqlsrv_Driver',
        'pdo_mysql'  => 'common_persistence_sql_pdo_mysql_Driver',
        'pdo_pgsql'  => 'common_persistence_sql_pdo_pgsql_Driver',
        'phpredis'   => 'common_persistence_PhpRedisDriver',
        'phpfile'    => 'common_persistence_PhpFileDriver',
        'SqlKvWrapper' => 'common_persistence_SqlKvDriver'
    );

    /**
     * 
     * @param string $persistenceId
     * @return common_persistence_Persistence
     */
    public static function getPersistence($persistenceId){
        if(!isset(self::$persistences[$persistenceId])) {
            self::$persistences[$persistenceId] = self::createPersistence($persistenceId);
        }
        return self::$persistences[$persistenceId];
    }

    /**
     * @param string $persistenceId
     * @throws common_Exception
     * @return common_persistence_Persistence
     */
    private static function createPersistence($persistenceId) {
        if(!isset($GLOBALS['generis_persistences'])) {
            throw new common_Exception('Persistence Configuration not found');
        }
        if(!isset($GLOBALS['generis_persistences'][$persistenceId])) {
            throw new common_Exception('Persistence Configuration for persistence '.$persistenceId.' not found');
        }
        $config = $GLOBALS['generis_persistences'][$persistenceId];
        $driverStr = $config['driver'];
        
        if (isset(self::$driverMap[$driverStr])) {
            $driverClassName = self::$driverMap[$driverStr];
        } else {
                $driverClassName = $driverStr;
        }
               
        if (class_exists($driverClassName)){
            $driver = new $driverClassName();
        }
        else{
            common_Logger::e('Driver '.$driverStr.' not found check your database configuration');
        }
        return $driver->connect($persistenceId, $config);
    }
    
}

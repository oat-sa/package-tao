<?php
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceNotFoundException;
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
class common_persistence_Manager extends ConfigurableService
{
    const SERVICE_KEY = 'generis/persistences';
    
    const OPTION_PERSISTENCES = 'persistences';

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
        'SqlKvWrapper' => 'common_persistence_SqlKvDriver',
        'no_storage' => 'common_persistence_NoStorageKvDriver'
    );
    
    /**
     * @return common_persistence_Manager
     */
    protected static function getDefaultManager()
    {
        try {
            $manager = ServiceManager::getServiceManager()->get(self::SERVICE_KEY);
        } catch (ServiceNotFoundException $ex) {
            $manager = new self(array(
                self::OPTION_PERSISTENCES => array()
            ));
            $manager->setServiceManager(ServiceManager::getServiceManager());
        }
        return $manager;
    }
    
    /**
     *
     * @param string $persistenceId
     * @return common_persistence_Persistence
     */
    public static function getPersistence($persistenceId)
    {
        return self::getDefaultManager()->getPersistenceById($persistenceId);
    }

    /**
     * Add a new persistence to the system
     *
     * @param string $persistenceId
     * @param array $persistenceConf
     */
    public static function addPersistence($persistenceId, array $persistenceConf)
    {
        $manager = self::getDefaultManager();
        $configs = $manager->getOption(self::OPTION_PERSISTENCES);
        $configs[$persistenceId] = $persistenceConf;
        $manager->setOption(self::OPTION_PERSISTENCES, $configs);
        
        $manager->getServiceManager()->register(self::SERVICE_KEY, $manager);
    }
    
    /**
     * @var array
     */
    private $persistences = array();
    
    /**
     * @return common_persistence_Persistence
     */
    public function getPersistenceById($persistenceId)
    {
        if(!isset($this->persistences[$persistenceId])) {
            $this->persistences[$persistenceId] = $this->createPersistence($persistenceId);
        }
        return $this->persistences[$persistenceId];
    }

    /**
     * @param string $persistenceId
     * @throws common_Exception
     * @return common_persistence_Persistence
     */
    private function createPersistence($persistenceId) {
        $generis = common_ext_ExtensionsManager::singleton()->getExtensionById('generis');
        $configs = $this->getOption(self::OPTION_PERSISTENCES);
        if (isset($configs[$persistenceId])) {
            $config = $configs[$persistenceId];
            $driverStr = $config['driver'];
            
            $driverClassName = isset(self::$driverMap[$driverStr])
                ? self::$driverMap[$driverStr]
                : $driverStr;
             
            if (!class_exists($driverClassName)){
                throw new common_exception_Error('Driver '.$driverStr.' not found check your database configuration');
            }
            $driver = new $driverClassName();
            return $driver->connect($persistenceId, $config);
        } else {
            throw new common_Exception('Persistence Configuration for persistence '.$persistenceId.' not found');
        }
    }
    
}

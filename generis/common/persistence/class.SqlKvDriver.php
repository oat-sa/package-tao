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
 * A key value driver based upon an existing sql persistence
 * 
 */
class common_persistence_SqlKvDriver implements common_persistence_KvDriver
{

    const DEFAULT_GC_PROBABILITY = 1000;
    
    /**
     * @var common_persistence_SqlPersistence
     */
    private $sqlPeristence;
    
    /**
     * Probability of garbage collection to be triggered
     * stores the inverse element
     * 
     * @var int
     */
    private $garbageCollection;
    
   
    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    function connect($id, array $params)
    {
        if (!isset($params['sqlPersistence'])) {
            throw new common_exception_Error('Missing underlying sql persistence');
        }
        
        $this->sqlPeristence = common_persistence_SqlPersistence::getPersistence($params['sqlPersistence']);
        $this->garbageCollection = isset($params['gc']) ? $params['gc'] : self::DEFAULT_GC_PROBABILITY;

        
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @param string $value
     * @param int $ttl
     * @throws common_Exception
     * @return boolean
     */
    public function set($id, $value, $ttl = null) {
       
        $returnValue = false;
        try{
            
            $expire = is_null($ttl) ? 0 : time() + $ttl;
            
            $encoded = base64_encode($value);
            $platformName = $this->sqlPeristence->getPlatForm()->getName();
            $params = array(':data' => $encoded, ':time' => $expire, ':id' => $id);
            
            
            if($platformName == 'mysql'){
                //query found in Symfony PdoSessionHandler
                $statement = "INSERT INTO kv_store (kv_id, kv_value, kv_time) VALUES (:id, :data, :time) 
                    ON DUPLICATE KEY UPDATE kv_value = VALUES(kv_value), kv_time = VALUES(kv_time)";
                $returnValue = $this->sqlPeristence->exec($statement,$params);
                
            } else if($platformName == 'oracle'){
                $statement = "MERGE INTO kv_store USING DUAL ON(kv_id = :id) 
                    WHEN NOT MATCHED THEN INSERT (kv_id, kv_value, kv_time) VALUES (:id, :data, sysdate) 
                    WHEN MATHED THEN UPDATE SET kv_value = :data WHERE kv_id = :id";
            }
            
            else {
                $statement = 'UPDATE kv_store SET kv_value = :data , kv_time = :time WHERE kv_id = :id';
                $returnValue = $this->sqlPeristence->exec($statement,$params);
                if ($returnValue == 0){
                    $returnValue = $this->sqlPeristence->insert('kv_store', array('kv_id' => $id, 'kv_time' => $expire, 'kv_value' => $encoded));
                }
            }
            
          
            if ($this->garbageCollection != 0 && rand(0, $this->garbageCollection) == 1) {
                $this->gc();
            } 
        }
        catch (Exception $e){
            throw new common_Exception("Unable to write the key value storage table in the database "  .$e->getMessage());
        }
        return (boolean)$returnValue;
    }
    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @throws common_Exception
     * @return string|boolean
     */
    public function get($id) {
        try{
           
            $statement = 'SELECT kv_value, kv_time FROM kv_store WHERE kv_id = ?';
            $statement = $this->sqlPeristence->getPlatForm()->limitStatement($statement,1);
            $sessionValue = $this->sqlPeristence->query($statement,array($id));
            while ($row = $sessionValue->fetch()) {
                if ($row["kv_time"] == 0 || $row["kv_time"] >= time() ) {
                    return base64_decode($row["kv_value"]);
                }
            }
        }
        catch (Exception $e){
            throw new common_Exception("Unable to read value from key value storage");
        }
        return false;
    }
    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @throws common_Exception
     * @return boolean
     */
    public function exists($id) {
        try{
           
            $statement = 'SELECT kv_value FROM kv_store WHERE kv_id = ?';
            $statement = $this->sqlPeristence->getPlatForm()->limitStatement($statement,1);
            $sessionValue = $this->sqlPeristence->query($statement,array($id));
            return ($sessionValue->fetch() !== false);
        }
        catch (Exception $e){
            throw new common_Exception("Unable to read value from key value storage");
        }
    }
    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @throws common_Exception
     * @return boolean
     */
    public function del($id) {
        try{
            $statement = 'DELETE FROM kv_store WHERE kv_id = ?';
            $sessionValue = $this->sqlPeristence->exec($statement,array($id));
            return (boolean)$sessionValue;
        }
        catch (Exception $e){
            throw new common_Exception("Unable to write the key value table in the database " .$e->getMessage());
        }
        return false;
    }
    
    /**
     * Should be moved to another interface (session handler) than the persistence, 
     * this class implementing only the persistence side and another class implementing 
     * the handler interface and relying on the persitence.
     */
    protected function gc()
    {
        $statement = 'DELETE FROM kv_store WHERE kv_time > 0 AND kv_time <  ? ';
        return (bool)$this->sqlPeristence->exec($statement, array(time()));
    }

   
}

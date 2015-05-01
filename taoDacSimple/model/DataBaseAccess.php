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
 *
 */

namespace oat\taoDacSimple\model;

use common_session_SessionManager;
use common_Logger;
use tao_models_classes_UserService;

/**
 * Class to handle the storage and retrieval of permissions
 * 
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 * @author Joel Bout <joel@taotesting.com>
 */
class DataBaseAccess
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---


    private $persistence = null;

    const TABLE_PRIVILEGES_NAME = 'data_privileges';

    // --- OPERATIONS ---

    
    public function __construct()
    {
        $this->setPersistence(\common_persistence_Manager::getPersistence('default'));
        
    }


    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param \common_persistence_Persistence $persistence
     */
    public function setPersistence(\common_persistence_Persistence $persistence){
        $this->persistence = $persistence;
    }
    
    /**
     * We can know which users have a privilege on a resource
     * @param array $resourceIds
     * @param array $userType ('role' or 'user' or both)
     * @return array list of users
     */
    public function getUsersWithPermissions($resourceIds)
    {
        $inQuery = implode(',', array_fill(0, count($resourceIds), '?'));
        $query = "SELECT resource_id, user_id, privilege FROM " . self::TABLE_PRIVILEGES_NAME . "
        WHERE resource_id IN ($inQuery)";
        /** @var \PDOStatement $statement */
        $statement = $this->persistence->query($query, $resourceIds);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $results;
    }

    /**
     * Get the permissions for a list of resources and users
     *
     * @access public
     * @param array $userIds
     * @param  array $resourceIds
     * @return array
     */
    public function getPermissions($userIds, array $resourceIds){
        // get privileges for a user/roles and a resource
        $returnValue = array();

        $inQueryResource = implode(',', array_fill(0, count($resourceIds), '?'));
        $inQueryUser = implode(',', array_fill(0, count($userIds), '?'));
        $query = "SELECT resource_id, privilege FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id IN ($inQueryResource) AND user_id IN ($inQueryUser)";

        $params = $resourceIds;
        foreach ($userIds as $userId) {
            $params[] = $userId;
        }
        /** @var \PDOStatement $statement */
        $statement = $this->persistence->query($query, $params);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

         foreach ($results as $result) {
            $returnValue[$result['resource_id']][] = $result['privilege'];
         }

         return $returnValue;
     }

    /**
     * add permissions of a user to a resource
     *
     * @access public
     * @param  string $user
     * @param  string $resourceId
     * @param  array $rights
     * @return boolean
     */
    public function addPermissions($user, $resourceId, $rights)
    {

        foreach ($rights as $privilege) {
            // add a line with user URI, resource Id and privilege
            $this->persistence->insert(
                self::TABLE_PRIVILEGES_NAME,
                array('user_id' => $user, 'resource_id' => $resourceId, 'privilege' => $privilege)
            );
        }
        return true;
    }


    /**
     * Get the permissions for a list of resources
     *
     * @access public
     * @param  string $resourceId
     * @return array
     */
    public function getResourcePermissions($resourceId)
    {
        // get privileges for a user/roles and a resource
        $returnValue = array();

        $query = "SELECT user_id, privilege FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id = ?";

        /** @var \PDOStatement $statement */
        $statement = $this->persistence->query($query, array($resourceId));
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $returnValue[$result['user_id']][] = $result['privilege'];
        }

        return $returnValue;
    }

    /**
     * get the delta between existing permissions and new permissions
     *
     * @access public
     * @param  string $resourceId
     * @param  array $rights associative array $user_id => $permissions
     * @return array
     */
    public function getDeltaPermissions($resourceId, $rights)
    {
        $privileges = $this->getResourcePermissions($resourceId);

        foreach($rights as $userId => $privilegeIds){
            //if privileges are in request but not in db we add then
            if(!isset($privileges[$userId])){
                $add[$userId] = $privilegeIds;
            }
            // compare privileges in db and request
            else{
                $add[$userId] = array_diff($privilegeIds,$privileges[$userId]);
                $remove[$userId] = array_diff($privileges[$userId],$privilegeIds);
                // unset already compare db variable
                unset($privileges[$userId]);
            }
        }

        //remaining privileges has to be removed
        foreach($privileges as $userId => $privilegeIds){
            $remove[$userId] = $privilegeIds;
        }


        return compact("remove", "add");
    }

    /**
     * remove permissions to a resource for a user
     *
     * @access public
     * @param  string $user
     * @param  string $resourceId
     * @param  array $rights
     * @return boolean
     */
    public function removePermissions($user, $resourceId, $rights)
    {
        //get all entries that match (user,resourceId) and remove them
        $inQueryPrivilege = implode(',', array_fill(0, count($rights), '?'));
        $query = "DELETE FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id = ? AND privilege IN ($inQueryPrivilege) AND user_id = ?";
        $params = array($resourceId);
        foreach ($rights as $rightId) {
            $params[] = $rightId;
        }
        $params[] = $user;
        
        $this->persistence->exec($query, $params);

        return true;
    }

    /**
     * Remove all permissions from a resource
     *
     * @access public
     * @param  array $resourceIds
     * @return boolean
     */
    public function removeAllPermissions($resourceIds)
    {
        //get all entries that match (resourceId) and remove them
        $inQuery = implode(',', array_fill(0, count($resourceIds), '?'));
        $query = "DELETE FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id IN ($inQuery)";
        $this->persistence->exec($query, $resourceIds);

        return true;
    }

}

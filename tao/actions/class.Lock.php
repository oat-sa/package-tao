<?php
/*  
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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 * 
 */
?>
<?php
/**
 * control the lock on a given resource
 * 
 * @author plichart
 * @package taoGroups
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_actions_Lock extends tao_actions_CommonModule {

	protected $lockService = null;
	public function __construct()
	{
		parent::__construct();
		//the service is initialized by default
		$this->service = tao_models_classes_lock_OntoLock::singleton();
		$this->defaultData();
	}
	
	public function release($uri)
	{   try {
        $this->service->releaseLock(
            new core_kernel_classes_Resource(tao_helpers_Uri::decode($uri)),
            tao_models_classes_UserService::singleton()->getCurrentUser());
        } catch (Exception $e) {
            //the connected user is not the owner of the lock
            //there is no lock on the resource
            //the lock is corrupted

            switch (get_class($e)) {
                case "common_exception_Unauthorized":{break;}
            }
        }
    }
	
}
?>
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 				 2013-2014 (update and modification) Open Assessment Technologies SA;
 * 
 */

use oat\generis\model\data\ModelManager;

/**
 * Custom extension installer for generis
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class common_ext_GenerisInstaller
    extends common_ext_ExtensionInstaller
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method install
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function install()
    {
    	if ($this->extension->getId() != 'generis') {
    		throw new common_ext_ExtensionException('Tried to install "'.$this->extension->getId().'" extension using the GenerisInstaller');
    	}
        //$this->installCustomScript();
		$this->installLoadDefaultConfig();
		
		ModelManager::setModel(new \core_kernel_persistence_smoothsql_SmoothModel(array(
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'default',
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => array('1'),
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => array('1'),
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => '1'
		)));
		
		$this->installOntology();
		//$this->installLocalData();
		//$this->installModuleModel();
		$this->installRegisterExt();
		
		common_cache_FileCache::singleton()->purge();
        
		common_Logger::d('Installing custom script for extension ' . $this->extension->getId());
		$this->installCustomScript();
		
        
    }

}
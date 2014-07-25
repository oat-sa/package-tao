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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * default action
 * must be in the actions folder
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class tao_actions_ExtensionsManager extends tao_actions_CommonModule {

    /* (non-PHPdoc)
     * @see tao_actions_CommonModule::_isAllowed()
    */
    protected function _isAllowed() {
        return parent::_isAllowed() && tao_helpers_SysAdmin::isSysAdmin();
    }
    
	/**
	 * Index page
	 */
	public function index() {

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$extensionManager->reset();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$availlableExtArray = $extensionManager->getAvailableExtensions();
		usort($availlableExtArray, function($a, $b) { return strcasecmp($a->getID(),$b->getID());});
		$this->setData('installedExtArray',$installedExtArray);
		$this->setData('availableExtArray',$availlableExtArray);
		$this->setView('extensionManager/view.tpl');

	}

    /**
     *
     * return current extension
     *
     * @return common_ext_Extension|null
     */
    protected function getCurrentExtension() {
		if ($this->hasRequestParameter('id')) {
			$extensionManager = common_ext_ExtensionsManager::singleton();
			return common_ext_ExtensionsManager::singleton()->getExtensionById($this->getRequestParameter('id'));
		} else {
			return null;
		}
	}

    /**
     * add an extension
     *
     * @param $id
     * @param $package_zip
     *
     */
    public function add( $id , $package_zip ){

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$fileUnzip = new fileUnzip(urldecode($package_zip));
		$fileUnzip->unzipAll(EXTENSION_PATH);
		$newExt = $extensionManager->getExtensionById($id);
		$extInstaller = new tao_install_ExtensionInstaller($newExt);
		try {
			$extInstaller->install();
			$message =   __('Extension ') . $newExt->getName() . __(' has been installed');
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}

		$this->setData('message',$message);
		$this->index();

	}

    /**
     *
     * install action
     *
     */
    public function install(){
		$success = false;
		try {
			$extInstaller = new tao_install_ExtensionInstaller($this->getCurrentExtension());
			$extInstaller->install();
			$message =   __('Extension ') . $this->getCurrentExtension()->getID() . __(' has been installed');
			$success = true;
			
			// reinit user session
			$session = core_kernel_classes_Session::singleton()->refresh();
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}

		echo json_encode(array('success' => $success, 'message' => $message));
	}

    /**
     *
     * modify an already installed action
     *
     * @param $loaded
     * @param $loadAtStartUp
     */
    public function modify($loaded,$loadAtStartUp){

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$configurationArray = array();
		foreach($installedExtArray as $k=>$ext){
			$configuration = new common_ext_ExtensionConfiguration(isset($loaded[$k]),isset($loadAtStartUp[$k]));
			$configurationArray[$k]=$configuration;
		}
		try {
			$extensionManager->modifyConfigurations($configurationArray);
			$message = __('Extensions\' configurations updated ');
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}
		$this->setData('message', $message);
		$this->index();

	}

}
?>
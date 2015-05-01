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

use oat\tao\model\menu\MenuService;
use oat\tao\helpers\translation\TranslationBundle;

/**
 * default action
 * must be in the actions folder
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_ExtensionsManager extends tao_actions_CommonModule {

	/**
	 * Index page
	 */
	public function index() {

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$availlableExtArray = $extensionManager->getAvailableExtensions();
		usort($availlableExtArray, function($a, $b) { return strcasecmp($a->getId(),$b->getId());});
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
     *
     * install action
     *
     */
    public function install(){
		$success = false;
		try {
			$extInstaller = new tao_install_ExtensionInstaller($this->getCurrentExtension());
			$extInstaller->install();
			$message =   __('Extension ') . $this->getCurrentExtension()->getId() . __(' has been installed');
			$success = true;
			
			// reinit user session
			$session = \common_session_SessionManager::getSession()->refresh();
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}

		echo json_encode(array('success' => $success, 'message' => $message));
	}

    /**
     * Once some extensions have been installed, we trigger this action.
     */
    public function postInstall()
    {
        $success = true;
        $message = '';
        
        // try to regenerate languages bundles
        try {
            tao_models_classes_LanguageService::singleton()->generateClientBundles(true);
        } catch (common_excpetion_Error $e) {
            $message = $e->getMessage();
            $success = false;
        }
        
        $this->returnJson(array(
            'success' => $success,
            'message' => $message
        ));
    }

	/**
	 * Disables an extension
	 */
	public function disable() {
	    $extId = $this->getRequestParameter('id');
	    \common_ext_ExtensionsManager::singleton()->setEnabled($extId, false);
	    MenuService::flushCache();
	    echo json_encode(array(
	        'success' => true,
	        'message' => __('Disabled %s', $this->getRequestParameter('id'))
	    ));
	}
	
	/**
	 * Enables an extension
	 */
	public function enable() {
	    $extId = $this->getRequestParameter('id');
	    \common_ext_ExtensionsManager::singleton()->setEnabled($extId, true);
	    MenuService::flushCache();
	    echo json_encode(array(
	        'success' => true,
	        'message' => __('Disabled %s', $this->getRequestParameter('id'))
	    ));
	}
	
	/**
	 * Uninstalls an extension
	 */
	public function uninstall() {
	    try {
	        $uninstaller = new \tao_install_ExtensionUninstaller($this->getCurrentExtension());
	        $success = $uninstaller->uninstall();
	        $message = __('Uninstalled %s', $this->getRequestParameter('id'));
	    } catch (\common_Exception $e) {
	        $success = false;
	        if ($e instanceof \common_exception_UserReadableException) {
	            $message = $e->getUserMessage();
	        } else {
	            $message = __('Uninstall of %s failed', $this->getRequestParameter('id'));
	        }
	    }
	    echo json_encode(array(
	        'success' => $success,
	        'message' => $message
	    ));
	}

}

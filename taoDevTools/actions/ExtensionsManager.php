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
 */

namespace oat\taoDevTools\actions;

use oat\taoDevTools\forms\Extension;
use oat\taoDevTools\models\ExtensionCreator;

/**
 * Extensions management controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class ExtensionsManager extends \tao_actions_ExtensionsManager {

	/**
	 * Index page
	 */
	public function index() {

		$extensionManager = \common_ext_ExtensionsManager::singleton();
		$all = array();
		$installed = array();
		foreach ($extensionManager->getInstalledExtensions() as $ext) {
		    $all[] = $ext;
		    $installed[] = $ext->getId();
		}
		foreach ($extensionManager->getAvailableExtensions() as $ext) {
		    $all[] = $ext;
		}
		$all = \helpers_ExtensionHelper::sortById($all);
		$this->setData('extensions',$all);
		$this->setData('installedIds',$installed);
		$this->setView('extensionManager/view.tpl');

	}
	
	/**
	 * Form to create a new extension
	 */
	public function create() {
	    $formContainer = new Extension();
	    $myForm = $formContainer->getForm();
	    
	    if ($myForm->isValid() && $myForm->isSubmited()) {
	        $creator = new ExtensionCreator(
	            $myForm->getValue('name'),
	            $myForm->getValue('label'),
	            $myForm->getValue('version'),
	            $myForm->getValue('author'),
	            $myForm->getValue('authorNs'),
	            $myForm->getValue('license'),
	            $myForm->getValue('description'),
	            $myForm->getValue('dependencies'),
	            $myForm->getValue('samples')
            );
	        $report = $creator->run();
	        $this->setData('myForm', __('Extension created'));
	    } else {
	        $this->setData('myForm', $myForm->render());
	    }

	    $this->setData('formTitle', __('Create a new Extension'));
	    $this->setView('form.tpl', 'tao');
	}

}

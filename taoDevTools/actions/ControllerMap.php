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

use oat\tao\model\controllerMap\Factory;

/**
 * Extensions management controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class ControllerMap extends \tao_actions_CommonModule {
    
    public function index() {

        $factory = new Factory();
        
        $data = array();
        foreach (\common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
            $data[$ext->getId()] = $factory->getControllers($ext->getId());
        }
        
        $this->setData('extensions', $data);
        $this->setView('controllerMap/index.tpl');
    }
}
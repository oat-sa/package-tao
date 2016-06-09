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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoCe\actions;

use oat\tao\model\menu\MenuService;
use tao_models_classes_accessControl_AclProxy;
use oat\tao\helpers\TaoCe;
use oat\tao\model\menu\Perspective;

/**
 * The Home controller provides actions for the Home screen of the Community Edition
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package taoCe
 
 * @license GPL-2.0
 *
 */
class Home extends \tao_actions_CommonModule {

    /**
     * initialize the services
     */
    public function __construct(){
        
        parent::__construct();
        $this->service = \tao_models_classes_TaoService::singleton();
    }

    /**
     * This action renders the template used by the splash screen popup
     */
    public function splash() {
        
        //the list of extensions the splash provides an explanation for.
        $defaultExtIds = array('items', 'tests', 'TestTaker', 'groups', 'delivery', 'results');
        
        //check if the user is a noob
        $this->setData('firstTime', TaoCe::isFirstTimeInTao());
        
        //load the extension data
        $defaultExtensions = array();
        $additionalExtensions = array();
        foreach (MenuService::getPerspectivesByGroup(Perspective::GROUP_DEFAULT) as $i => $perspective) {
            if (in_array((string) $perspective->getId(), $defaultExtIds)) {
                $defaultExtensions[strval($perspective->getId())] = array(
                    'id' => $perspective->getId(),
                    'name' => $perspective->getName(),
                    'extension' => $perspective->getExtension(),
                    'description' => $perspective->getDescription()
                );
            } else {
                $additionalExtensions[$i] = array(
                    'id' => $perspective->getId(),
                    'name' => $perspective->getName(),
                    'extension' => $perspective->getExtension()
                );
            }

            //Test if access
            $access = false;
            foreach ($perspective->getChildren() as $section) {
                if (tao_models_classes_accessControl_AclProxy::hasAccess($section->getAction(), $section->getController(), $section->getExtensionId())) {
                    $access = true;
                    break;
                }
            }
            if (in_array((string) $perspective->getId(), $defaultExtIds)) {
                $defaultExtensions[strval($perspective->getId())]['enabled'] = $access;
            } else {
                $additionalExtensions[$i]['enabled'] = $access;
            }
        }

        $this->setData('extensions', array_merge($defaultExtensions, $additionalExtensions));
        $this->setData('defaultExtensions', $defaultExtensions);
        $this->setData('additionalExtensions', $additionalExtensions);
        
        $this->setView('splash.tpl');
    }
}

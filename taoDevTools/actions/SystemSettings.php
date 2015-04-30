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

/**
 * Extensions management controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class SystemSettings extends \tao_actions_CommonModule {
    
    public function index() {
        // needed for ac
    }
    
    public function viewSetting() {
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($this->getRequestParameter('classUri'));
        $this->setData('setting', $ext->getConfig($this->getRequestParameter('uri')));
        $this->setView('SystemSettings/viewSetting.tpl');
    }
    
    public function data() {
        if ($this->hasRequestParameter('classUri')) {
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($this->getRequestParameter('classUri'));
            $data = $this->getKeys($ext);
        } else {
    		$data = array(
    			'data' 	=> __("Settings"),
    			'attributes' => array(
    					'id' => 1,
    					'class' => 'node-class'
    				),
    			'children' => array()
    			);
    		foreach (\common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
    		    if (file_exists(CONFIG_PATH.$ext->getId())) {
        			$data['children'][] =  array(
            		    'data' 	=> $ext->getManifest()->getLabel(),
        			    'type' => 'class',
        			    '_data' => array(
        			        'id' => $ext->getId()
        			    ),
            		    'attributes' => array(
            		        'id' => $ext->getId(),
            		        'class' => 'node-class',
            		        'data-uri' => $ext->getId()
            		    ),
        			    'state'	=> 'closed'
        		    );
    		    }
    		}
        }
		$this->returnJson($data);
    }
    
    public function getKeys(\common_ext_Extension $ext) {
        $data = array();
        
        $path = CONFIG_PATH.$ext->getId();
        if (file_exists($path)) {
            foreach (new \DirectoryIterator($path) as $fileinfo) {
                if (!$fileinfo->isDot() && substr($fileinfo->getFilename(), -strlen('.conf.php')) == '.conf.php') {
                    $key = substr($fileinfo->getFilename(), 0, -strlen('.conf.php'));
                    $data[] =  array(
                        'data' 	=> $key,
                        'attributes' => array(
                            'id' => $key,
                            'class' => 'node-instance'
                        )
                    );
                }
            }
        }
        return $data;
    }
}
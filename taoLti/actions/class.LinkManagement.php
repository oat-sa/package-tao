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
 * 
 */

/**
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package filemanager
 
 */
abstract class taoLti_actions_LinkManagement extends tao_actions_CommonModule {
	
	public function __construct(taoLti_models_classes_LtiTool $service) {
		$this->service = $service;
	}
	
	/**
	 * render the main layout
	 */
	public function index() {
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		$links = array();
		$class = new core_kernel_classes_Class(CLASS_LTI_CONSUMER);
		
		foreach ($class->getInstances() as $consumer) {
			$links[] = array(
				'key' => $consumer->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_OAUTH_KEY)),
				'secret' => $consumer->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_OAUTH_SECRET)),
				'url' => $this->service->getLaunchUrl(array('delivery' => $uri))
			);
		}
		$this->setData('links', $links);
		
		$this->setData('launchUrl', $this->service->getLaunchUrl(array('delivery' => $uri)));
		$this->setData('consumers', $class->getInstances());
		$this->setData('delivery', $uri);
		$this->setView('linkManagement.tpl', 'taoLti');
	}
	
}
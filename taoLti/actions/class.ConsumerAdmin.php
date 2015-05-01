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
 * This controller allows the additon and deletion
 * of LTI Oauth Consumers
 * 
 * @author Joel Bout
 * @package taoLti
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

class taoLti_actions_ConsumerAdmin extends tao_actions_TaoModule {
	
	/**
	 * constructor uses default TaoService
	 */
	public function __construct(){
		parent::__construct();
		$this->service = tao_models_classes_TaoService::singleton();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see tao_actions_TaoModule::getRootClass()
	 */
	public function getRootClass() {
		return new core_kernel_classes_Class(CLASS_LTI_CONSUMER);
	}
	
}
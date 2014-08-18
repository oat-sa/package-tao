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

namespace oat\taoLti\actions;

use \tao_actions_CommonModule;
use \tao_helpers_Request;
use \common_exception_IsAjaxAction;

/**
 * An abstract lti controller
 * 
 * @package taoLti
 */
abstract class LtiModule extends tao_actions_CommonModule
{

	/**
	 * (non-PHPdoc)
	 * @see tao_actions_CommonModule::returnError()
	 */
	protected function returnError($description, $returnLink = false) {
        if (tao_helpers_Request::isAjax()) {
            throw new common_exception_IsAjaxAction(__CLASS__.'::'.__FUNCTION__); 
        } else {
            if (!empty($description)) {
                $this->setData('message', $description);
            }
            if ($returnLink !== false) {
                $this->setData('returnLink', $returnLink);
            }
            $this->setView('error.tpl', 'taoLti');
        }
	}
	
}
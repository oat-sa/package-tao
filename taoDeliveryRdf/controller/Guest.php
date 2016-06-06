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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */
namespace oat\taoDeliveryRdf\controller;

use common_session_SessionManager;
use oat\taoDelivery\controller\DeliveryServer;
use oat\taoDeliveryRdf\model\guest\GuestTestTakerSession;
/**
 * DeliveryServer Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Guest extends DeliveryServer
{
    /**
	 * Init guest session and redirect to module index
	 */
	public function guest()
	{
		common_session_SessionManager::endSession();
		$session = new GuestTestTakerSession();
		common_session_SessionManager::startSession($session);

		$this->redirect($this->getReturnUrl());
	}
}

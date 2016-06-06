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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\tao\model\session;

use common_session_DefaultSession;
use common_session_Session;
use common_session_SessionManager;
use oat\oatbox\user\User;

/**
 * PretenderSession allows a user pretend to be another user.
 *
 * @access private
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package oat\tao\model
 */
class PretenderSession extends common_session_DefaultSession
{
    /**
     * Real user session
     * 
     * @var common_session_Session
     */
    private $internalSession;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->internalSession = common_session_SessionManager::getSession();
    }

    /**
     * Revert back to the original Session
     */
    public function restoreOriginal()
    {
        common_session_SessionManager::startSession($this->internalSession);
    }
}
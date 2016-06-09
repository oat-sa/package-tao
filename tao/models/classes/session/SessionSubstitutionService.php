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

use oat\oatbox\user\User;
use oat\oatbox\service\ConfigurableService;
use common_session_Session;
use common_session_SessionManager;

/**
 * Class SessionSubstitutionService
 *
 * Used to temporary replace the current user session (to pretend to be another user).
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package oat\tao\model
 */
class SessionSubstitutionService extends ConfigurableService implements \oat\tao\model\SessionSubstitutionService
{
    /**
     * @param User $user
     * @return PretenderSession new session instance
     */
    public function substituteSession(User $user)
    {
        $session = new PretenderSession($user);
        common_session_SessionManager::startSession($session);
        return $session;
    }

    /**
     * @return boolean
     */
    public function isSubstituted()
    {
        $result = false;
        $session = common_session_SessionManager::getSession();
        if ($session instanceof PretenderSession) {
            $result = true;
        }
        return $result;
    }

    /**
     * @return void
     * @return common_session_Session original session instance
     * @throws \Exception
     */
    public function revert()
    {
        $session = common_session_SessionManager::getSession();
        if ($session instanceof PretenderSession) {
            $session->restoreOriginal();
        } else {
            throw new \Exception('Session has not been substituted.');
        }
        return common_session_SessionManager::getSession();
    }
}
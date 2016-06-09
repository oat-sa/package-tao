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

namespace oat\tao\model;

use oat\tao\model\session\PretenderSession;
use oat\oatbox\user\User;
use common_session_Session;

/**
 * Interface SessionSubstitutionService
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package oat\tao\model
 */
interface SessionSubstitutionService
{
    const SERVICE_ID = 'tao/SessionSubstitution';

    /**
     * @param User $user
     * @return PretenderSession new session instance
     */
    public function substituteSession(User $user);

    /**
     * @return boolean
     */
    public function isSubstituted();

    /**
     * @return void
     * @return common_session_Session original session instance
     */
    public function revert();
}
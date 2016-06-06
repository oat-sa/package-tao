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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoQtiTest\models\runner\session;

use qtism\runtime\tests\Route;
use qtism\data\AssessmentTest;
use taoQtiTest_helpers_SessionManager;

/**
 * SessionManager that instantiate the runner's TestSession.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 *
 */
class SessionManager extends taoQtiTest_helpers_SessionManager
{

    /**
     * Instantiates an AssessmentTestSession with the default overriden TestSession.
     *
     * @param AssessmentTest $test
     * @param Route $route
     * @return TestSession
     */
    protected function instantiateAssessmentTestSession(AssessmentTest $test, Route $route)
    {
        return new TestSession($test, $this, $route, $this->getResultServer(), $this->getTest());
    }
}

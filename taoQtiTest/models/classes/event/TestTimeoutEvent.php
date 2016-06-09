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

namespace oat\taoQtiTest\models\event;
use qtism\runtime\tests\AssessmentTestSession;

/**
 * Event represents timeout during test session
 * Triggered before all types of timeouts (item, section, test part, test)
 *
 * @see \oat\taoQtiTest\models\runner\QtiRunnerService::onTimeout()
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class TestTimeoutEvent extends AbstractTestEvent
{
    /**
     * @var integer
     * @see \qtism\runtime\tests\AssessmentTestSessionException
     */
    protected $timeoutCode;

    /**
     * TestTimeoutEvent constructor.
     * @param AssessmentTestSession $session
     * @see \qtism\runtime\tests\AssessmentTestSessionException
     * @param $timeoutCode
     */
    public function __construct(AssessmentTestSession $session, $timeoutCode)
    {
        parent::__construct($session);
        $this->timeoutCode = $timeoutCode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
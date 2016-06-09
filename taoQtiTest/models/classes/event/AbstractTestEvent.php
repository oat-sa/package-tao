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
use oat\oatbox\event\Event;

/**
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
abstract class AbstractTestEvent implements Event
{
    protected $session;

    /**
     * QtiMoveEvent constructor.
     * @param AssessmentTestSession $session
     */
    public function __construct(AssessmentTestSession $session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }


    /**
     * @return AssessmentTestSession
     */
    public function getSession()
    {
        return $this->session;
    }

}
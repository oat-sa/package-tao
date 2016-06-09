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
use qtism\runtime\tests\RouteItem;
use oat\oatbox\event\Event;

/**
 *
 */
class QtiMoveEvent implements Event
{
    const CONTEXT_BEFORE = 'before';
    const CONTEXT_AFTER = 'after';

    private $from;
    private $to;
    private $session;
    private $context;

    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * QtiMoveEvent constructor.
     * @param string $context 'before' or 'after' move
     * @param AssessmentTestSession $session
     * @param null|RouteItem $from
     * @param null|RouteItem $to
     */
    public function __construct($context, AssessmentTestSession $session, RouteItem $from = null, RouteItem $to = null)
    {
        $this->context = $context;
        $this->session = $session;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return AssessmentTestSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return null|RouteItem
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return null|RouteItem
     */
    public function getTo()
    {
        return $this->to;
    }

}
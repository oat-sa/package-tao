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

use oat\taoTests\models\event\TestChangedEvent;
/**
 *
 */
class QtiTestChangeEvent extends TestChangedEvent
{
    private $session;
    
    public function __construct(\taoQtiTest_helpers_TestSession $testSession)
    {
        $this->session = $testSession;
    }
    
    public function getServiceCallId()
    {
        return $this->session->getSessionId();
    }
    
    public function getNewStateDescription()
    {
        $pos = $this->session->getRoute()->getPosition();
        $count = $this->session->getRouteCount();
        if ($this->session->isRunning()) {
            $section = $this->session->getCurrentAssessmentSection();
            return __('%1$s - item %2$s/%3$s', $section->getTitle(), $pos+1, $count);
        } else {
            return __('finished');
        }
    } 
}
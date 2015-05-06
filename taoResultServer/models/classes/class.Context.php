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
 * Copyright (c) 2013 (original work) Open Assessment Technologies S.A.
 *
 */

/**
 * An Assessment Result is used to report the results of a candidate's interaction
 * with a test and/or one or more items attempted.
 * Information about the test is optional,
 * in some systems it may be possible to interact with items that are not organized into
 * a test at all. For example, items that are organized with learning resources and presented
 * individually in a formative context.
 *
 * @author
 *
 *
 */
class taoResultServer_models_classes_Context
{

    /**
     * The system that creates the result (for example, the test delivery system) should assign a session identifier that it can use to identify the session.
     * Subsequent systems that process the result might assign their own identifier to the session which should be added to the context if the result is modified and exported for transport again.
     * 
     * @var sessionIdentifier
     */
    private $sessionIdentifiers;

    /**
     * A unique identifier for the test candidate.
     * The attribute is defined by the IMS Learning Information Services specification [IMS_LIS].
     * 
     * @var string (an uri)
     */
    private $sourcedID;

    public function addSessionIdentifier(taoResultServer_models_classes_SessionIdentifier $sessionIdentifier)
    {
        $this->sessionIdentifiers[] = $sessionIdentifier;
    }

    public function getSessionIdentifiers()
    {
        return $this->sessionIdentifiers;
    }

    public function setSourcedID($sourcedID)
    {
        $this->sourcedID = $sourcedID;
    }

    public function getSourcedID()
    {
        return $this->sourcedID;
    }
}

?>
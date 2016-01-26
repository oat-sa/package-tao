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
 *
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 * 
 * An Assessment Result is used to report the results of a candidate's interaction
 * with a test and/or one or more items attempted.
 * Information about the test is optional,
 * in some systems it may be possible to interact with items that are not organized into a test at all. For example, items that are organized with learning resources and presented individually in a formative context.
 */
class taoResultServer_models_classes_TraceVariable extends taoResultServer_models_classes_Variable
{

    /**
     * When a response variable is bound to an interaction that supports the shuffling of choices, the sequence of choices experienced by the candidate will vary between test instances.
     * When shuffling is in effect, the sequence of choices should be reported as a sequence of choice identifiers using this attribute.
     * 
     * @var string
     */
    public $trace;

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @param string $trace
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;
    }

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @return string
     */
    public function getTrace()
    {
        return $this->trace;
    }
    
    /**
     * Return value.
     * @author  Aleh Hutnikau, <hutnikau@1pt.com>
     * @return string
     */
    public function getValue()
    {
        return $this->getTrace();
    }

    /**
     * Set the value of the trace variable
     * @param string $value
     */
    public function setValue($value)
    {
        $this->setTrace($value);
    }
}

?>
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
 * with a test and/or one or more items attempted. Information about the test is optional,
 * in some systems it may be possible to interact with items that are not organized into 
 * a test at all. For example, items that are organized with learning resources and 
 * presented individually in a formative context.
 * 
 */
class taoResultServer_models_classes_OutcomeVariable extends taoResultServer_models_classes_Variable
{

    /**
     * taken from the corresponding outcomeDeclaration.
     * 
     * @var float
     */
    public $normalMaximum;

    /**
     *
     * @var float
     */
    public $normalMinimum;


    /**
     * The value(s) of the outcome variable.
     * The order of the values is significant only if the outcome was declared with ordered cardinality.
     * 
     * @var array
     */
    public $value;

    /**
     * @param float $normalMaximum
     */
    public function setNormalMaximum($normalMaximum)
    {
        $this->normalMaximum = $normalMaximum;
    }

    /**
     *
     * @access public 
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return float
     */
    public function getNormalMaximum()
    {
        return $this->normalMaximum;
    }

    /**
     *
     * @access public 
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param float $normalMinimum
     */
    public function setNormalMinimum($normalMinimum)
    {
        $this->normalMinimum = $normalMinimum;
    }

    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return float
     */
    public function getNormalMinimum()
    {
        return $this->normalMinimum;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {   
        return base64_decode($this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {   
        //null byte, binary 
        $this->value = base64_encode($value);
    }
}

?>
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
     * The views (if any) declared for the outcome must be copied to the report to enable systems that render the report to hide information not relevant in a specific situation.
     * If no values are given, the outcome's value should be considered relevant in all views.
     * 
     * @var array {author, candidate, proctor, scorer, testConstructor, tutor}
     */
    // public $views;
    /**
     *
     * @var string
     */
    // public $interpretation;
    /**
     *
     * @var uri
     */
    // public $longInterpretation;
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
     * If a mastery value is specified in the corresponding outcomeDeclaration it may be reported alongside the value of the outcomeVariable.
     * In some cases, the mastery value may not be an attribute of the item itself, but be determined by the context in which the item is delivered, for example, by examining the candidates in a specific cohort. The mastery value may be reported with the outcome value even when there is no corresponding value in the declaration.
     * 
     * @var float
     */
    // public $masteryValue;
    /**
     * The value(s) of the outcome variable.
     * The order of the values is significant only if the outcome was declared with ordered cardinality.
     * 
     * @var array
     */
    public $value;
    /*
     * public function getViews(){ return $this->views; } public function addView($view){ if (!(in_array($cardinality, array("author","candidate","proctor", "scorer", "testConstructor", "tutor")))){ throw new common_exception_InvalidArgumentType("view"); } $this->views[] = $view; } public function setInterpretation($interpretation){ $this->interpretation = $interpretation; } public function getInterpretation(){ return $this->interpretation; } public function setLongInterpretation($longInterpretation){ $this->longInterpretation = $longInterpretation; } public function getLongInterpretation(){ return $this->longInterpretation; }
     */
    public function setNormalMaximum($normalMaximum)
    {
        $this->normalMaximum = $normalMaximum;
    }

    /**
     *
     * @access public 
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return number
     */
    public function getNormalMaximum()
    {
        return $this->normalMaximum;
    }

    /**
     *
     * @access public 
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param unknown $normalMinimum            
     */
    public function setNormalMinimum($normalMinimum)
    {
        $this->normalMinimum = $normalMinimum;
    }

    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return number
     */
    public function getNormalMinimum()
    {
        return $this->normalMinimum;
    }
    /*
     * public function setMasteryValue($masteryValue){ $this->masteryValue = $masteryValue; } public function getMasteryValue(){ return $this->masteryValue; }
     */
    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return multitype:
     */
    public function getValue()
    {   
        return base64_decode($this->value);
    }

    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param unknown $value            
     */
    public function setValue($value)
    {   
        //null byte, binary 
        $this->value = base64_encode($value);
    }
}

?>
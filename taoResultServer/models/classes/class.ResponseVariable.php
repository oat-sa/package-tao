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
class taoResultServer_models_classes_ResponseVariable extends taoResultServer_models_classes_Variable
{

    /**
     * The correct response may be output as part of the report if desired.
     * Systems are not limited to reporting correct responses declared in responseDeclarations. For example, a correct response may be set by a templateRule or may simply have been suppressed from the declaration passed to the delivery engine (e.g., for security).
     * 
     * @var bool (todo should be a class)
     */
    public $correctResponse;

    /**
     *
     * @var string
     */
    public $candidateResponse;

    
    /**
     * substitued for the db storage into a GENERIS_TRUE/FALSE
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param string correctResponse
     */
    public function setCorrectResponse($correctResponse)
    {
        $this->correctResponse = $correctResponse;
    }

    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return boolean
     */
    public function getCorrectResponse()
    {
        return $this->correctResponse;
    }

    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param unknown $candidateResponse            
     */
    public function setCandidateResponse($candidateResponse)
    {
        //binary and nullbyte safe
        $this->candidateResponse = base64_encode($candidateResponse);
    }
    /**
     *
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return mixed
     */
    public function getCandidateResponse()
    {   
        //base64 binary and nullbyte safe
        return base64_decode($this->candidateResponse);      
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getCandidateResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value){
        $this->setCandidateResponse($value);
    }
}

?>
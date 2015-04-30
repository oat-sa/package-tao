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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * A mock configuration component for which you can specify the type of report.
 * for testing purpose.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_Mock
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The expected report status.
     *
     * @access private
     * @var int
     */
    private $expectedStatus = 0;

    // --- OPERATIONS ---

    /**
     * Create a new Mock configuration component with an expected report status,
     * a name.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int expectedStatus The expected status of the report that will be provided by the check method. Must correspond to a constant of the Report class.
     * @param  string name The name of the mock configuration component to make it identifiable among others.
     * @return mixed
     */
    public function __construct($expectedStatus, $name)
    {
        
        $this->setExpectedStatus($expectedStatus);
        $this->setName($name);
        
    }

    /**
     * Fake configuration check that will provide a Report with the expected
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function check()
    {
        
        $message = 'Mock configuration report.';
        $report = new common_configuration_Report($this->getExpectedStatus(), $message, $this);
        return $report;
        
    }

    /**
     * Provide the expected status and contains a value defined by the status
     * of the Report class.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getExpectedStatus()
    {
        $returnValue = (int) 0;

        
        $returnValue = $this->expectedStatus;
        

        return (int) $returnValue;
    }

    /**
     * Set the expected status of the Mock configuration component.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int expectedStatus A status corresponding to a constant value of the Report class.
     * @return void
     */
    public function setExpectedStatus($expectedStatus)
    {
        
        $this->expectedStatus = $expectedStatus;
        
    }

} 
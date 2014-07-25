<?php
/*  
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
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 25.07.2012, 16:44:14 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_configuration_PHPExtension
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/configuration/class.PHPExtension.php');

/* user defined includes */
// section -64--88-56-1--4c174f22:138be969f09:-8000:0000000000001B32-includes begin
// section -64--88-56-1--4c174f22:138be969f09:-8000:0000000000001B32-includes end

/* user defined constants */
// section -64--88-56-1--4c174f22:138be969f09:-8000:0000000000001B32-constants begin
// section -64--88-56-1--4c174f22:138be969f09:-8000:0000000000001B32-constants end

/**
 * Short description of class common_configuration_PHPDatabaseDriver
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_PHPDatabaseDriver
    extends common_configuration_PHPExtension
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method check
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Report
     */
    public function check()
    {
        $returnValue = null;

        // section -64--88-56-1--4c174f22:138be969f09:-8000:0000000000001B34 begin
        $report = parent::check();
        $name = $this->getName();
        
        if ($report->getStatus() == common_configuration_Report::VALID){
            $report->setMessage("Database Driver '${name}' is available.");
        }
        else{
            $report->setMessage("Database Driver '${name}' could not be found or is unavailable.");
        }

        $returnValue = $report;
        // section -64--88-56-1--4c174f22:138be969f09:-8000:0000000000001B34 end

        return $returnValue;
    }

} /* end of class common_configuration_PHPDatabaseDriver */

?>
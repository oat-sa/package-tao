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
 * Generis Object Oriented API - common/exception/class.Error.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.04.2012, 18:04:53 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/class.Exception.php');

/**
 * include common_log_SeverityLevel
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/interface.SeverityLevel.php');

/* user defined includes */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-includes begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-includes end

/* user defined constants */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-constants begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-constants end

/**
 * Short description of class common_exception_Error
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_Error
    extends common_Exception
        implements common_log_SeverityLevel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getSeverity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getSeverity()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001907 begin
        $returnValue = common_Logger::ERROR_LEVEL;
        // section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001907 end

        return (int) $returnValue;
    }

} /* end of class common_exception_Error */

?>
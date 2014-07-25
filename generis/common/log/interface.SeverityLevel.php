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
 * Generis Object Oriented API - common/log/interface.SeverityLevel.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.01.2012, 16:44:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-includes begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-includes end

/* user defined constants */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-constants begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-constants end

/**
 * Short description of class common_log_SeverityLevel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
interface common_log_SeverityLevel
{


    // --- OPERATIONS ---

    /**
     * Short description of method getSeverity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getSeverity();

} /* end of interface common_log_SeverityLevel */

?>
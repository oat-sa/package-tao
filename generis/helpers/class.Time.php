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
 * Generis Object Oriented API - helpers/class.Time.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.02.2012, 11:17:50 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-includes begin
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-includes end

/* user defined constants */
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-constants begin
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-constants end

/**
 * Short description of class helpers_Time
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package helpers
 */
class helpers_Time
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getMicroTime
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return helpers_double
     */
    public static function getMicroTime()
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C8 begin
        
        list($ms, $s) = explode(" ", microtime());
        $returnValue = $s+$ms;
        
        // section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C8 end

        return (float) $returnValue;
    }

} /* end of class helpers_Time */

?>
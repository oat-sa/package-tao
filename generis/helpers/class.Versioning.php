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
 * Generis Object Oriented API - helpers/class.Versioning.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.01.2013, 15:34:00 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-includes begin
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-includes end

/* user defined constants */
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-constants begin
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-constants end

/**
 * Short description of class helpers_Versioning
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 * @deprecated
 */
class helpers_Versioning
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * deprecated, since always enabled
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @return boolean
     */
    public static function isEnabled()
    {
        return true;
    }

    /**
     * please use helpers_FileSource::getFileSources()
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @return array
     */
    public static function getAvailableRepositories()
    {
        $returnValue = array();

        // section 10-30-1--78--774a33b7:13ad0ae6f5f:-8000:0000000000001BB9 begin
        $returnValue = helpers_FileSource::getFileSources();
        // section 10-30-1--78--774a33b7:13ad0ae6f5f:-8000:0000000000001BB9 end

        return (array) $returnValue;
    }

} /* end of class helpers_Versioning */

?>
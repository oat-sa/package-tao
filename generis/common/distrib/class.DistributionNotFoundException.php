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
 * Such an Exception must be thrown if a distribution that was supposed to exist
 * not.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-includes begin
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-includes end

/* user defined constants */
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-constants begin
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-constants end

/**
 * Such an Exception must be thrown if a distribution that was supposed to exist
 * not.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */
class common_distrib_DistributionNotFoundException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_distrib_DistributionNotFoundException */

?>
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
 * TAO - tao/models/classes/class.UserException.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 13.03.2012, 11:53:25 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
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

/* user defined includes */
// section 127-0-1-1-235ad9e5:12db7a01f69:-8000:0000000000002D13-includes begin
// section 127-0-1-1-235ad9e5:12db7a01f69:-8000:0000000000002D13-includes end

/* user defined constants */
// section 127-0-1-1-235ad9e5:12db7a01f69:-8000:0000000000002D13-constants begin
// section 127-0-1-1-235ad9e5:12db7a01f69:-8000:0000000000002D13-constants end

/**
 * Short description of class tao_models_classes_UserException
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */
class tao_models_classes_UserException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @return mixed
     */
    public function __construct($message)
    {
        // section 127-0-1-1-235ad9e5:12db7a01f69:-8000:0000000000002D14 begin
        
    	parent::__construct($message);
    	
        // section 127-0-1-1-235ad9e5:12db7a01f69:-8000:0000000000002D14 end
    }

} /* end of class tao_models_classes_UserException */

?>
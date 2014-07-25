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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoQTI/models/classes/QTI/response/class.TakeoverFailedException.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 16:45:40 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Don't generate this class
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-includes begin
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-includes end

/* user defined constants */
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-constants begin
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
class taoQTI_models_classes_QTI_response_TakeoverFailedException
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
    public function __construct($message = '')
    {
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361F begin
        parent::__construct(empty($message) ? 'Impossible to takeover ResponseProcessing for QTI item' : $message);
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361F end
    }

} /* end of class taoQTI_models_classes_QTI_response_TakeoverFailedException */

?>
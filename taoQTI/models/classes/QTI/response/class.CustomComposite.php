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
 * TAO - taoQTI/models/classes/QTI/response/class.CustomComposite.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.01.2012, 19:06:03 with ArgoUML PHP module 
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
 * include taoQTI_models_classes_QTI_response_Composite
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/class.Composite.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
class taoQTI_models_classes_QTI_response_CustomComposite
    extends taoQTI_models_classes_QTI_response_Composite
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute compositionRules
     *
     * @access public
     * @var array
     */
    public $compositionRules = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getCompositionRules
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getCompositionRules()
    {
        $returnValue = array();

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003608 begin
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003608 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCompositionQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getCompositionQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003634 begin
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003634 end

        return (string) $returnValue;
    }

} /* end of class taoQTI_models_classes_QTI_response_CustomComposite */

?>
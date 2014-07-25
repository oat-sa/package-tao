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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Scale/class.Enumeration.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.02.2012, 11:48:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A scale for the measurements of an item
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/Scale/class.Scale.php');

/* user defined includes */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-includes begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-includes end

/* user defined constants */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-constants begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-constants end

/**
 * Short description of class taoItems_models_classes_Scale_Enumeration
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */
class taoItems_models_classes_Scale_Enumeration
    extends taoItems_models_classes_Scale_Scale
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute values
     *
     * @access public
     * @var array
     */
    public $values = array();

    /**
     * Short description of attribute CLASS_URI
     *
     * @access public
     * @var string
     */
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#EnumerationScale';

    // --- OPERATIONS ---

} /* end of class taoItems_models_classes_Scale_Enumeration */

?>
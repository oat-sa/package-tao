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
 * TAO - tao/helpers/form/elements/class.Label.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 11.05.2012, 17:19:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a FormElement entity
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormElement.php');

/* user defined includes */
// section 127-0-1-1-32598abc:126ff43e5cc:-8000:0000000000001EC2-includes begin
// section 127-0-1-1-32598abc:126ff43e5cc:-8000:0000000000001EC2-includes end

/* user defined constants */
// section 127-0-1-1-32598abc:126ff43e5cc:-8000:0000000000001EC2-constants begin
// section 127-0-1-1-32598abc:126ff43e5cc:-8000:0000000000001EC2-constants end

/**
 * Short description of class tao_helpers_form_elements_Label
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_Label
    extends tao_helpers_form_FormElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute widget
     *
     * @access public
     * @var string
     */
    public $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Label';

    // --- OPERATIONS ---

} /* end of abstract class tao_helpers_form_elements_Label */

?>
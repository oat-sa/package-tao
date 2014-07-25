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
 * Generis Object Oriented API - core\kernel\classes\class.Literal.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 15:15:24 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_classes_Container
 *
 * @author patrick.plichart@tudor.lu
 */
require_once('core/kernel/classes/class.Container.php');

/**
 * should inherit from standard collection provided in php
 *
 * @author patrick.plichart@tudor.lu
 */
require_once('core/kernel/classes/class.ContainerCollection.php');

/* user defined includes */
// section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D6F-includes begin
// section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D6F-includes end

/* user defined constants */
// section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D6F-constants begin
// section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D6F-constants end

/**
 * Short description of class core_kernel_classes_Literal
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_Literal
    extends core_kernel_classes_Container
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute literal
     *
     * @access public
     * @var string
     */
    public $literal = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string literal
     * @param  string debug
     * @return void
     */
    public function __construct($literal, $debug = '')
    {
        // section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D7C begin
        $this->literal = strval($literal);
        if(DEBUG_MODE){
    		$this->debug = $debug;
        }
        // section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D7C end
    }

    /**
     * Short description of method __toString
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000008921 begin
        $returnValue = $this->literal;
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000008921 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_Literal */

?>
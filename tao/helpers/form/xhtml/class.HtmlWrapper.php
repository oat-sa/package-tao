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
 * This decorator render the decorated element inside xhtml.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A decorator is an helper used for aspect oriented rendering.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/interface.Decorator.php');

/* user defined includes */
// section 127-0-1-1-3c8d01cf:1256d79098b:-8000:0000000000001CEE-includes begin
// section 127-0-1-1-3c8d01cf:1256d79098b:-8000:0000000000001CEE-includes end

/* user defined constants */
// section 127-0-1-1-3c8d01cf:1256d79098b:-8000:0000000000001CEE-constants begin
// section 127-0-1-1-3c8d01cf:1256d79098b:-8000:0000000000001CEE-constants end

/**
 * This decorator render the decorated element inside xhtml.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_xhtml
 */
class tao_helpers_form_xhtml_HtmlWrapper
        implements tao_helpers_form_Decorator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute begin
     *
     * @access protected
     * @var string
     */
    protected $begin = '';

    /**
     * Short description of attribute end
     *
     * @access protected
     * @var string
     */
    protected $end = '';

    // --- OPERATIONS ---

    /**
     * Short description of method preRender
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function preRender()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001952 begin
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001952 end

        return (string) $returnValue;
    }

    /**
     * Short description of method postRender
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function postRender()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001954 begin
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001954 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getOption
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string key
     * @return string
     */
    public function getOption($key)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C79 begin
        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C79 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setOption
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string key
     * @param  string value
     * @return boolean
     */
    public function setOption($key, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C7C begin
        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C7C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1-3c8d01cf:1256d79098b:-8000:0000000000001CF5 begin
        // section 127-0-1-1-3c8d01cf:1256d79098b:-8000:0000000000001CF5 end
    }

} /* end of class tao_helpers_form_xhtml_HtmlWrapper */

?>
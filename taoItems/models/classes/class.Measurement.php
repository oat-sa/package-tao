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
 * TAO - taoItems/models/classes/class.Measurement.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.03.2012, 16:42:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-includes begin
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-includes end

/* user defined constants */
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-constants begin
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-constants end

/**
 * Short description of class taoItems_models_classes_Measurement
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_Measurement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute identifier
     *
     * @access private
     * @var string
     */
    private $identifier = '';

    /**
     * Short description of attribute scale
     *
     * @access private
     * @var Scale
     */
    private $scale = null;

    /**
     * Short description of attribute description
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * Short description of attribute humanAssisted
     *
     * @access private
     * @var boolean
     */
    private $humanAssisted = false;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string identifier
     * @param  string description
     * @return mixed
     */
    public function __construct($identifier, $description = null)
    {
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037D8 begin
        $this->identifier	= $identifier;
        $this->description	= $description;
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037D8 end
    }

    /**
     * Short description of method getIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037DD begin
        $returnValue = $this->identifier;
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037DD end

        return (string) $returnValue;
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EE begin
        $returnValue = $this->description;
        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EE end

        return (string) $returnValue;
    }

    /**
     * Short description of method setHumanAssisted
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean humanAssisted
     * @return mixed
     */
    public function setHumanAssisted($humanAssisted)
    {
        // section 127-0-1-1--28e405a3:1362f0fe41e:-8000:0000000000003BC8 begin
        $this->humanAssisted = $humanAssisted;
        // section 127-0-1-1--28e405a3:1362f0fe41e:-8000:0000000000003BC8 end
    }

    /**
     * Short description of method isHumanAssisted
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function isHumanAssisted()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--28e405a3:1362f0fe41e:-8000:0000000000003BC6 begin
        $returnValue = $this->humanAssisted; 
        // section 127-0-1-1--28e405a3:1362f0fe41e:-8000:0000000000003BC6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoItems_models_classes_Scale_Scale
     */
    public function getScale()
    {
        $returnValue = null;

        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EC begin
        $returnValue = $this->scale;
        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EC end

        return $returnValue;
    }

    /**
     * Short description of method setScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Scale scale
     * @return mixed
     */
    public function setScale( taoItems_models_classes_Scale_Scale $scale)
    {
        // section 127-0-1-1-67366732:1359ace6a59:-8000:000000000000382E begin
        $this->scale = $scale;
        // section 127-0-1-1-67366732:1359ace6a59:-8000:000000000000382E end
    }

    /**
     * Short description of method removeScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function removeScale()
    {
        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003831 begin
        $this->scale = null;
        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003831 end
    }

} /* end of class taoItems_models_classes_Measurement */

?>
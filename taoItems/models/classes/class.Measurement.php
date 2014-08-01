<?php
/**  
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

/**
 * Short description of class taoItems_models_classes_Measurement
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 
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
        
        $this->identifier	= $identifier;
        $this->description	= $description;
        
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

        
        $returnValue = $this->identifier;
        

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

        
        $returnValue = $this->description;
        

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
        
        $this->humanAssisted = $humanAssisted;
        
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

        
        $returnValue = $this->humanAssisted; 
        

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

        
        $returnValue = $this->scale;
        

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
        
        $this->scale = $scale;
        
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
        
        $this->scale = null;
        
    }

} /* end of class taoItems_models_classes_Measurement */

?>
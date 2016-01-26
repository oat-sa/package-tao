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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class common_configuration_FileSystemComponent
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_FileSystemComponent
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Whether should be checked recursively (if passed location of dirrectory).
     *
     * @access private
     * @var boolean
     */
    private $recursive = false;
    
    /**
     * Short description of attribute location
     *
     * @access private
     * @var string
     */
    private $location = '';

    /**
     * Short description of attribute expectedRights
     *
     * @access private
     * @var string
     */
    private $expectedRights = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string location
     * @param  string expectedRights
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($location, $expectedRights, $optional = false, $recursive = false)
    {
        
        parent::__construct('tao.configuration.filesystem', $optional);
        $this->setExpectedRights($expectedRights);
        $this->setLocation($location);
        $this->setRecursive($recursive);
    }

    /**
     * Short description of method getLocation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getLocation()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->location;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setLocation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string location
     * @return void
     */
    public function setLocation($location)
    {
        
        $this->location = $location;
        
    }
    
    /**
     * Set $this->recursive value 
     * 
     * @access public
     * @author Aleh Hutnikau, <hutnikau@1pt.com>
     * @param boolean $recursive
     * @return void
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }
    
    /**
     * Get $this->recursive value 
     * 
     * @access public
     * @author Aleh Hutnikau, <hutnikau@1pt.com>
     * @return boolean
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * Short description of method exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        $returnValue = (bool) false;

        
        $returnValue = @file_exists($this->getLocation());
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getExpectedRights
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExpectedRights()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->expectedRights;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setExpectedRights
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string expectedRights
     * @return void
     */
    public function setExpectedRights($expectedRights)
    {
        
        if (!empty($expectedRights) && preg_match('/^r*w*x*$/', $expectedRights) !== 0){
            $this->expectedRights = $expectedRights;    
        }
        else{
            throw new common_configuration_MalformedRightsException("Malformed rights. Expected format is r|rw|rwx.");
        }
        
    }

    /**
     * Short description of method check
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Report
     */
    public function check()
    {
        $returnValue = null;

        
        $expectedRights = $this->getExpectedRights();
        $location = $this->getLocation();
        $name = $this->getName();
        
        if (!$this->exists()){
            return new common_configuration_Report(common_configuration_Report::UNKNOWN,
                                                   "File system component '${name}' could not be found in '${location}'.",
                                                   $this);
        }
        else{
            if (strpos($expectedRights, 'r') !== false && !$this->isReadable($location)){
                return new common_configuration_Report(common_configuration_Report::INVALID,
                                                       "File system component '${name}' in '${location} is not readable.",
                                                       $this);
            }
            
            if (strpos($expectedRights, 'w') !== false && !$this->isWritable($location)){
                return new common_configuration_Report(common_configuration_Report::INVALID,
                                                       "File system component '${name}' in '${location} is not writable.",
                                                       $this);
            }

            if (strpos($expectedRights, 'x') !== false && !$this->isExecutable($location)){
                return new common_configuration_Report(common_configuration_Report::INVALID,
                                                       "File system component '${name}' in '${location} is not executable.",
                                                       $this);
            }
            
            return new common_configuration_Report(common_configuration_Report::VALID,
                                                   "File system component '${name}' in '${location} is compliant with expected rights (${expectedRights}).'",
                                                   $this);
        } 
        

        return $returnValue;
    }

    /**
     * If file is readable.
     *
     * @access public
     * @author Aleh Hutnikau, <hutnikau@1pt.com>
     * @param string $location File location
     * @return boolean
     */
    public function isReadable($location = null)
    {
        $returnValue = true;
        
        if ($location === null) {
            $location = $this->getLocation();
        }
        
        if (is_file($location) || !$this->getRecursive()) {
            $returnValue = is_readable($location);
        } else {
            $recursiveIterator = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($recursiveIterator);
            foreach ($iterator as $file) {
                $returnValue = $returnValue && $file->isReadable();
            }
        }
        
        return $returnValue;
    }

    /**
     * If file is writable.
     *
     * @access public
     * @author Aleh Hutnikau, <hutnikau@1pt.com>
     * @param string $location File location
     * @return boolean
     */
    public function isWritable($location = null)
    {
        $returnValue = true;
        
        if ($location === null) {
            $location = $this->getLocation();
        }
        
        if (is_file($location) || !$this->getRecursive()) {
            $returnValue = is_writable($location);
        } else {
            $recursiveIterator = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($recursiveIterator);
            foreach ($iterator as $file) {
                $returnValue = $returnValue && $file->isWritable();
            }
        }
        
        return $returnValue;
    }

    /**
     * If file is executable.
     *
     * @access public
     * @author Aleh Hutnikau, <hutnikau@1pt.com>
     * @param string $location File location
     * @return boolean
     */
    public function isExecutable($location = null)
    {
        $returnValue = true;
        
        if ($location === null) {
            $location = $this->getLocation();
        }
        
        if (is_file($location) || !$this->getRecursive()) {
            $returnValue = is_executable($location);
        } else {
            $recursiveIterator = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($recursiveIterator);
            foreach ($iterator as $file) {
                $returnValue = $returnValue && $file->isExecutable();
            }
        }
        
        return $returnValue;
    }

}
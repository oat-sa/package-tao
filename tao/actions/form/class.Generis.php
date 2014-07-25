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
 * This container enables gives you tools to create a form from ontology
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:000000000000248A-includes begin
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:000000000000248A-includes end

/* user defined constants */
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:000000000000248A-constants begin
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:000000000000248A-constants end

/**
 * This container enables gives you tools to create a form from ontology
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
abstract class tao_actions_form_Generis
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The uri of the default top level class
     * used by default to limit the recursivity level in the ontology
     *
     * @access protected
     * @var string
     */
    const DEFAULT_TOP_CLASS = 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject';

    /**
     * used to define a top level class
     *
     * @access protected
     * @var Class
     */
    protected $topClazz = null;

    /**
     * the class resource to create the form from
     *
     * @access protected
     * @var Class
     */
    protected $clazz = null;

    /**
     * the resource to create the form from
     *
     * @access protected
     * @var Resource
     */
    protected $instance = null;

    // --- OPERATIONS ---

    /**
     * constructor, set the ontology's class, resource and the form options
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  Resource instance
     * @param  array options
     * @return mixed
     */
    public function __construct( core_kernel_classes_Class $clazz,  core_kernel_classes_Resource $instance = null, $options = array())
    {
        // section 127-0-1-1-ed0d875:129a2b8fa60:-8000:0000000000002060 begin

    	$this->clazz 	= $clazz;
    	$this->instance = $instance;
    	
    	if(isset($options['topClazz'])){
    		$this->topClazz = new core_kernel_classes_Class($options['topClazz']);
    		unset($options['topClazz']);
    	}
    	$returnValue = parent::__construct(array(), $options);
    	
        // section 127-0-1-1-ed0d875:129a2b8fa60:-8000:0000000000002060 end
    }

    /**
     * get the class
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getClazz()
    {
        $returnValue = null;

        // section 127-0-1-1-ed0d875:129a2b8fa60:-8000:0000000000002067 begin
        
        $returnValue = $this->clazz;
        
        // section 127-0-1-1-ed0d875:129a2b8fa60:-8000:0000000000002067 end

        return $returnValue;
    }

    /**
     * get the resource
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getInstance()
    {
        $returnValue = null;

        // section 127-0-1-1-ed0d875:129a2b8fa60:-8000:0000000000002069 begin
        
        $returnValue = $this->instance;
        
        // section 127-0-1-1-ed0d875:129a2b8fa60:-8000:0000000000002069 end

        return $returnValue;
    }

    /**
     * get the current top level class (the defined or the default)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getTopClazz()
    {
        $returnValue = null;

        // section 127-0-1-1--7978326a:129a2dd1980:-8000:0000000000002089 begin
        
   	 	if(!is_null($this->topClazz)){
        	$returnValue = $this->topClazz;
        }
        else{
        	$returnValue = new core_kernel_classes_Class(self::DEFAULT_TOP_CLASS);
        }
        
        // section 127-0-1-1--7978326a:129a2dd1980:-8000:0000000000002089 end

        return $returnValue;
    }

} /* end of abstract class tao_actions_form_Generis */

?>
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
 * This container enables gives you tools to create a form from ontology
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
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
        

    	$this->clazz 	= $clazz;
    	$this->instance = $instance;
    	
    	if(isset($options['topClazz'])){
    		$this->topClazz = new core_kernel_classes_Class($options['topClazz']);
    		unset($options['topClazz']);
    	}
    	$returnValue = parent::__construct(array(), $options);
    	
        
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

        
        
        $returnValue = $this->clazz;
        
        

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

        
        
        $returnValue = $this->instance;
        
        

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

        
        
   	 	if(!is_null($this->topClazz)){
        	$returnValue = $this->topClazz;
        }
        else{
        	$returnValue = new core_kernel_classes_Class(self::DEFAULT_TOP_CLASS);
        }
        
        

        return $returnValue;
    }

} /* end of abstract class tao_actions_form_Generis */

?>
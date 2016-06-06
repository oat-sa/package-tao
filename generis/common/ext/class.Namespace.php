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
 * Short description of class common_ext_Namespace
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_ext_Namespace
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * A unique identifier of the namespace
     *
     * @access protected
     * @var int
     */
    protected $modelId = 0;

    /**
     * the namespace URI
     *
     * @access protected
     * @var string
     */
    protected $uri = '';

    // --- OPERATIONS ---

    /**
     * Create a namespace instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int id
     * @param  string uri
     * @return mixed
     */
    public function __construct($id = 0, $uri = '')
    {
        
        
    	if($id > 0){
    		$this->modelId = $id;
    	}
    	if(!empty($uri)){
    		$this->uri = $uri;
    	}
    	
        
    }

    /**
     * Get the identifier of the namespace instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getModelId()
    {
        $returnValue = (int) 0;

        
        
        $returnValue = $this->modelId;
        
        

        return (int) $returnValue;
    }

    /**
     * Get the namespace URI
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getUri()
    {
        $returnValue = (string) '';

        
        
        $returnValue = $this->uri;
        
        

        return (string) $returnValue;
    }

    /**
     * Magic method, return the Namespace URI
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        
        
        $returnValue = $this->getUri();
        
        

        return (string) $returnValue;
    }

    /**
     * Remove a namespace from the ontology. All triples bound to the model will
     * be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function remove()
    {
        $returnValue = (bool) false;

        
        $db = core_kernel_classes_DbWrapper::singleton();
        if (false === $db->exec("DELETE FROM statements WHERE modelid = ?", array($this->getModelId()))){
        	$returnValue = false;
        }
        else{
        	if (false === $db->exec("DELETE FROM models WHERE modelid = ?", array($this->getModelId()))){
        		$returnValue = false;
        	}
        	else{
        		$returnValue = true;
        	}
        }
        
        

        return (bool) $returnValue;
    }

}
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
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Enables you to manage the module namespaces
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_ext_NamespaceManager
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * the single instance of the NamespaceManager
     *
     * @access private
     * @var NamespaceManager
     */
    private static $instance = null;

    /**
     * Stock the list of all module's namespace, to be retrieved more
     *
     * @access protected
     * @var array
     */
    protected $namespaces = array();

    // --- OPERATIONS ---

    /**
     * Private constructor to force the use of the singleton
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
    }

    /**
     * Main entry point to retrieve the unique NamespaceManager instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_ext_NamespaceManager
     */
    public static function singleton()
    {
        $returnValue = null;

        
        
        if(is_null(self::$instance)){
        	$class = __CLASS__;				//used in case of subclassing
        	self::$instance = new $class();
        }
        $returnValue = self::$instance;
        
        

        return $returnValue;
    }

    /**
     * Get the list of all module's namespaces
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAllNamespaces()
    {
        $returnValue = array();

        
        
        if(count($this->namespaces) == 0){
        	$db = core_kernel_classes_DbWrapper::singleton();
        	$query = 'SELECT modelid, modeluri FROM models';
			$result = $db->query($query);
			
			while ($row = $result->fetch()){	
				$id 	= $row['modelid'];
				$uri 	= $row['modeluri'];
				$this->namespaces[$id] = $uri;
			}
        }
        
        foreach($this->namespaces as $id => $uri){
        	$returnValue[$uri] = new common_ext_Namespace($id, $uri);
        }

        

        return (array) $returnValue;
    }

    /**
     * Conveniance method to retrieve the local Namespace
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_ext_Namespace
     */
    public function getLocalNamespace()
    {
		$localModelUri = LOCAL_NAMESPACE;
		if (substr($localModelUri, -1) != '#') {
		    $localModelUri.= '#';
		}
		return $this->getNamespace($localModelUri);
    }

    /**
     * Get a namesapce identified by the modelId or modelUri
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  modelid
     * @return common_ext_Namespace
     */
    public function getNamespace($modelid)
    {
        $returnValue = null;
    
        if(count($this->namespaces) == 0){
        	$this->getAllNamespaces();	//load the namespaces attribute 
       	}
        
        //get modelId from modelUri
        if(is_string($modelid)){
        	$modelid = array_search($modelid, $this->namespaces);
        }
        
    	//get namespace from modelId
    	if(is_int($modelid)){
        	if(isset($this->namespaces[$modelid])){
        		$returnValue = new common_ext_Namespace($modelid, $this->namespaces[$modelid]);
        	}
        }

        return $returnValue;
    }

    /**
     * Reset the current NamespaceManager instance.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function reset()
    {
        
        $this->namespaces = array();
        
    }

}
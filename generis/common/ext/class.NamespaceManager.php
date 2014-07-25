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
 * @package common
 * @subpackage ext
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

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001593 begin
        
        if(is_null(self::$instance)){
        	$class = __CLASS__;				//used in case of subclassing
        	self::$instance = new $class();
        }
        $returnValue = self::$instance;
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001593 end

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

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001595 begin
        
        if(count($this->namespaces) == 0){
        	$db = core_kernel_classes_DbWrapper::singleton();
        	$query = 'SELECT "modelID", "baseURI" FROM "models"';
			$result = $db->query($query);
			while ($row = $result->fetch()){
				$id 	= $row['modelID'];
				$uri 	= $row['baseURI'];
				$this->namespaces[$id] = $uri;
			}
        }
        
        foreach($this->namespaces as $id => $uri){
        	$returnValue[$uri] = new common_ext_Namespace($id, $uri);
        }
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001595 end

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
        $returnValue = null;

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001597 begin
        
        $session = core_kernel_classes_Session::singleton();
		$localModel = $session->getNameSpace();
		if(!preg_match("/#$/", $localModel)){
			$localModel.= '#';
		}
    	if(count($this->namespaces) == 0){
        	$this->getAllNamespaces();	//load the namespaces attribute 
        }
        if( ($modeId = array_search($localModel, $this->namespaces, true)) !== false ){
        	$returnValue = new common_ext_Namespace($modeId, $this->namespaces[$modeId]);
        }
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001597 end

        return $returnValue;
    }

    /**
     * Get a namesapce identified by the modelId or modelUri
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  modelID
     * @return common_ext_Namespace
     */
    public function getNamespace($modelID)
    {
        $returnValue = null;

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001599 begin
    
        if(count($this->namespaces) == 0){
        	$this->getAllNamespaces();	//load the namespaces attribute 
       	}
        
        //get modelId from modelUri
        if(is_string($modelID)){
        	$modelID = array_search($modelID, $this->namespaces);
        }
        
    	//get namespace from modelId
    	if(is_int($modelID)){
        	if(isset($this->namespaces[$modelID])){
        		$returnValue = new common_ext_Namespace($modelID, $this->namespaces[$modelID]);
        	}
        }
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001599 end

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
        // section 10-13-1-85--470bb007:135aad37aa4:-8000:0000000000001944 begin
        $this->namespaces = array();
        // section 10-13-1-85--470bb007:135aad37aa4:-8000:0000000000001944 end
    }

}
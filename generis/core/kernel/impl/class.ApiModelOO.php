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
 * Generis Object Oriented API - core\kernel\impl\class.ApiModelOO.php
 *
 * Short description of class core_kernel_impl_ApiModelOO
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 15:28:05 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_ApiModelOO
    extends core_kernel_impl_Api
        implements core_kernel_api_ApiModel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var ApiModelOO
     */
    private static $instance = null;

    // --- OPERATIONS ---


    /**
     * build xml rdf containing rdf:Description of all meta-data the conected
     * may get
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array sourceNamespaces
     * @return string
     */
    public function exportXmlRdf($sourceNamespaces = array())
    {
        $returnValue = (string) '';

        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009C0 begin
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$models = array();
		
		foreach($sourceNamespaces as $namespace){
			if(!preg_match("/\#$/", $namespace)){
				$namespace .= "#";
			}
			$result = $dbWrapper->query('SELECT * FROM "models"  WHERE "modelURI" = ? OR "baseURI" = ?', array(
				$namespace,
				$namespace
			));
			if ($row = $result->fetch(PDO::FETCH_ASSOC)){
				$models[] = $row;
				$result->closeCursor();
			}
		}
		
		$allModels = array();
		$result = $dbWrapper->query("SELECT * FROM `models`");
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$allModels[] = $row;
		}
		
		
		if(count($models) > 0){
		
			$baseNs = array(
						'xmlns:rdf'		=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
						'xmlns:rdfs'	=> 'http://www.w3.org/2000/01/rdf-schema#'
					);
					
			$currentNs = array();
			if(count($models) == 1){
				$currentNs['xml:base'] = $namespace;
				$model = $models[0];
				$currentNs["xmlns:ns{$model['modelID']}"] = $namespace;
			}
			foreach($models as $i => $model){
				if(!preg_match("/#$/", $model['modelURI'])){
					$model['modelURI'] .= '#';
				}
				$currentNs["xmlns:ns{$model['modelID']}"] = $model['modelURI'];
			}
			$currentNs = array_merge($baseNs, $currentNs);
			
			$allNs = array();
			foreach($allModels as $i => $model){
				$allNs["xmlns:ns{$model['modelID']}"] = $model['modelURI'];
			}
			$allNs = array_merge($baseNs, $allNs);
				
			try{
				
				$dom = new DOMDocument();
				$dom->formatOutput = true;
				$root = $dom->createElement('rdf:RDF');
				
				foreach($currentNs as $namespaceId => $namespaceUri){
					$root->setAttribute($namespaceId, $namespaceUri);
				}
				$dom->appendChild($root);
					
				foreach($models as $i => $model){
					
					$modelId = $model['modelID'];
					$modelUri = $model['modelURI'];
					
					$subjects = array();
					$result = $dbWrapper->query("SELECT DISTINCT `subject` FROM `statements` WHERE `modelID`=$modelId");
					while($r = $result->fetch()){
						$subjects[] = $r['subject'];
					}
					foreach($subjects as $subject){
						$description = $dom->createElement('rdf:Description');
						$description->setAttribute('rdf:about', $subject);
						
							$result = $dbWrapper->query("SELECT * FROM `statements` WHERE `subject`= '{$subject}'");
							while($t = $result->fetch()){
								
								$predicate 	= trim($t['predicate']);
								$object 	= trim($t['object']);
								$lang 		= trim($t['l_language']);
								
								$nodeName = null;
								
								foreach($allNs as $namespaceId => $namespaceUri){
									if($namespaceId == 'xml:base') {
                                        continue;
                                    }
									if(preg_match("/^".preg_quote($namespaceUri, '/')."/", $predicate)){
										if(!array_key_exists($namespaceId, $currentNs)){
											$currentNs[$namespaceId] = $namespaceUri;
											$root->setAttribute($namespaceId, $namespaceUri);
										}
										$nodeName = str_replace('xmlns:', '', $namespaceId).':'.str_replace($namespaceUri, '', $predicate);
										break;
									}
								}
								$resourceValue = false;
								foreach($allNs as $namespaceId => $namespaceUri){
									if( preg_match("/^".preg_quote($namespaceUri, '/')."/", $object) || 
										preg_match("/^http\:\/\/(.*)\#[a-zA-Z1-9]*/", $object)){
										$resourceValue = true;
										break;
									}
								}
								if(!is_null($nodeName)){
									try{
										$node = $dom->createElement($nodeName);
										if(!empty($lang)){
											$node->setAttribute('xml:lang', $lang);
										}
										if($resourceValue){
												$node->setAttribute('rdf:resource', $object);
										}
										else{
											if(!empty($object) && !is_null($object)){
												
												/**
												 * Replace the CDATA section inside XML fields by a replacement tag:
												 * <![CDATA[ ]]> to <CDATA></CDATA>
												 * @todo check if this behavior is the right
												 */
												$object = str_replace('<![CDATA[', '<CDATA>', $object);
												$object = str_replace(']]>', '</CDATA>', $object);

												$node->appendChild($dom->createCDATASection($object));
											}
										}
										$description->appendChild($node);
									}
									catch(DOMException $de){
										//print $de;
									}
								}
							}
						$root->appendChild($description);
					}
				}
				$returnValue = $dom->saveXml();
			}
			catch(Exception $e){
				print $e;
			}
		}
		
        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009C0 end

        return (string) $returnValue;
    }

    /**
     * import xml rdf files into the knowledge base
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string targetNameSpace
     * @param  string fileLocation
     * @return boolean
     */
    public function importXmlRdf($targetNameSpace, $fileLocation)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009C4 begin
        
        require_once(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
        $session = core_kernel_classes_Session::singleton();
        
	    if(!preg_match("/#$/", $targetNameSpace)){
			$targetNameSpace .= '#';
		}

		// Init RDF API for PHP
		$modFactory = new ModelFactory();
		$memModel 	= $modFactory->getMemModel();
		$dbModel	= $modFactory->getDefaultDbModel($targetNameSpace);
		
		// Load and parse document
		$memModel->load($fileLocation);
		
		$added = 0;
		
		$it = $memModel->getStatementIterator();
		$size = $memModel->size();
		while ($it->hasNext()) {
			$statement = $it->next();
			if($dbModel->add($statement, $session->getUserUri()) === true){
				$added++;
			}
		}
		
		error_reporting(E_ALL);
		
        if($size > 0 && $added > 0){
			$returnValue = true;
        }
		
        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009C4 end

        return (bool) $returnValue;
    }



    /**
     * returns an xml rdf serialization for uriResource with all meta dat found
     * inferenced from te knowlege base about this resource
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string uriResource
     * @return string
     */
    public function getResourceDescriptionXML($uriResource)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009CD begin
        
        
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$subject = $dbWrapper->dbConnector->quote($uriResource);
		
		$baseNs = array(
						'xmlns:rdf'		=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
						'xmlns:rdfs'	=> 'http://www.w3.org/2000/01/rdf-schema#'
					);
		
		$query = 'SELECT "models"."modelID", "models"."modelURI" FROM "models" INNER JOIN "statements" ON "statements"."modelID" = "models"."modelID"
											WHERE "statements"."subject" = ' . $subject;
		$query = $dbWrapper->limitStatement($query, 1);
		$result = $dbWrapper->query($query);
		if($row = $result->fetch()){
			$modelId  = $row['modelID'];
			$modelUri =  $row['modelURI'];
			if(!preg_match("/#$/", $modelUri)){
				$modelUri .= '#';
			}
			
			$result->closeCursor();
		}
		$currentNs = array("xmlns:ns{$modelId}" => $modelUri);
		$currentNs = array_merge($baseNs, $currentNs);
		
		
		$allModels = array();
		$result = $dbWrapper->query('SELECT * FROM "models"');
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			$allModels[] = $row;
		}
		
		$allNs = array();
		foreach($allModels as $i => $model){
			if(!preg_match("/#$/", $model['modelURI'])){
				$model['modelURI'] .= '#';
			}
			$allNs["xmlns:ns{$model['modelID']}"] = $model['modelURI'];
		}
		$allNs = array_merge($baseNs, $allNs);
				
		try{
				
			$dom = new DOMDocument();
			$dom->formatOutput = true;
			$root = $dom->createElement('rdf:RDF');
				
			foreach($currentNs as $namespaceId => $namespaceUri){
				$root->setAttribute($namespaceId, $namespaceUri);
			}
			$dom->appendChild($root);
					
			$description = $dom->createElement('rdf:Description');
			$description->setAttribute('rdf:about', $uriResource);
			
			$result = $dbWrapper->query('SELECT * FROM "statements" WHERE "subject" = ' . $subject);
			while($row = $result->fetch()){
				
				$predicate 	= trim($row['predicate']);
				$object 	= trim($row['object']);
				$lang 		= trim($row['l_language']);
				
				$nodeName = null;
				
				foreach($allNs as $namespaceId => $namespaceUri){
					if($namespaceId == 'xml:base') {
                        continue;
                    }
					if(preg_match("/^".preg_quote($namespaceUri, '/')."/", $predicate)){
						if(!array_key_exists($namespaceId, $currentNs)){
							$currentNs[$namespaceId] = $namespaceUri;
							$root->setAttribute($namespaceId, $namespaceUri);
						}
						$nodeName = str_replace('xmlns:', '', $namespaceId).':'.str_replace($namespaceUri, '', $predicate);
						break;
					}
				}
				
				$resourceValue = false;
				foreach($allNs as $namespaceId => $namespaceUri){
					if( preg_match("/^".preg_quote($namespaceUri, '/')."/", $object) || 
						preg_match("/^http\:\/\/(.*)\#[a-zA-Z1-9]*/", $object)){
						$resourceValue = true;
						break;
					}
				}
				if(!is_null($nodeName)){
					try{
						$node = $dom->createElement($nodeName);
						if(!empty($lang)){
							$node->setAttribute('xml:lang', $lang);
						}
						
						if($resourceValue){
								$node->setAttribute('rdf:resource', $object);
						}
						else{
							if(!empty($object) && !is_null($object)){
								
								/**
								 * Replace the CDATA section inside XML fields by a replacement tag:
								 * <![CDATA[ ]]> to <CDATA></CDATA>
								 * @todo check if this behavior is the right
								 */
								$object = str_replace('<![CDATA[', '<CDATA>', $object);
								$object = str_replace(']]>', '</CDATA>', $object);

								$node->appendChild($dom->createCDATASection($object));
							}
						}
						$description->appendChild($node);
					}
					catch(DOMException $de){
						//print $de;
					}
				}
			}
			$root->appendChild($description);
			$returnValue = $dom->saveXml();
		}
		catch(DomException $e){
			print $e;
		}
        
        
        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009CD end

        return (string) $returnValue;
    }

    /**
     * returns metaclasses tat are not subclasses of other metaclasses
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getMetaClasses()
    {
        $returnValue = null;

        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009CF begin
        $returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        
        $classClass = new core_kernel_classes_Class(RDFS_CLASS);
        foreach($classClass->getSubClasses(true) as $uri => $subClass){
        	$returnValue->add($subClass);
        }
		// section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009CF end

        return $returnValue;
    }

    /**
     * returns classes that are not subclasses of other classes
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRootClasses()
    {
        $returnValue = null;

        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009D6 begin
    
        $returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $query =  "SELECT DISTINCT subject FROM statements WHERE (predicate = ? AND object = ?) 
        			AND subject NOT IN (SELECT subject FROM statements WHERE predicate = ?)";
    	$result	= $dbWrapper->query($query, array(
        	RDF_TYPE,
        	RDFS_CLASS,
        	RDFS_SUBCLASSOF
        ));
        
        while ($row = $result->fetch()) {
       		$returnValue->add(new core_kernel_classes_Class($row['subject']));
        }
		
        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009D6 end

        return $returnValue;
    }

    /**
     * add a new statment to the knowledge base
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function setStatement($subject, $predicate, $object, $language)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009E8 begin
        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $session 	= core_kernel_classes_Session::singleton();
        $localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        $query = 'INSERT INTO "statements" ("modelID","subject","predicate","object","l_language","author","stread","stedit","stdelete","epoch")
        			VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);';

        try{
	        $returnValue = $dbWrapper->exec($query, array(
	       		$localNs->getModelId(),
	       		$subject,
	       		$predicate,
	       		$object,
	       		$language,
	       		$session->getUserUri(),
	       		$mask,
	       		$mask,
	       		$mask
	        ));
        }
        catch (PDOException $e){
	        if($e->getCode() !== '00000'){
				throw new common_Exception ("Unable to setStatement (SPO) {$subject}, {$predicate}, {$object} : " . $e->getMessage());
			}
        }
        
        // section 10-13-1--31--741da406:11928f5acb9:-8000:00000000000009E8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getResourceTree
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string uriResource
     * @param  int depth
     * @return common_Tree
     */
    public function getResourceTree($uriResource, $depth)
    {
        $returnValue = null;

        // section 10-13-1--99-c056755:11a5428ab79:-8000:00000000000010B8 begin
		$factory = core_kernel_classes_TreeFactory::singleton();
		$factory->setOptions(array("properties" => 'true',"instances" =>'true'));
		$factory->setRootClass($uriResource);
		$factory->setSession(core_kernel_classes_Session::singleton());
		$factory->setDepth($depth);
		$returnValue = $factory->getTree();

        // section 10-13-1--99-c056755:11a5428ab79:-8000:00000000000010B8 end

        return $returnValue;
    }

    /**
     * Short description of method getAllClasses
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getAllClasses()
    {
        $returnValue = null;

        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:00000000000019AD begin
    	
        $returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $query =  "SELECT DISTINCT subject FROM statements WHERE (predicate = ? AND object = ?) OR predicate = ?";
    	$result	= $dbWrapper->query($query, array(
        	RDF_TYPE,
        	RDFS_CLASS,
        	RDFS_SUBCLASSOF
        ));
        
        while ($row = $result->fetch()) {
        	$returnValue->add(new core_kernel_classes_Class($row['subject']));
        }
		
        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:00000000000019AD end

        return $returnValue;
    }

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string predicate
     * @param  string object
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubject($predicate, $object)
    {
        $returnValue = null;

        // section 10-13-1--99--65c50b00:11c66591411:-8000:0000000000000E9A begin
		
		$sqlQuery = "SELECT subject FROM statements WHERE predicate = ? AND object= ? ";
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->query($sqlQuery, array (
			$predicate,
			$object
		));
		$returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
		while ($row = $sqlResult->fetch()){
			$container = new core_kernel_classes_Resource($row['subject'], __METHOD__);
			$container->debug = __METHOD__ ;
			$returnValue->add($container);
		}

        // section 10-13-1--99--65c50b00:11c66591411:-8000:0000000000000E9A end

        return $returnValue;
    }

    /**
     * Short description of method removeStatement
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function removeStatement($subject, $predicate, $object, $language)
    {
        $returnValue = (bool) false;

        // section 10-13-1--99--152a2f30:1201eae099d:-8000:0000000000001EAE begin
        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    
        $query = "DELETE FROM statements WHERE subject = ?
        			AND predicate = ? AND object = ?
        			AND (l_language = ? OR l_language = '')";

        $returnValue = $dbWrapper->exec($query, array(
       		$subject,
       		$predicate,
       		$object,
       		$language
        ));
        
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:0000000000001EAE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getObject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string subject
     * @param  string predicate
     * @return core_kernel_classes_ContainerCollection
     */
    public function getObject($subject, $predicate)
    {
        $returnValue = null;

        // section -87--2--3--76-51a982f1:1278aabc987:-8000:000000000000891E begin
    	$sqlQuery = "SELECT object FROM statements WHERE subject = ? AND predicate = ?";
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->query($sqlQuery, array (
			$subject,
			$predicate
		));
		$returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
		while ($row = $sqlResult->fetch()){
			
			$value = $row['object'];
			if(!common_Utils::isUri($value)) {
				$container = new core_kernel_classes_Literal($value);
			}
			else{
				$container = new core_kernel_classes_Resource($value);
			}
			$container->debug = __METHOD__ ;
			$returnValue->add($container);
		}
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:000000000000891E end

        return $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_impl_ApiModelOO
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-13-1--31-4da692cc:119bcf499fd:-8000:0000000000000E92 begin
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;
        // section 10-13-1--31-4da692cc:119bcf499fd:-8000:0000000000000E92 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    private function __construct()
    {
        // section 10-13-1--31-4da692cc:119bcf499fd:-8000:0000000000000E96 begin
        // section 10-13-1--31-4da692cc:119bcf499fd:-8000:0000000000000E96 end
    }

} /* end of class core_kernel_impl_ApiModelOO */

?>
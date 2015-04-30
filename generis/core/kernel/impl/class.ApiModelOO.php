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
 * @package generis
 
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
     * @deprecated
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array sourceNamespaces
     * @return string
     */
    public function exportXmlRdf($sourceNamespaces = array())
    {
        $modelIds = array();
    	foreach($sourceNamespaces as $namespace){
			if(!preg_match("/\#$/", $namespace)){
				$namespace .= "#";
			}

			$result = $dbWrapper->query('SELECT * FROM "models"  WHERE "modeluri" = ?', array(
				$namespace
			));
			if ($row = $result->fetch(PDO::FETCH_ASSOC)){
			    $modelIds[] = $row['modelid'];
				$result->closeCursor();
			}
		}
		return $xml = core_kernel_api_ModelExporter::exportXmlRdf($modelIds);
       
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

        if(!file_exists($fileLocation) || !is_readable($fileLocation)){
            throw new common_Exception("Unable to load ontology : $fileLocation");
        }
        
	    if(!preg_match("/#$/", $targetNameSpace)){
			$targetNameSpace .= '#';
		}
		$modFactory = new core_kernel_api_ModelFactory();
		$returnValue = $modFactory->createModel($targetNameSpace, file_get_contents($fileLocation));
		


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

        
        
        
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$subject = $dbWrapper->quote($uriResource);
		
		$baseNs = array(
						'xmlns:rdf'		=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
						'xmlns:rdfs'	=> 'http://www.w3.org/2000/01/rdf-schema#'
					);
		
		$query = 'SELECT "models"."modelid", "models"."modeluri" FROM "models" INNER JOIN "statements" ON "statements"."modelid" = "models"."modelid"
											WHERE "statements"."subject" = ' . $subject;
		$query = $dbWrapper->limitStatement($query, 1);
		$result = $dbWrapper->query($query);
		if($row = $result->fetch()){
			$modelId  = $row['modelid'];
			$modelUri =  $row['modeluri'];
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
			if(!preg_match("/#$/", $model['modeluri'])){
				$model['modeluri'] .= '#';
			}
			$allNs["xmlns:ns{$model['modelid']}"] = $model['modeluri'];
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

        
        $returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        
        $classClass = new core_kernel_classes_Class(RDFS_CLASS);
        foreach($classClass->getSubClasses(true) as $uri => $subClass){
        	$returnValue->add($subClass);
        }
		

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

        
        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $platform   = $dbWrapper->getPlatForm();
        $localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        $query = 'INSERT INTO statements (modelid,subject,predicate,object,l_language,author,epoch)
        			VALUES  (?, ?, ?, ?, ?, ? , ?);';

        try{
	        $returnValue = $dbWrapper->exec($query, array(
	       		$localNs->getModelId(),
	       		$subject,
	       		$predicate,
	       		$object,
	       		$language,
	       		\common_session_SessionManager::getSession()->getUserUri(),
	            $platform->getNowExpression()
	             
	        ));
        }
        catch (PDOException $e){
	        if($e->getCode() !== '00000'){
				throw new common_Exception ("Unable to setStatement (SPO) {$subject}, {$predicate}, {$object} : " . $e->getMessage());
			}
        }
        
        

        return (bool) $returnValue;
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

        
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;
        

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
        
        
    }

}
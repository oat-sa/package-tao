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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

namespace oat\generisHard\models\hardapi;

/**
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package generisHard
 
 */
class RowManager
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute table
     *
     * @access protected
     * @var string
     */
    protected $table = '';

    /**
     * Short description of attribute columns
     *
     * @access protected
     * @var array
     */
    protected $columns = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string table
     * @param  array columns
     * @return mixed
     */
    public function __construct($table, $columns)
    {
        
        
    	$this->table = $table;
		$this->columns = $columns;
    	
        
    }

    /**
     * Short description of method insertRows
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array rows
     * @return boolean
     */
    public function insertRows($rows)
    {
        $returnValue = (bool) false;

        

        // The class has  multiple properties 
        $multipleColumns = array();
        
        $size = count($rows);
		if($size > 0){
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			//building the insert query
			
			//set the column names
			$query = 'INSERT INTO "'.$this->table.'" (uri';
			foreach($this->columns as $column){
				if(isset($column['multi']) && $column['multi'] === true){
					continue;
				}

				$query .= ', '.$dbWrapper->quoteIdentifier($column['name']).'';
			}
			$query .= ') VALUES ';
			
			$uris = array();
			
			//set the values
			foreach($rows as $i => $row){
				$uris[] = $row['uri'];
				 
				$query.= "('{$row['uri']}'";
				foreach($this->columns as $column){
					
					if(array_key_exists($column['name'], $row)){
						//the property is multiple, postone its treatment
						if(isset($column['multi']) && $column['multi'] === true){
							continue;
						}
						
						else if(isset($column['foreign']) && !empty($column['foreign'])){
							//set the uri of the foreign resource
							$foreignResource = $row[$column['name']];
							if($foreignResource instanceof \core_kernel_classes_Resource){
								$query .= ", '{$foreignResource->getUri()}'";
							}
							else if (!empty($foreignResource)){
								$query.= ", " . $dbWrapper->quote($foreignResource);
							}
							else{
								$query.= ", NULL";
							}
						}
						else{
							
							$value = $row[$column['name']];
							if($value instanceof \core_kernel_classes_Resource){
								$query.= ", '{$value->getUri()}'";
							}
							else{	//the value is a literal
								$value = trim($dbWrapper->quote($value), "'\"");
								$query.= ", '{$value}'";
							}
						}
					}
				}
				$query.= ")";
				if($i < $size-1){
					$query .= ',';
				}
			}

			// Insert rows of the main table			
			$dbWrapper->exec($query);
			
			
			//get the ids of the inserted rows
			$uriList = '';
			foreach($uris as $uri){
				$uriList .= "'$uri',";
			}
			$uriList = substr($uriList, 0, strlen($uriList) -1);
			
			$instanceIds = array();
			
			$query 	= 'SELECT "id", "uri" FROM "'.$this->table.'" WHERE "uri" IN ('.$uriList.')';
			$result = $dbWrapper->query($query);
			while ($r = $result->fetch()){
				$instanceIds[$r['uri']] = $r['id'];
			}
			
			// If the class has multiple properties
			// Insert rows in its associate table <tableName>Props
			foreach ($rows as $row){
				$queryRows = "";
				
				foreach($this->columns as $column){
					
					$multiplePropertyUri = Utils::getLongName($column['name']);
					if (!isset($column['multi']) || $column['multi'] === false || $multiplePropertyUri == RDF_TYPE){
						continue;
					}
					
					$multiQuery = 'SELECT "object", "l_language" FROM "statements" WHERE "subject" = ? AND "predicate" = ?';
					$multiResult = $dbWrapper->query($multiQuery,array($row['uri'], $multiplePropertyUri));
					
					

					while ($t = $multiResult->fetch()){
						if(!(empty($queryRows))){
							$queryRows .= ',';
						}
						
						$object = $dbWrapper->quote($t['object']);
						
						if (\common_Utils::isUri($t['object'])){
							$queryRows .= "({$instanceIds[$row['uri']]}, '{$multiplePropertyUri}', NULL, {$object}, '{$t['l_language']}')";	
						}
						else{
							$queryRows .= "({$instanceIds[$row['uri']]}, '{$multiplePropertyUri}', {$object}, NULL, '{$t['l_language']}')";

						}
					}
				}
				
				if (!empty($queryRows)){
					
					$queryMultiple = 'INSERT INTO "'.$this->table.'props"
						("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") VALUES ' . $queryRows;
					
					$multiplePropertiesResult = $dbWrapper->exec($queryMultiple);
				}
			}
		}
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getForeignIds
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array rows
     * @return array
     */
    protected function getForeignIds($rows)
    {
        $returnValue = array();

        
        
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        
        $foreigns = array();
        foreach($this->columns as $column){
        	
			if(isset($column['foreign']) && !empty($column['foreign'])){
				
				$uriList = '';
				foreach($rows as  $row){
					
					$foreignResource = $row[$column['name']];
					if ($foreignResource!=null){
						if($foreignResource instanceof \core_kernel_classes_Resource){
							$uriList .= "'{$foreignResource->getUri()}',";
						}
					} 
				}
				
				if (!empty ($uriList)){
					$uriList = substr($uriList, 0, strlen($uriList) -1);
				$query = 'SELECT "id", "uri" FROM "'.$column['foreign'].'" WHERE uri IN ('.$uriList.')';
				$result = $dbWrapper->query($query);

				$foreign = array();
				while($r = $result->fetch()){
					
					if(!isset($column['multi']) || $column['multi'] === false){
						$foreign[$r['uri']]  = $r['id'];
					}
					else{
						$key = $r['uri'];
						if(array_key_exists($key, $foreign)){
							$foreign[$r['uri']][] = $r['id'];
						}
						else{
							$foreign[$r['uri']]  = array($r['id']);
						}
					}
				}
				$foreigns[$column['foreign']] = $foreign;
				}
			}
		}
		
		$returnValue = $foreigns;
        
        

        return (array) $returnValue;
    }

}
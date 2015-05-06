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

namespace oat\generisHard\models\hardsql;

use oat\generisHard\models\hardapi\ResourceReferencer;

/**
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generisHard
 
 */
class Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getInstanceId
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public static function getInstanceId( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        
        
    	$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();
    	$table = ResourceReferencer::singleton()->resourceLocation ($resource);
    	
    	try{
	    	$query = 'SELECT "id" FROM "'.$table.'" WHERE uri= ?';
	    	$query = $dbWrapper->limitStatement($query, 1);
	    	$result = $dbWrapper->query($query, array ($resource->getUri()));
	  
	    	if($row = $result->fetch()){
	    		$returnValue = $row['id'];
	    		$result->closeCursor();
	    	}
    	}
    	catch (\PDOException $e){
    		throw new Exception("Unable to find the resource {$resource->getUri()} in {$table} : " .$e->getMessage());
    	}
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getResourceToTableId
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public static function getResourceToTableId( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        
        
    	$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();
    	$query = 'SELECT "id" FROM "resource_to_table" WHERE "uri"=?';
    	$result = $dbWrapper->query($query, array ($resource->getUri()));
    	
    	if ($row = $result->fetch()){
    		$returnValue = $row['id'];
    	}
    
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getClassId
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @param  Resource resource
     * @return string
     */
    public static function getClassId( \core_kernel_classes_Class $class,  \core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        
        
        try{
	        $dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();
	    	$query = 'SELECT "id" FROM "class_to_table" WHERE "uri"=? AND "table"=?';
	    	$result = $dbWrapper->query($query, array (
	    		$class->getUri()
	    		, ResourceReferencer::singleton()->resourceLocation ($resource)
	    	));
	    	
	    	if ($row = $result->fetch()){
	    		$returnValue = $row['id'];
	    	}
        }
        catch (\PDOException $e){
        	throw new Exception("Unable to find the class {$class->getUri()} in class_to_table : " .$e->getMessage());
        }
        
        

        return (string) $returnValue;
    }
    
    public static function getClassInfo(\core_kernel_classes_Class $class){
    	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
    	$sql = 'SELECT "id", "table" FROM "class_to_table" WHERE "uri" = ?';
    	$result = $dbWrapper->query($sql, array($class->getUri()));
    
    	$returnValue = false;
    
    	while ($row = $result->fetch()){
    		$returnValue = array('id' => $row['id'], 'table' => $row['table']);
    		$result->closeCursor();
    		
    		return $returnValue;
    	}
    
    	return $returnValue;
    }

}
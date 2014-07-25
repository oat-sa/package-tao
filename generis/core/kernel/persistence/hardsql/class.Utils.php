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
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/hardsql/class.Utils.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 27.04.2012, 08:21:21 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-includes begin
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-includes end

/* user defined constants */
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-constants begin
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Utils
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Utils
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
    public static function getInstanceId( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:000000000000160E begin
        
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    	$table = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
    	
    	try{
	    	$query = 'SELECT "id" FROM "'.$table.'" WHERE uri= ?';
	    	$query = $dbWrapper->limitStatement($query, 1);
	    	$result = $dbWrapper->query($query, array ($resource->getUri()));
	  
	    	if($row = $result->fetch()){
	    		$returnValue = $row['id'];
	    		$result->closeCursor();
	    	}
    	}
    	catch (PDOException $e){
    		throw new core_kernel_persistence_hardsql_Exception("Unable to find the resource {$resource->getUri()} in {$table} : " .$e->getMessage());
    	}
        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:000000000000160E end

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
    public static function getResourceToTableId( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001611 begin
        
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    	$query = 'SELECT "id" FROM "resource_to_table" WHERE "uri"=?';
    	$result = $dbWrapper->query($query, array ($resource->getUri()));
    	
    	if ($row = $result->fetch()){
    		$returnValue = $row['id'];
    	}
    
        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001611 end

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
    public static function getClassId( core_kernel_classes_Class $class,  core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001614 begin
        
        try{
	        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
	    	$query = 'SELECT "id" FROM "class_to_table" WHERE "uri"=? AND "table"=?';
	    	$result = $dbWrapper->query($query, array (
	    		$class->getUri()
	    		, core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource)
	    	));
	    	
	    	if ($row = $result->fetch()){
	    		$returnValue = $row['id'];
	    	}
        }
        catch (PDOException $e){
        	throw new core_kernel_persistence_hardsql_Exception("Unable to find the class {$class->getUri()} in class_to_table : " .$e->getMessage());
        }
        
        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001614 end

        return (string) $returnValue;
    }
    
    public static function getClassInfo(core_kernel_classes_Class $class){
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
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

} /* end of class core_kernel_persistence_hardsql_Utils */

?>
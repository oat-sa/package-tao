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

/**
 * Short description of class core_kernel_persistence_smoothsql_Utils
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_persistence_smoothsql_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method sortByLanguage
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  dataset
     * @param  string langColname
     * @return array
     */
    public static function sortByLanguage($dataset, $langColname)
    {
        $returnValue = array();

        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $session = core_kernel_classes_Session::singleton(); 
    	$selectedLanguage = $session->getDataLanguage();
    	$defaultLanguage = DEFAULT_LANG;
    	$fallbackLanguage = '';
    					  
    	$sortedResults = array($selectedLanguage => array(),
    						   $defaultLanguage => array(),
    						   $fallbackLanguage => array());
    					  
    	foreach ($dataset as $row) {
    		$sortedResults[$row[$langColname]][] = array('value' => $dbWrapper->getPlatForm()->getPhpTextValue($row['object']), 
    													 'language' => $row[$langColname]);
    	}
    	
    	$returnValue = array_merge($sortedResults[$selectedLanguage], 
    							   (count($sortedResults) > 2) ? $sortedResults[$defaultLanguage] : array(),
    							   $sortedResults[$fallbackLanguage]);
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getFirstLanguage
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array values
     * @return array
     */
    public static function getFirstLanguage($values)
    {
        $returnValue = array();

        
   		if (count($values) > 0) {
    		$previousLanguage = $values[0]['language'];

    		foreach ($values as $value) {
    			if ($value['language'] == $previousLanguage) {
    				$returnValue[] = $value['value'];
    			}
    			else {
    				break;
    			}
    		}
    	}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method filterByLanguage
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  dataset
     * @param  string langColname
     * @return array
     */
    public static function filterByLanguage($dataset, $langColname)
    {
        $returnValue = array();

        
        $result = self::sortByLanguage($dataset, $langColname);
    	$returnValue = self::getFirstLanguage($result);
        

        return (array) $returnValue;
    }

    /**
     * Short description of method identifyFirstLanguage
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array values
     * @return string
     */
    public static function identifyFirstLanguage($values)
    {
        $returnValue = (string) '';

        
    	if (count($values) > 0) {
    		$previousLanguage = $values[0]['language'];
    		$returnValue = $previousLanguage;
    		
    		foreach ($values as $value) {
    			if ($value['language'] == $previousLanguage) {
    				continue;
    			}
    			else {
    				$returnValue = $previousLanguage;
    				break;
    			}
    		}
    	}
        

        return (string) $returnValue;
    }

    /**
     * Build a SQL search pattern on basis of a pattern and a comparison mode.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string pattern A value to compare.
     * @param  boolean like The manner to compare values. If set to true, the LIKE SQL operator will be used. If set to false, the = (equal) SQL operator will be used.
     * @return string
     */
    public static function buildSearchPattern($pattern, $like = true)
    {
        $returnValue = (string) '';
    
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
    
        switch (gettype($pattern)) {
        	case 'string' :
        	case 'numeric':
        	    $patternToken = $pattern;
        	    $object = trim(str_replace('*', '%', $patternToken));
    
        	    if($like){
        	        if(!preg_match("/^%/", $object)){
        	            $object = "%" . $object;
        	        }
        	        if(!preg_match("/%$/", $object)){
        	            $object = $object . "%";
        	        }
        	        $returnValue .= ' LIKE '. $dbWrapper->quote($object);
        	    }
        	    else {
        	        $returnValue = (strpos($object, '%') !== false)
        	        ? 'LIKE '. $dbWrapper->quote($object)
        	        : '= '. $dbWrapper->quote($patternToken);
        	    }
        	    break;
    
        	case 'object' :
        	    if($pattern instanceof core_kernel_classes_Resource) {
        	        $returnValue = ' = ' . $dbWrapper->quote($pattern->getUri());
        	    } else {
        	        common_Logger::w('non ressource as search parameter: '. get_class($pattern), 'GENERIS');
        	    }
        	    break;
    
        	default:
        	    throw new common_Exception("Unsupported type for searchinstance array: " . gettype($value));
        }
    
        return (string) $returnValue;
    }
}
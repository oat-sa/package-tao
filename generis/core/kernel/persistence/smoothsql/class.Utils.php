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
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.07.2011, 08:42:00 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-includes begin
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-includes end

/* user defined constants */
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-constants begin
// section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190D-constants end

/**
 * Short description of class core_kernel_persistence_smoothsql_Utils
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
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

        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190E begin
        $session = core_kernel_classes_Session::singleton(); 
    	$selectedLanguage = $session->getDataLanguage();
    	$defaultLanguage = DEFAULT_LANG;
    	$fallbackLanguage = '';
    					  
    	$sortedResults = array($selectedLanguage => array(),
    						   $defaultLanguage => array(),
    						   $fallbackLanguage => array());
    					  
    	foreach ($dataset as $row) {
    		$sortedResults[$row[$langColname]][] = array('value' => $row['object'], 
    													 'language' => $row[$langColname]);
    	}
    	
    	$returnValue = array_merge($sortedResults[$selectedLanguage], 
    							   (count($sortedResults) > 2) ? $sortedResults[$defaultLanguage] : array(),
    							   $sortedResults[$fallbackLanguage]);
        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:000000000000190E end

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

        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001912 begin
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
        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001912 end

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

        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001915 begin
        $result = self::sortByLanguage($dataset, $langColname);
    	$returnValue = self::getFirstLanguage($result);
        // section 10-13-1-85-61dcfc6d:1301cc5c657:-8000:0000000000001915 end

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

        // section 10-13-1-85--4651ba20:1301d2ffa69:-8000:0000000000001915 begin
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
        // section 10-13-1-85--4651ba20:1301d2ffa69:-8000:0000000000001915 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Utils */

?>
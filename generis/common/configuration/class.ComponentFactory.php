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
 * Generis Object Oriented API - common\configuration\class.ComponentFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.11.2012, 11:31:18 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D73-includes begin
// section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D73-includes end

/* user defined constants */
// section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D73-constants begin
// section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D73-constants end

/**
 * Short description of class common_configuration_ComponentFactory
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_ComponentFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute fileSystemCount
     *
     * @access private
     * @var int
     */
    private static $fileSystemCount = 0;

    /**
     * Short description of attribute mockCount
     *
     * @access private
     * @var int
     */
    private static $mockCount = 0;

    // --- OPERATIONS ---

    /**
     * Short description of method buildPHPRuntime
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string min
     * @param  string max
     * @param  boolean optional
     * @return common_configuration_PHPRuntime
     */
    public static function buildPHPRuntime($min, $max = null, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D74 begin
        $returnValue = new common_configuration_PHPRuntime($min, $max, $optional);
        // section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D74 end

        return $returnValue;
    }

    /**
     * Short description of method buildPHPExtension
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @param  string min
     * @param  string max
     * @param  boolean optional
     * @return common_configuration_PHPExtension
     */
    public static function buildPHPExtension($name, $min = null, $max = null, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D84 begin
        $returnValue = new common_configuration_PHPExtension($min, $max, $name, $optional);
        // section 10-13-1-85-54b22cba:13b2c6aa086:-8000:0000000000001D84 end

        return $returnValue;
    }

    /**
     * Short description of method buildPHPINIValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @param  string expectedValue
     * @param  boolean optional
     * @return common_configuration_PHPINIValue
     */
    public static function buildPHPINIValue($name, $expectedValue, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001D8A begin
        $returnValue = new common_configuration_PHPINIValue($expectedValue, $name, $optional);
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001D8A end

        return $returnValue;
    }

    /**
     * Short description of method buildPHPDatabaseDriver
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @param  boolean optional
     * @return common_configuration_PHPDatabaseDriver
     */
    public static function buildPHPDatabaseDriver($name, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001D94 begin
        $returnValue = new common_configuration_PHPDatabaseDriver(null, null, $name, $optional);
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001D94 end

        return $returnValue;
    }

    /**
     * Short description of method buildFileSystemComponent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string location
     * @param  string expectedRights
     * @param  boolean optional
     * @return common_configuration_FileSystemComponent
     */
    public static function buildFileSystemComponent($location, $expectedRights, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001D9D begin
        $returnValue = new common_configuration_FileSystemComponent($location, $expectedRights, $optional = false);
        self::incrementFileSystemCount();
        $returnValue->setName('FileSystemComponentCheck_' . self::getFileSystemCount());
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001D9D end

        return $returnValue;
    }

    /**
     * Short description of method buildCustom
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @param  string extension
     * @param  boolean optional
     * @return common_configuration_Component
     */
    public static function buildCustom($name, $extension, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-4f783fb:13b2cada0cf:-8000:0000000000001DB0 begin
    	// Camelize the name to find it in the checks folder.
        $name = explode('_', $name);
        for ($i = 0; $i < count($name); $i++){
            $name[$i] = ucfirst($name[$i]);
        }
        $name = implode('', $name);
        $checkClassName = "${extension}_install_checks_${name}";
        
        // Instanciate the Component.
        try{
            $checkClass = new ReflectionClass($checkClassName);
            $returnValue = $checkClass->newInstanceArgs(array("custom_${extension}_${name}", $optional));
        }
        catch (LogicException $e){
	        $msg = "Cannot instantiate custom check '${name}' for extension '${extension}': ";
	        $msg .= $e->getMessage();
	        throw new common_configuration_ComponentFactoryException($msg);
        }
        catch (ReflectionException $e){
        	$msg = "Cannot instantiate custom check '${name}' for extension '${extension}': ";
	        $msg .= $e->getMessage();
	        throw new common_configuration_ComponentFactoryException($msg);
        }
        // section 10-13-1-85-4f783fb:13b2cada0cf:-8000:0000000000001DB0 end

        return $returnValue;
    }

    /**
     * Short description of method buildMock
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int expectedStatus
     * @param  boolean optional
     * @return common_configuration_Mock
     */
    public static function buildMock($expectedStatus, $optional = false)
    {
        $returnValue = null;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DA7 begin
        self::incrementMockCount();
        $returnValue = new common_configuration_Mock($expectedStatus, 'MockComponentCheck_' . self::getMockCount());
        $returnValue->setOptional($optional);
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DA7 end

        return $returnValue;
    }

    /**
     * Short description of method buildFromArray
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array array
     * @return common_configuration_Component
     */
    public static function buildFromArray($array)
    {
        $returnValue = null;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB0 begin
        if (!empty($array)){
        	if (!empty($array['type'])){
        		$acceptedTypes = array('PHPRuntime', 'PHPINIValue', 'PHPExtension', 'PHPDatabaseDriver', 'FileSystemComponent', 'Custom', 'Mock');
        		$cleanType = preg_replace('/^Check/i', '', $array['type']);
        		if (in_array($cleanType, $acceptedTypes)){
        			
        			if (!empty($array['value'])){
        				$values = $array['value'];
        				
        				// Optional parameter is always used.
        				$optional = false;
        				if (!empty($values['optional'])){
        					$optional = $values['optional'];
        				}
        				
	        			switch ($cleanType){
	        				case 'PHPRuntime':
	        					$max = null;
	        					if (!empty($values['max'])){
	        						$max = $values['max'];
	        					}
	        					
	        					if (empty($values['min'])){
	        						$msg = "Mandatory attribute 'min' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					$returnValue = self::buildPHPRuntime($values['min'], $max, $optional);
	        				break;
	        				
	        				case 'PHPINIValue':
	        					if (empty($values['name'])){
	        						$msg = "Mandatory attribute 'name' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					if (empty($values['value']) && $values['value'] !== '0'){
	        						$msg = "Mandatory attribute 'value' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					$returnValue = self::buildPHPINIValue($values['name'], $values['value'], $optional);
	        				break;
	        				
	        				case 'PHPExtension':
	        					if (empty($values['name'])){
	        						$msg = "Mandatory attribute 'name' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					$min = null;
	        					if (!empty($values['min'])){
	        						$min = $values['min'];	
	        					}
	        					
	        					$max = null;
	        					if (!empty($values['max'])){
	        						$max = $values['max'];	
	        					}
	        					
	        					$returnValue = self::buildPHPExtension($values['name'], $min, $max, $optional);
	        				break;
	        				
	        				case 'PHPDatabaseDriver':
	        					if (empty($values['name'])){
	        						$msg = "Mandatory attribute 'name' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					$returnValue = self::buildPHPDatabaseDriver($values['name'], $optional);
	        				break;
	        				
	        				case 'FileSystemComponent':
	        					if (empty($values['location'])){
	        						$msg = "Mandatory attribute 'location' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					if (empty($values['rights'])){
	        						$msg = "Mandatory attribute 'rights' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}
	        					
	        					$returnValue = self::buildFileSystemComponent($values['location'], $values['rights'], $optional);
	        				break;
	        				
	        				case 'Custom':
	        					if (empty($values['name'])){
	        						$msg = "Mandatory attribute 'name' is missing.";
	        						throw new common_configuration_ComponentFactoryException($msg);	
	        					}

	        					$extension = 'generis';
	        					if (!empty($values['extension'])){
	        						$extension = $values['extension'];
	        					}
	        					
	        					$returnValue = self::buildCustom($values['name'], $extension, $optional);
	        				break;
	        				
	        				case 'Mock':
	        					$status = common_configuration_Report::VALID; 
	        					if (!empty($values['status'])){
	        						$status = $values['status'];
	        					}
	        					
	        					$returnValue = self::buildMock($status, $optional);
	        				break;
	        			}	
        			}
        			else{
        				$msg = "No 'value' array provided.";
        				throw new common_configuration_ComponentFactoryException($msg);
        			}
        		}
        		else{
        			$msg = "Unknown 'type' = '${cleanType}'.";
        			throw new common_configuration_ComponentFactoryException($msg);
        		}
        	}
        	else{
        		$msg = "Cannot build a Configuration Component without 'type'.";
        		throw new common_configuration_ComponentFactoryException($msg);
        	}
        }
        else{
        	$msg = 'Cannot build a Configuration Component with an empty array.';
        	throw new common_configuration_ComponentFactoryException($msg);
        }
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB0 end

        return $returnValue;
    }

    /**
     * Short description of method getFileSystemCount
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    private static function getFileSystemCount()
    {
        $returnValue = (int) 0;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB3 begin
        $returnValue = self::$fileSystemCount;
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB3 end

        return (int) $returnValue;
    }

    /**
     * Short description of method setFileSystemCount
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int fileSystemCount
     * @return void
     */
    private static function setFileSystemCount($fileSystemCount)
    {
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB5 begin
        self::$fileSystemCount = $fileSystemCount;
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB5 end
    }

    /**
     * Short description of method incrementFileSystemCount
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    private static function incrementFileSystemCount()
    {
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB8 begin
        $count = self::getFileSystemCount();
        $count++;
        self::setFileSystemCount($count);
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DB8 end
    }

    /**
     * Short description of method getMockCount
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    private static function getMockCount()
    {
        $returnValue = (int) 0;

        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DBA begin
        $returnValue = self::$mockCount;
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DBA end

        return (int) $returnValue;
    }

    /**
     * Short description of method setMockCount
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int mockCount
     * @return void
     */
    private static function setMockCount($mockCount)
    {
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DBC begin
        self::$mockCount = $mockCount;
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DBC end
    }

    /**
     * Short description of method incrementMockCount
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    private static function incrementMockCount()
    {
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DBF begin
        $count = self::getMockCount();
        $count++;
        self::setMockCount($count);
        // section 10-13-1-85-14a261be:13b2c72d816:-8000:0000000000001DBF end
    }

} /* end of class common_configuration_ComponentFactory */

?>
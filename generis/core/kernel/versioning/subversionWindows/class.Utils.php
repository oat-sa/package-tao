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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * Short description of class core_kernel_versioning_subversionWindows_Utils
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_subversionWindows_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method exec
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  string command
     * @return string
     */
    public static function exec( core_kernel_classes_Resource $resource, $command)
    {
        $returnValue = (string) '';

        
        $username = "";
        $password = "";
        $repository = null;
        
        try{
        	if(empty($command)){
        		throw new Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : $command_ must be specified');
        	}
        	
			//get context variables
			if($resource instanceof core_kernel_versioning_File){
				$repository = $resource->getRepository();
			}else if($resource instanceof core_kernel_versioning_Repository){
				$repository = $resource;
			}else{
				throw new Exception('The first parameter (resource) should be a File or a Repository');
			}
        	
			if(is_null($repository)){
				throw new Exception('Unable to find the repository to work with for the reference resource ('.$resource->getUri().')');
			}
			
			$username = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN));
			$password = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD));
			
        	$returnValue = shell_exec('svn --username ' . $username . ' --password ' . $password . ' ' . $command);
//                var_dump('svn --username ' . $username . ' --password ' . $password . ' ' . $command);
//                var_dump($returnValue);
        }
        catch (Exception $e){
        	die('Error code `svn_error_command` in ' . $e->getMessage());
        }
        

        return (string) $returnValue;
    }

}
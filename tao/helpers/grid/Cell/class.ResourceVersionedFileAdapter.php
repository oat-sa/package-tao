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
 * 
 */

/**
 * Short description of class tao_helpers_grid_Cell_ResourceVersionedFileAdapter
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 
 */
class tao_helpers_grid_Cell_ResourceVersionedFileAdapter
    extends tao_helpers_grid_Cell_VersionedFileAdapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getVersionedFile
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return core_kernel_classes_Resource
     */
    public function getVersionedFile($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        
        if(empty($data)){
        	throw new Exception('data can not be empty');
        }
    	if(!empty($data) && common_Utils::isUri($data)){
			$data = new core_kernel_classes_Resource($data);
		}
		if(!core_kernel_versioning_File::isVersionedFile($data)){
			throw new Exception('data has to be a valid versioned file uri');
		}
		
		$returnValue = $data;
        

        return $returnValue;
    }

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return int
     */
    public function getVersion($rowId, $columnId, $data = null)
    {
        $returnValue = (int) 0;

        
		$returnValue = null;
        

        return (int) $returnValue;
    }

}

?>
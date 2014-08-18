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
 * Short description of class core_kernel_versioning_FileInterface
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
interface core_kernel_versioning_FileInterface
{


    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::commit()
     */
    public function commit( core_kernel_file_File $resource, $message, $path, $recursive = false);

    /**
     * Short description of method update
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     * @see core_kernel_versioning_File::update()
     */
    public function update( core_kernel_file_File $resource, $path, $revision = null);

    /**
     * Short description of method revert
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     * @see core_kernel_versioning_File::revert()
     */
    public function revert( core_kernel_file_File $resource, $revision = null, $msg = "");

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::delete()
     */
    public function delete( core_kernel_file_File $resource, $path);

    /**
     * Short description of method add
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_file_File $resource, $path, $recursive = false, $force = false);

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     * @see core_kernel_versioning_File::gethistory()
     */
    public function getHistory( core_kernel_file_File $resource, $path);

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  array options
     * @return int
     */
    public function getStatus( core_kernel_file_File $resource, $path, $options = array());

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  string version
     * @return boolean
     */
    public function resolve( core_kernel_file_File $resource, $path, $version);

} /* end of interface core_kernel_versioning_FileInterface */

?>
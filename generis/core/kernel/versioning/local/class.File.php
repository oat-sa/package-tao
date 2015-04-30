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
 * Short description of class core_kernel_versioning_local_File
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_local_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var File
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::commit()
     */
    public function commit( core_kernel_file_File $resource, $message, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method update
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     * @see core_kernel_versioning_File::update()
     */
    public function update( core_kernel_file_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method revert
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     * @see core_kernel_versioning_File::revert()
     */
    public function revert( core_kernel_file_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        
		throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::delete()
     */
    public function delete( core_kernel_file_File $resource, $path)
    {
        $returnValue = (bool) false;

        
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = helpers_File::remove($path);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_file_File $resource, $path, $recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        
        common_Logger::i(__FUNCTION__.' called on local directory ', 'LOCALVCS');
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     * @see core_kernel_versioning_File::gethistory()
     */
    public function getHistory( core_kernel_file_File $resource, $path)
    {
        $returnValue = array();

        
		throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by local directory');
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  array options
     * @return int
     */
    public function getStatus( core_kernel_file_File $resource, $path, $options = array())
    {
        $returnValue = (int) 0;

        
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = VERSIONING_FILE_STATUS_NORMAL;
        

        return (int) $returnValue;
    }

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  string version
     * @return boolean
     */
    public function resolve( core_kernel_file_File $resource, $path, $version)
    {
        $returnValue = (bool) false;

        
		throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_versioning_local_File
     */
    public static function singleton()
    {
        $returnValue = null;

        
        if(is_null(self::$instance)){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
		

        return $returnValue;
    }

}
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
 * Short description of class core_kernel_versioning_FileProxy
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_FileProxy
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var FileProxy
     */
    private static $instance = null;

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
    public function commit( core_kernel_file_File $resource, $message, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->commit($resource, $message, $path, $recursive);
        

        return (bool) $returnValue;
    }

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
    public function update( core_kernel_file_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->update($resource, $path, $revision);
        

        return (bool) $returnValue;
    }

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
    public function revert( core_kernel_file_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->revert($resource, $revision, $msg);
        

        return (bool) $returnValue;
    }

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
    public function delete( core_kernel_file_File $resource, $path)
    {
        $returnValue = (bool) false;

        
        //update before delete, else we get an out of date exception
        $resource->update();
        //and delete
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->delete($resource, $path);
        

        return (bool) $returnValue;
    }

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
    public function add( core_kernel_file_File $resource, $path, $recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->add($resource, $path, $recursive, $force);
        

        return (bool) $returnValue;
    }

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
    public function getHistory( core_kernel_file_File $resource, $path)
    {
        $returnValue = array();

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->getHistory($resource, $path);
        

        return (array) $returnValue;
    }

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
    public function getStatus( core_kernel_file_File $resource, $path, $options = array())
    {
        $returnValue = (int) 0;

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->getStatus($resource, $path, $options);
        

        return (int) $returnValue;
    }

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
    public function resolve( core_kernel_file_File $resource, $path, $version)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($resource);
		$returnValue = $delegate->resolve($resource, $path, $version);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getImplementationToDelegateTo
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_versioning_FileInterface
     */
    public function getImplementationToDelegateTo( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        
        
		$repository = $resource->getRepository();
		if(!is_null($repository)){

            $VCStype = $repository->getVCSType();
			$implClass = '';

			// Function of the repository type, define the implementation to attack
			switch ($VCStype->getUri()) {
				case PROPERTY_GENERIS_VCS_TYPE_SUBVERSION:
					$implClass = 'core_kernel_versioning_subversion_File';
					break;
				case PROPERTY_GENERIS_VCS_TYPE_SUBVERSION_WIN:
					$implClass = 'core_kernel_versioning_subversionWindows_File';
					break;
				case INSTANCE_GENERIS_VCS_TYPE_LOCAL:
	        		$implClass = 'core_kernel_versioning_local_File';
	        		break;
	        	default:
	        		throw new common_exception_Error('unknown Version Control System '.$VCStype->getLabel().'('.$VCStype.')');
			}

			// If an implementation has been found
			if (!empty($implClass)) {
				$reflectionMethod = new ReflectionMethod($implClass, 'singleton');
				$delegate = $reflectionMethod->invoke(null);
				$returnValue = $delegate;
			}
			
		}else{
			throw new core_kernel_versioning_exception_FileUnversionedException('no repository associated to the aledged versioned file');
		}

        

        return $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_FileProxy
     */
    public static function singleton()
    {
        $returnValue = null;

        
		if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_FileProxy();
		}
		$returnValue = self::$instance;
        

        return $returnValue;
    }

}
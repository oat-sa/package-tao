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
 * Short description of class core_kernel_versioning_RepositoryProxy
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_RepositoryProxy
        implements core_kernel_versioning_RepositoryInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var RepositoryProxy
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method checkout
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string url
     * @param  string path
     * @param  int revision
     * @return boolean
     */
    public function checkout( core_kernel_versioning_Repository $vcs, $url, $path, $revision = null)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($vcs);
        $returnValue = $delegate->checkout($vcs, $url, $path, $revision);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method authenticate
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function authenticate( core_kernel_versioning_Repository $vcs, $login, $password)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($vcs);
        $returnValue = $delegate->authenticate($vcs, $login, $password);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method export
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  int revision
     * @return boolean
     */
    public function export( core_kernel_versioning_Repository $vcs, $src, $target = null, $revision = null)
    {
        $returnValue = (bool) false;

        
        $delegate = $this->getImplementationToDelegateTo($vcs);
        $returnValue = $delegate->export($vcs, $src, $target, $revision);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method import
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  string message
     * @param  array options
     * @return core_kernel_file_File
     */
    public function import( core_kernel_versioning_Repository $vcs, $src, $target, $message = "", $options = array())
    {
        $returnValue = null;

        
        $delegate = $this->getImplementationToDelegateTo($vcs);
        $returnValue = $delegate->import($vcs, $src, $target, $message, $options);
        

        return $returnValue;
    }

    /**
     * Short description of method listContent
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string path
     * @param  int revision
     * @return array
     */
    public function listContent( core_kernel_versioning_Repository $vcs, $path, $revision = null)
    {
        $returnValue = array();

        
        $delegate = $this->getImplementationToDelegateTo($vcs);
        $returnValue = $delegate->listContent($vcs, $path, $revision);
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getImplementationToDelegateTo
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_versioning_RepositoryInterface
     */
    public function getImplementationToDelegateTo( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        
    	$VCStype = $resource->getVCSType();
        $implClass = '';
        
        // Function of the repository type, define the implementation to attack

        switch ($VCStype->getUri())
        {
        	case PROPERTY_GENERIS_VCS_TYPE_SUBVERSION:
        		$returnValue = core_kernel_versioning_subversion_Repository::singleton();
        		break;
        	case PROPERTY_GENERIS_VCS_TYPE_SUBVERSION_WIN:
        		$returnValue = core_kernel_versioning_subversionWindows_Repository::singleton();
        		break;
        	case INSTANCE_GENERIS_VCS_TYPE_LOCAL:
        		$returnValue = core_kernel_versioning_local_Repository::singleton();
        		break;
			default:
        		throw new common_exception_Error('unknown Version Control System '.$VCStype->getLabel().'('.$VCStype.')');
        }
        

        return $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_RepositoryProxy
     */
    public static function singleton()
    {
        $returnValue = null;

        
		if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_RepositoryProxy();
		}
		$returnValue = self::$instance;
        

        return $returnValue;
    }

}
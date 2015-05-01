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
 * Short description of class core_kernel_versioning_local_Repository
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_local_Repository
        implements core_kernel_versioning_RepositoryInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Repository
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method checkout
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Repository vcs
     * @param  string url
     * @param  string path
     * @param  int revision
     * @return boolean
     */
    public function checkout( core_kernel_versioning_Repository $vcs, $url, $path, $revision = null)
    {
        $returnValue = (bool) false;

        
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method authenticate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Repository vcs
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function authenticate( core_kernel_versioning_Repository $vcs, $login, $password)
    {
        $returnValue = (bool) false;

        
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = is_dir($vcs->getPath());
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method export
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  int revision
     * @return boolean
     */
    public function export( core_kernel_versioning_Repository $vcs, $src, $target = null, $revision = null)
    {
        $returnValue = (bool) false;

        
        throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method import
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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

        
        throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        

        return $returnValue;
    }

    /**
     * Short description of method listContent
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Repository vcs
     * @param  string path
     * @param  int revision
     * @return array
     */
    public function listContent( core_kernel_versioning_Repository $vcs, $path, $revision = null)
    {
        $returnValue = array();

        
        throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        

        return (array) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_versioning_local_Repository
     */
    public static function singleton()
    {
        $returnValue = null;

        
        if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_local_Repository();
		}
		$returnValue = self::$instance;
        

        return $returnValue;
    }

}
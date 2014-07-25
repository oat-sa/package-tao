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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
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
 * Automatically generated on 19.12.2012, 14:52:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_local
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_versioning_RepositoryInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/versioning/interface.RepositoryInterface.php');

/* user defined includes */
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E38-includes begin
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E38-includes end

/* user defined constants */
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E38-constants begin
// section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E38-constants end

/**
 * Short description of class core_kernel_versioning_local_Repository
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_local
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

        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 begin
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = true;
        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 end

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

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016E6 begin
        common_Logger::i(__FUNCTION__.' called on local directory', 'LOCALVCS');
        $returnValue = is_dir($vcs->getPath());
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016E6 end

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

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:000000000000290C begin
        throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:000000000000290C end

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

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002912 begin
        throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002912 end

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

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002916 begin
        throw new core_kernel_versioning_exception_Exception(__METHOD__.' not supported by Local Directory');
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002916 end

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

        // section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E45 begin
        if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_local_Repository();
		}
		$returnValue = self::$instance;
        // section 10-30-1--78-73b2f78e:13bb35d7c97:-8000:0000000000001E45 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_local_Repository */

?>
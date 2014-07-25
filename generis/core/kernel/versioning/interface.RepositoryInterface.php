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
 * Automatically generated on 11.01.2012, 15:16:55 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002502-includes begin
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002502-includes end

/* user defined constants */
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002502-constants begin
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002502-constants end

/**
 * Short description of class core_kernel_versioning_RepositoryInterface
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */
interface core_kernel_versioning_RepositoryInterface
{


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
    public function checkout( core_kernel_versioning_Repository $vcs, $url, $path, $revision = null);

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
    public function authenticate( core_kernel_versioning_Repository $vcs, $login, $password);

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
    public function export( core_kernel_versioning_Repository $vcs, $src, $target = null, $revision = null);

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
    public function import( core_kernel_versioning_Repository $vcs, $src, $target, $message = "", $options = array());

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
    public function listContent( core_kernel_versioning_Repository $vcs, $path, $revision = null);

} /* end of interface core_kernel_versioning_RepositoryInterface */

?>
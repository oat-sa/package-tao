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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.GenerisInstaller.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.06.2012, 11:18:54 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_ext_ExtensionInstaller
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/ext/class.ExtensionInstaller.php');

/* user defined includes */
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-includes begin
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-includes end

/* user defined constants */
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-constants begin
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-constants end

/**
 * Short description of class common_ext_GenerisInstaller
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_GenerisInstaller
    extends common_ext_ExtensionInstaller
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method install
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function install()
    {
        // section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A40 begin
    	if ($this->extension->getID() != 'generis') {
    		throw new common_ext_ExtensionException('Tried to install "'.$this->extension->getID().'" using the GenerisInstaller');
    	}
        //$this->installCustomScript();
		//$this->installWriteConfig();
		$this->installOntology();
		//$this->installLocalData();
		//$this->installWriteConfig();
		//$this->installModuleModel();
		//$this->installRegisterExt();
		
		common_cache_FileCache::singleton()->purge();
        // section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A40 end
    }

} /* end of class common_ext_GenerisInstaller */

?>
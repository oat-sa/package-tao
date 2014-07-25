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
 * This exception must be thrown when a manifest is malformed e.g. missing
 * data, syntax, ...
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * An exception that occurs in the context of Extension Manifests.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/ext/class.ManifestException.php');

/* user defined includes */
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-includes begin
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-includes end

/* user defined constants */
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-constants begin
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-constants end

/**
 * This exception must be thrown when a manifest is malformed e.g. missing
 * data, syntax, ...
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_MalformedManifestException
    extends common_ext_ManifestException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_MalformedManifestException */

?>
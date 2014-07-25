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
 * Any exception related to extensions should inherit this class.
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002342-includes begin
// section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002342-includes end

/* user defined constants */
// section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002342-constants begin
// section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002342-constants end

/**
 * Any exception related to extensions should inherit this class.
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionException extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The extension ID related to the exception.
     *
     * @access private
     * @var Integer
     */
    private $extensionId = null;

    // --- OPERATIONS ---

    /**
     * Sets the extension ID related to the exception.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extensionId An extension ID.
     * @return mixed
     */
    public function setExtensionId($extensionId)
    {
        // section -64--88-56-1--5ed7f181:1380f260043:-8000:0000000000001A65 begin
        $this->extensionId = $extensionId;
        // section -64--88-56-1--5ed7f181:1380f260043:-8000:0000000000001A65 end
    }

    /**
     * Get the extension ID related to the exception
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExtensionId()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--5ed7f181:1380f260043:-8000:0000000000001A6B begin
        $returnValue = $this->extensionId;
        // section -64--88-56-1--5ed7f181:1380f260043:-8000:0000000000001A6B end

        return (string) $returnValue;
    }

    /**
     * Creates a new instance of ExtensionException.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string message
     * @param  string extensionId
     * @return mixed
     */
    public function __construct($message, $extensionId = 'unknown')
    {
        // section -64--88-56-1--5ed7f181:1380f260043:-8000:0000000000001A6F begin
        parent::__construct($message);
        $this->setExtensionId($extensionId);
        // section -64--88-56-1--5ed7f181:1380f260043:-8000:0000000000001A6F end
    }

} /* end of class common_ext_ExtensionException */

?>
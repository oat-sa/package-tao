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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * UriProvider implementation based on a serial stored in the database.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage uri
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Any implementation of the AbstractUriProvider class aims at providing unique
 * to client code. It should take into account the state of the Knowledge Base
 * avoid collisions. The AbstractUriProvider::provide method must be implemented
 * subclasses to return a valid URI.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/uri/class.AbstractUriProvider.php');

/* user defined includes */
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-includes begin
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-includes end

/* user defined constants */
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-constants begin
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-constants end

/**
 * UriProvider implementation based on a serial stored in the database.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage uri
 */
class common_uri_DatabaseSerialUriProvider
    extends common_uri_AbstractUriProvider
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Generates a URI based on a serial stored in the database.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     * @throws common_UriProviderException
     */
    public function provide()
    {
        $returnValue = (string) '';

        // section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A5 begin
        $driver = $this->getDriver();
        switch ($driver){
            case 'pdo_pgsql':
            case 'pdo_mysql':
                $dbWrapper = core_kernel_classes_DbWrapper::singleton();
                $modelUri = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
                $sth = $dbWrapper->prepare("SELECT generis_sequence_uri_provider(?)");
        		if ($sth->execute(array($modelUri)) !== false){
        			$row = $sth->fetch();
        			$returnValue = $row[0];
        			$sth->closeCursor();
        		}
        		else{
        			throw new common_uri_UriProviderException("An error occured while calling the stored procedure for driver '${driver}': " . $dbWrapper->errorMessage() . ".");	
        		}
                
            break;
        	default:
        		throw new common_uri_UriProviderException("Unknown database driver : ".$driver);
        	break;
        }
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A5 end

        return (string) $returnValue;
    }

} /* end of class common_uri_DatabaseSerialUriProvider */

?>
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
 * The ServiceFactory enable you to get Service instances dynamically.
 * Use the ServiceFactory::get(serviceName) to retrieve a single instance of a
 * implementation.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 * @version 0.1
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001829-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001829-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001829-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001829-constants end

/**
 * The ServiceFactory enable you to get Service instances dynamically.
 * Use the ServiceFactory::get(serviceName) to retrieve a single instance of a
 * implementation.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 * @version 0.1
 */
class tao_models_classes_ServiceFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Entry point to get an instance of a service 
     * by it's short name.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  string serviceName The name of the service you want to retrieve. You can set the complete class name, the interface name or only the ressource name managed by the service.
     * @return tao_models_classes_GenerisService
     */
    public static function get($serviceName)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001832 begin
        $returnValue = tao_models_classes_Service::getServiceByName($serviceName);
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001832 end

        return $returnValue;
    }

} /* end of class tao_models_classes_ServiceFactory */

?>
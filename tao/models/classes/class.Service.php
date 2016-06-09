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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Service is the base class of all services, and implements the singleton
 * for derived services
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
abstract class tao_models_classes_Service {
    // --- ASSOCIATIONS ---
    // --- ATTRIBUTES ---

    /**
     * Contains the references of each service instance. 
     * The service name is used as key.
     *
     * @access private
     * @var array
     */
    private static $instances = array();

    /**
     * pattern to create service dynamically.
     * Use the printf syntax, where %1$ is the short name of the service
     *
     * @access private
     * @var string
     */

    const namePattern = 'tao%1$s_models_classes_%1$sService';

    // --- OPERATIONS ---

    /**
     * protected constructor to enforce the singleton pattern
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function __construct() {
        
        
    }

    /**
     * returns an instance of the service defined by servicename. Always returns
     * same instance for a class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     *
     * @param  string $serviceName
     *
     * @return tao_models_classes_Service
     * @throws common_exception_Error
     */
    public static function getServiceByName($serviceName) {
        $returnValue = null;

        
        $className = (!class_exists($serviceName) || !preg_match("/^(tao|wf)/", $serviceName)) ? sprintf(self::namePattern, ucfirst(strtolower($serviceName))) : $serviceName;

        // does the class exist
        if (!class_exists($className)) {
            throw new common_exception_Error('Tried to init abstract class ' . $className);
        }

        $class = new ReflectionClass($className);
        // is it concrete
        if ($class->isAbstract()) {
            throw new common_exception_Error('Tried to init abstract class ' . $className . ' for param \'' . $serviceName . '\'');
        }
        // does it extend Service
        if (!$class->isSubclassOf('tao_models_classes_Service')) {
            throw new common_exception_Error("$className must referr to a class extending the tao_models_classes_Service");
        }

        //create the instance only once
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
        }

        //get the instance
        $returnValue = self::$instances[$className];
        

        return $returnValue;
    }

    /**
     * returns an instance of the service the function was called from. Always
     * the same instance for a class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return $this
     */
    public static function singleton() {
        $returnValue = null;

        
        $serviceName = get_called_class();
        if (!isset(self::$instances[$serviceName])) {
            self::$instances[$serviceName] = new $serviceName();
        }

        $returnValue = self::$instances[$serviceName];
        

        return $returnValue;
    }

}

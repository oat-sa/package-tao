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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

namespace oat\generisHard\helpers;

use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\switcher\Switcher;

/**
 * This helper class provides helper methods dealing with optimization of TAO.
 * 
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class Optimization {
    
   /**
    * The COMPILED constant.
    *
    * @var string
    */
    const COMPILED = 'compiled';
    
    /**
     * The DECOMPILED constant.
     *
     * @var string
     */
    const DECOMPILED = 'decompiled';
    
    /**
     * Get the instance count of a particular optimizable class.
     *
     */
    public static function getClassInstancesCount(\core_kernel_classes_Class $class){
        return count($class->getInstances($class, true));
    }
    
    /**
     * Returns the list of optimizable classes. The classes that are optimizable are found
     * in the manifests of extensions that are currently installed.
     *
     * + The returned associative array has key values corresponding to a Class URI, and the values
     * are the optimization options that have to be used in order to optimize classes.
     *
     * + The optimization options are associative arrays containing boolean values depicting how
     * the class must be optmized. The following keys are provided:
     *
     *  - compile: array('recursive' =>          true/false,
     *                   'append' =>              true/false,
     *                   'rmSources' =>           true/false)
     *
     *  - decompile: array('recursive' =>        true/false)
     *
     * + The status of classes (compiled or decompiled) are described in the 'status' key.
     *
     *  - status: string	'compiled'/'decompiled
     *
     * + The amount of already compiled/decompiled instances of classes are described in the 'count' key.
     *
     *  - count: integer
     *
     * + The class name of classes are described in the 'class' key:
     *
     *  - class: string		(e.g. 'User', 'Item', ...)
     *
     * + The class URI of classes are described in the 'classUri' key:
     *
     * - classUri: string	(e.g. 'http://myplatform/mytao.rdf#user1', ...)
     *
     * @return array The Optimizable classes, their optimization options and miscellaneous information.
     */
    public static function getOptimizableClasses(){
    
        $returnValue = array();
    
        $optionsCompile = array(
            'recursive'             => true,
            'append'                => true,
            'rmSources'             => true
        );
    
        $optionsDecompile = array(
            'recursive'             => true
        );
    
        $defaultOptions = array(
            'compile' => $optionsCompile,
            'decompile' => $optionsDecompile
        );
    
        $optimizableClasses = array();
        $extManager = \common_ext_ExtensionsManager::singleton();
        $extensions = $extManager->getInstalledExtensions();
    
        foreach ($extensions as $ext){
            $optimizableClasses = array_merge($optimizableClasses, $ext->getOptimizableClasses());
        }
    
        $optimizableClasses = array_unique($optimizableClasses);
        $referencer = ResourceReferencer::singleton();
    
        foreach ($optimizableClasses as $optClass){
            $optClass = new \core_kernel_classes_Class($optClass);
            	
            $values['compile'] = $defaultOptions['compile'];
            $values['decompile'] = $defaultOptions['decompile'];
            $values['count'] = self::getClassInstancesCount($optClass);
            $values['class'] = $optClass->getLabel();
            $values['classUri'] = $optClass->getUri();
            $values['status'] = ($referencer->isClassReferenced($optClass)) ? self::COMPILED : self::DECOMPILED;
            $returnValue[$optClass->getUri()] = $values;
        }
    
        return $returnValue;
    }
    
    /**
     * Convenience method to compile a class. The optimization options are retrieved from
     * data returned by getOptimizableClasses().
     *
     * This method returns an associative structure containing the following informations:
     *
     * array(
     *    "success" => true/false,
     *    "count" => integer // the class instances that were optimized
     *    "relatedClasses" => array("class1", "class2", ...) // the classes that were impacted by the optimization
     *                                                // depending on the optimization options
     * )
     *
     * @param \core_kernel_classes_Class A class to compile.
     * @see Optimization::getOptimizableClasses()
     * @return array
     */
    public static function compileClass(\core_kernel_classes_Class $class){
    
        $result = array('success' => false);
        $optimizableClasses = self::getOptimizableClasses();
         
        if(isset($optimizableClasses[$class->getUri()]) && isset($optimizableClasses[$class->getUri()]['compile'])){
    
            //build the option array and launch the compilation:
            $options = array_merge($optimizableClasses[$class->getUri()]['compile']);
    
            $switcher = new Switcher();
            $switcher->hardify($class, $options);
    
            //prepare return value
            $hardenedClasses = $switcher->getHardenedClasses();
            $count = isset($hardenedClasses[$class->getUri()]) ? $hardenedClasses[$class->getUri()] : 0;
            $relatedClasses = array();
            foreach($hardenedClasses as $relatedClassUri => $nb){
                if($relatedClassUri != $class->getUri()){
                    $relatedClass = new \core_kernel_classes_Class($relatedClassUri);
                    $relatedClasses[$relatedClass->getLabel()] = $nb;
                }
            }
    
            $result = array(
                'success'    => true,
                'count'     => $count,
                'relatedClasses' => $relatedClasses
            );
    
            unset($switcher);
        }
    
        return $result;
    }
    
    /**
     * Decompile a specific class with the decompilation options found in the data returned
     * by the decompileClass method.
     *
     * This method returns an associative array containing the following informations:
     *
     * array(
     *    "success" => true/false,
     *    "count" => integer // the class instances that were unoptimized
     *    "relatedClasses" => array("class1", "class2", ...) // the classes that were impacted by the unoptimization
     *                                                // depending on the unoptimization options
     * )
     *
     * @see Optimization::decompileClass()
     * @param \core_kernel_classes_Class class The class you would like to compile.
     */
    public static function decompileClass(\core_kernel_classes_Class $class){
    
        $result = array('success' => false);
        $optimizableClasses = self::getOptimizableClasses();
         
        if(isset($optimizableClasses[$class->getUri()]) && isset($optimizableClasses[$class->getUri()]['decompile'])){
    
            //build the option array and launch the compilation:
            $userDefinedOptions = array();
            $options = array_merge($optimizableClasses[$class->getUri()]['decompile'], $userDefinedOptions);
    
            $switcher = new Switcher();
            $switcher->unhardify($class, $options);
    
            //prepare return value
            $decompiledClass = $switcher->getDecompiledClasses();
            $count = isset($decompiledClass[$class->getUri()])?$decompiledClass[$class->getUri()]:0;
            $relatedClasses = array();
            foreach($decompiledClass as $relatedClassUri => $nb){
                if($relatedClassUri != $class->getUri()){
                    $relatedClass = new \core_kernel_classes_Class($relatedClassUri);
                    $relatedClasses[$relatedClass->getLabel()] = $nb;
                }
            }
    
            $result = array(
                'success'    => true,
                'count'     => $count,
                'relatedClasses' => $relatedClasses
            );
    
            unset($switcher);
        }
    
        return $result;
    }
}
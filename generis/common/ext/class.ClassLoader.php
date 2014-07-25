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
 * Generis Object Oriented API - common/ext/class.ClassLoader.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 01.10.2012, 09:53:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
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
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-includes begin
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-includes end

/* user defined constants */
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-constants begin
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-constants end

/**
 * Short description of class common_ext_ClassLoader
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ClassLoader
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * array[folder] to register set of packages to the autoload
     *
     * @access private
     * @var array
     */
    private $packages = array();

    /**
     * array[class => file] to register set of files to the autoload
     *
     * @access private
     * @var array
     */
    private $files = array();

    /**
     * Short description of attribute singleton
     *
     * @access private
     * @var ClassLoader
     */
    private static $singleton = null;

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_ext_ClassLoader
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B40 begin
        if (is_null(self::$singleton)) {
        	self::$singleton = new self();
        }
        $returnValue = self::$singleton;
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B40 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B42 begin
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B42 end
    }

    /**
     * add folder to the classLoader
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string package
     * @return mixed
     */
    public function addPackage($package )
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B8 begin
		$this->packages[] = $package;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B8 end
    }

    /**
     * add file to the classLoader for a specific class
     *
     * @access public
     * @author lionel.lecaque@tudor.lu
     * @param  string file
     * @param  string class
     * @return mixed
     */
    public function addFile($file, $class)
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017BB begin
		$this->files[$class] = $file;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017BB end
    }

    /**
     * return all files the classloader will have to autoload
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getFiles()
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CA begin
        return $this->files;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CA end
    }

    /**
     * return all packages the classloader will have to autoload
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getPackages()
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CC begin
        return $this->packages;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CC end
    }

    /**
     * set an array[class => files] the classloader have to autoload
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array files
     * @return mixed
     */
    public function setFiles($files)
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CE begin
        $this->files = $files;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CE end
    }

    /**
     * set an array[folder] the classloader have to autoload
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array packages
     * @return mixed
     */
    public function setPackages($packages)
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017D1 begin
		$this->packages = $packages;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017D1 end
    }

    /**
     * Short description of method register
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function register()
    {
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B48 begin
        spl_autoload_register(array($this, 'autoload'));
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B48 end
    }

    /**
     * Short description of method autoload
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string className
     * @return mixed
     */
    public function autoload($className)
    {
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B3E begin
        //var_dump($className);
		$files = $this->getFiles();
        if(!empty($files) && is_array($files)){
            if(isset($files[$className])){
                require_once ($files[$className]);
                return;
            }
        }
        $packages = $this->getPackages();

        if(!empty($packages) && is_array($packages)){
            foreach($packages as $path) {

                if (file_exists($path. $className . '.class.php')) {
                    require_once $path . $className . '.class.php';
                    return;
                }
                if (file_exists($path. 'class.'.$className . '.php')) {
                    require_once $path . 'class.'. $className . '.php';
                    return;
                }
            }
        }
        $split = explode("_",$className);
        $path = GENERIS_BASE_PATH.'/../';
        for ( $i = 0 ; $i<sizeof($split)-1 ; $i++){
            $path .= $split[$i].'/';
        }
        $filePath = $path . 'class.'.$split[sizeof($split)-1] . '.php';

        if (file_exists($filePath)){
            require_once $filePath;
            return;
        }
        else{
        	$filePath = $path . 'interface.'.$split[sizeof($split)-1] . '.php';
        	if (file_exists($filePath)){
        		require_once $filePath;
        		return;
        	}
        }
        // section 10-30-1--78--3a1a6c41:13a1b46a114:-8000:0000000000001B3E end
    }

} /* end of class common_ext_ClassLoader */

?>
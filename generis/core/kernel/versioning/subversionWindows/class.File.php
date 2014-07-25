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
 * Automatically generated on 02.02.2012, 16:53:22 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_versioning_FileInterface
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/versioning/interface.FileInterface.php');

/* user defined includes */
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-includes begin
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-includes end

/* user defined constants */
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-constants begin
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-constants end

/**
 * Short description of class core_kernel_versioning_subversionWindows_File
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */
class core_kernel_versioning_subversionWindows_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var File
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::commit()
     */
    public function commit( core_kernel_file_File $resource, $message, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A begin
        
        try {
            $rStr = !$recursive ? '--non-recursive' : '';
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'commit "' . $path . '" -m "'. $message . '" '.$rStr);
        }
        catch (Exception $e) {
        	die('Error code `svn_error_commit` in ' . $e->getMessage());
        }

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method update
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     * @see core_kernel_versioning_File::update()
     */
    public function update( core_kernel_file_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C begin
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'update "' . $path .'"');
        } 
        catch (Exception $e) {
        	die('Error code `svn_error_update` in ' . $e->getMessage());
        }
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method revert
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     * @see core_kernel_versioning_File::revert()
     */
    public function revert( core_kernel_file_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E begin
        
        /**
         * @todo make all the functions coherent
         * Sometimes u pass a path sometimes not, the resource is enough
         * if we use just the resource, the only way to use svn will be to use generis ... so ... 6
         */
        $path = $resource->getAbsolutePath();
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'revert "' . $path.'"');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_revert` in ' . $e->getMessage());
        }
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::delete()
     */
    public function delete( core_kernel_file_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 begin
        
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'delete "' . $path.'" --force');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_delete` in ' . $e->getMessage());
        }
        
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_file_File $resource, $path, $recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 begin
        
        try {
            $rStr = !$recursive ? '--non-recursive' : '';
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'add "' . $path .'" '.$rStr);
        } 
        catch (Exception $e) {
        	die('Error code `svn_error_add` in ' . $e->getMessage());
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     * @see core_kernel_versioning_File::gethistory()
     */
    public function getHistory( core_kernel_file_File $resource, $path)
    {
        $returnValue = array();

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB begin
        
        $xmlStr = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'log "' . $path .'" --xml');
        //$xmlStr = implode('', $arrayLog);
        
        $dom = new DOMDocument();
        @$dom->loadXML($xmlStr);

        $xpath = new DOMXPath($dom);
        $entries = $xpath->query("//logentry");

        foreach ($entries as $entry) {
            $returnValue[] = array(
                 'revision'   => $entry->getAttribute('revision')
                 , 'author'     => $entry->getElementsByTagName('author')->item(0)->nodeValue
                 , 'time'       => strtotime($entry->getElementsByTagName('date')->item(0)->nodeValue)
                 , 'msg'        => $entry->getElementsByTagName('msg')->item(0)->nodeValue
            );
        }
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  array options
     * @return int
     */
    public function getStatus( core_kernel_file_File $resource, $path, $options = array())
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001902 begin
        
        $returnValue = null;
        $resourceStatus = null;
        
        $statuses = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'status "' . $path .'" --show-updates');
        $lines = explode("\n", $statuses);
                foreach ($lines as $line) {
                    //if(preg_match('#^.*'.preg_quote($path).'$#', $line)){
                    $pattern = '@'.preg_quote($path).'$@';
                    if(preg_match($pattern, $line) != 0){
                        $resourceStatus = $line;
                        break;
                    }
                }
                
        // If the file has a status, check the status is not unversioned or added
        if (!is_null($resourceStatus)) {
                
            $text_status = substr($resourceStatus, 0, 1);
            switch ($text_status) {
                        case '?':
                            $returnValue = VERSIONING_FILE_STATUS_UNVERSIONED;
                            break;
                        case 'A':
                            $returnValue = VERSIONING_FILE_STATUS_ADDED;
                            break;
                        case 'A':
                            $returnValue = VERSIONING_FILE_STATUS_DELETED;
                            break;
                        case 'C':
                            $returnValue = VERSIONING_FILE_STATUS_CONFLICTED;
                            break;
                        case 'M':
                            $returnValue = VERSIONING_FILE_STATUS_MODIFIED;
                            break;
                        case 'R':
                            $returnValue = VERSIONING_FILE_STATUS_REPLACED;
                            break;
                        case ' ':
                            $returnValue = VERSIONING_FILE_STATUS_REMOTELY_MODIFIED;
                            break;
                    }
        } else {
            //the file is maybe inside an unversioned folder (status null, info null)
            $info = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'info "' . $path . '"');
            if (!empty($info) && file_exists($path)) {
                $returnValue = VERSIONING_FILE_STATUS_NORMAL;
            } else {
                $returnValue = VERSIONING_FILE_STATUS_UNVERSIONED;
        }
        }
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001902 end

        return (int) $returnValue;
    }

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  string version
     * @return boolean
     */
    public function resolve( core_kernel_file_File $resource, $path, $version)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001921 begin
        
        $returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'resolve --accept '.$version.' "' . $path .'"');
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001921 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_file_File
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188E begin
        
        if (self::$instance == null){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
        
        // section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188E end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversionWindows_File */

?>
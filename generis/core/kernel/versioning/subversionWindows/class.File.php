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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * Short description of class core_kernel_versioning_subversionWindows_File
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
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

        
        
        try {
            $rStr = !$recursive ? '--non-recursive' : '';
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'commit "' . $path . '" -m "'. $message . '" '.$rStr);
        }
        catch (Exception $e) {
        	die('Error code `svn_error_commit` in ' . $e->getMessage());
        }

        

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

        
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'update "' . $path .'"');
        } 
        catch (Exception $e) {
        	die('Error code `svn_error_update` in ' . $e->getMessage());
        }
        

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

        
        
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'delete "' . $path.'" --force');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_delete` in ' . $e->getMessage());
        }
        
        

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

        
        
        try {
            $rStr = !$recursive ? '--non-recursive' : '';
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'add "' . $path .'" '.$rStr);
        } 
        catch (Exception $e) {
        	die('Error code `svn_error_add` in ' . $e->getMessage());
        }
        
        

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

        
        
        $returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'resolve --accept '.$version.' "' . $path .'"');
        
        

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

        
        
        if (self::$instance == null){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
        
        

        return $returnValue;
    }

}
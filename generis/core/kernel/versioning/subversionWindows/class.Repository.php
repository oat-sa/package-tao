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
 * Short description of class
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_subversionWindows_Repository
        implements core_kernel_versioning_RepositoryInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Repository
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method checkout
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string url
     * @param  string path
     * @param  int revision
     * @return boolean
     */
    public function checkout( core_kernel_versioning_Repository $vcs, $url, $path, $revision = null)
    {
        $returnValue = (bool) false;

        
        
        try {
        	$url = (string) $vcs->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL));
        	$path = (string) $vcs->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH));
        	$path = ($path[strlen($path)-1] == DIRECTORY_SEPARATOR) ? substr($path, 0, strlen($path)-1) : $path;
        	
        	if (empty($url)){
        		throw new common_Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : the url must be specified');
        	}
        	if (empty($path)){
        		throw new common_Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : the path must be specified');
        	}

        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'checkout ' . $url . ' "' . $path .'"');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_checkout` in ' . $e->getMessage());
        }

        

        return (bool) $returnValue;
    }

    /**
     * Short description of method authenticate
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function authenticate( core_kernel_versioning_Repository $vcs, $login, $password)
    {
        $returnValue = (bool) false;

        
        throw new core_kernel_versioning_exception_Exception("The function (".__METHOD__.") is not available in this versioning implementation (".__CLASS__.")");
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method export
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  int revision
     * @return boolean
     */
    public function export( core_kernel_versioning_Repository $vcs, $src, $target = null, $revision = null)
    {
        $returnValue = (bool) false;

        
        $r=!is_null($revision)?' -r '.$revision:'';
        $returnValue = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'export --force "' . $src . '" "' . $target.'"'.$r);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method import
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  string message
     * @param  array options
     * @return core_kernel_file_File
     */
    public function import( core_kernel_versioning_Repository $vcs, $src, $target, $message = "", $options = array())
    {
        $returnValue = null;

        
        
        $saveResource = isset($options['saveResource']) && $options['saveResource'] ? true : false;
        $result = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'import -m "'.stripslashes($message).'" "' . $src . '" "' . $target.'"');
        
        //Save a resource
        if($saveResource){
            $folderName = basename($src);
            $relativePath = $target.$folderName;
            $folder = $vcs->createFile('', $relativePath);
            $returnValue = $folder;
        }
        
        

        return $returnValue;
    }

    /**
     * Short description of method listContent
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string path
     * @param  int revision
     * @return array
     */
    public function listContent( core_kernel_versioning_Repository $vcs, $path, $revision = null)
    {
        $returnValue = array();

        
        
        $r=!is_null($revision)?' -r '.$revision:'';
        $xmlStr = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'list --xml "' . $path.'"'.$r);
        
        $dom = new DOMDocument();
        @$dom->loadXML($xmlStr);

        $xpath = new DOMXPath($dom);
        $entries = $xpath->query("//entry");

        foreach ($entries as $entry) {
            $returnValue[] = array(
                 'name'         => $entry->getElementsByTagName('name')->item(0)->nodeValue
                 , 'type'       => $entry->getAttribute('kind')
                 , 'revision'   => $entry->getElementsByTagName('commit')->item(0)->getAttribute('revision')
                 , 'author'     => $entry->getElementsByTagName('commit')->item(0)->getElementsByTagName('author')->item(0)->nodeValue
                 , 'time'       => strtotime($entry->getElementsByTagName('commit')->item(0)->getElementsByTagName('date')->item(0)->nodeValue)
            );
        }

        

        return (array) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_subversion_Repository
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
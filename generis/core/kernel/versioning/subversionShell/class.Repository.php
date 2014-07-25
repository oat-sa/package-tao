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
 * Automatically generated on 11.01.2012, 12:05:46 with ArgoUML PHP module 
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
 * include core_kernel_versioning_RepositoryInterface
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/versioning/interface.RepositoryInterface.php');

/* user defined includes */
// section 127-0-1-1-a831e14:134415460c1:-8000:0000000000001890-includes begin
// section 127-0-1-1-a831e14:134415460c1:-8000:0000000000001890-includes end

/* user defined constants */
// section 127-0-1-1-a831e14:134415460c1:-8000:0000000000001890-constants begin
// section 127-0-1-1-a831e14:134415460c1:-8000:0000000000001890-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
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

        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 begin
        
        try {
        	$url = $vcs->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL));
        	$path = $vcs->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH));
        	
        	if (empty($url)){
        		throw new common_Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : the url must be specified');
        	}
        	if (empty($path)){
        		throw new common_Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : the path must be specified');
        	}

        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'checkout "' . $url . '" "' . $path .'" 2>&1');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_checkout` in ' . $e->getMessage());
        }

        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 end

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

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016E6 begin
        throw new core_kernel_versioning_exception_Exception("The function (".__METHOD__.") is not available in this versioning implementation (".__CLASS__.")");
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016E6 end

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

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:000000000000290C begin
        $r=!is_null($revision)?' -r '.$revision:'';
        $returnValue = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'export "' . $src . '" "' . $target.'"'.$r);
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:000000000000290C end

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

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002912 begin
        
        //Save the imported resource in the onthology ?
        $saveResource = isset($options['saveResource']) && $options['saveResource'] ? true : false;
        //Import the folder in the repository
        $result = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'import -m "'.stripslashes($message).'" "' . $src . '" "' . $target.'"');
        
        $repositoryUrl = $vcs->getUrl();
        $relativePath = substr($target, strlen($repositoryUrl));
        $folder = $vcs->createFile('', $relativePath);
        $folder->update();
        
        //Save a resource
        if($saveResource){
            $returnValue = $folder;
        }else{
            $resourceToDelete = new core_kernel_classes_Resource($folder);
            $resourceToDelete->delete();
        }
        
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002912 end

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

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002916 begin
        
        $r=!is_null($revision)?' -r '.$revision:'';
        $arrayList = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'list --xml "' . $path.'"'.$r);
        $xmlStr = implode('', $arrayList);
        
        $dom = new DOMDocument();
        $dom->loadXML($xmlStr);

        $xpath = new DOMXPath($dom);
        $entries = $xpath->query("//entry");

        foreach($entries as $entry){
            $returnValue[] = array(
                 'name'         => $entry->getElementsByTagName('name')->item(0)->nodeValue
                 , 'type'       => $entry->getAttribute('kind')
                 , 'revision'   => $entry->getElementsByTagName('commit')->item(0)->getAttribute('revision')
                 , 'author'     => $entry->getElementsByTagName('commit')->item(0)->getElementsByTagName('author')->item(0)->nodeValue
                 , 'time'       => strtotime($entry->getElementsByTagName('commit')->item(0)->getElementsByTagName('date')->item(0)->nodeValue)
            );
        }

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002916 end

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

        // section 127-0-1-1-a831e14:134415460c1:-8000:0000000000001894 begin
        if (self::$instance == null){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
        // section 127-0-1-1-a831e14:134415460c1:-8000:0000000000001894 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversionWindows_Repository */

?>
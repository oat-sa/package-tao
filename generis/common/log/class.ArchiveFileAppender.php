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
 * Generis Object Oriented API - common/log/class.ArchiveFileAppender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 31.01.2012, 12:07:23 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_log_SingleFileAppender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/class.SingleFileAppender.php');

/* user defined includes */
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-includes begin
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-includes end

/* user defined constants */
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-constants begin
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-constants end

/**
 * Short description of class common_log_ArchiveFileAppender
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
class common_log_ArchiveFileAppender
    extends common_log_SingleFileAppender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute COMPRESSION_ZIP
     *
     * @access public
     * @var string
     */
    const COMPRESSION_ZIP = 'zip';

    /**
     * Short description of attribute COMPRESSION_NONE
     *
     * @access public
     * @var string
     */
    const COMPRESSION_NONE = 'none';

    /**
     * Short description of attribute directory
     *
     * @access public
     * @var string
     */
    public $directory = '';

    /**
     * Short description of attribute compression
     *
     * @access public
     * @var string
     */
    public $compression = 'zip';

    // --- OPERATIONS ---

    /**
     * Short description of method init
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001866 begin
        if (isset($configuration['directory']) && $configuration['directory']) {
        	$this->directory = rtrim($configuration['directory'],DIRECTORY_SEPARATOR);
        } elseif (isset($configuration['file'])) {
        	$this->directory = dirname($configuration['file']);
        }
        if (isset($configuration['compression'])) {
        	if (is_bool($configuration['compression'])) {
        		$this->compression = $configuration['compression'] ? self::COMPRESSION_ZIP : self::COMPRESSION_NONE; 
        	} else {
        		switch ($configuration['compression']) {
	        		case self::COMPRESSION_ZIP:
	        			$this->compression = self::COMPRESSION_ZIP;
	        			break;
	        		case self::COMPRESSION_NONE:
	        			$this->compression = self::COMPRESSION_NONE;
	        			break;
	        		default:
	        			return false;
	        	}
        	}
        }
        
        if (!empty($this->directory)){
        	$returnValue = parent::init($configuration);
        }
        else{
        	$returnValue = false;
        }
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001866 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method initFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initFile()
    {
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001868 begin
    	if ($this->maxFileSize > 0 && file_exists($this->filename) && filesize($this->filename) >= $this->maxFileSize) {
	    	
	    	if ($this->compression == self::COMPRESSION_ZIP) {
	    		$zip = new ZipArchive;
				$res = $zip->open($this->getAvailableArchiveFileName(), ZipArchive::CREATE);
				if ($res === true) {
				    $zip->addFile($this->filename, basename($this->filename));
				    $zip->close();
				    unlink($this->filename);
				} else {
					//fail silently
					return false;
				}	
	    	} elseif ($this->compression == self::COMPRESSION_NONE) {
	    		$success = rename($this->filename, $this->getAvailableArchiveFileName());
	    		if (!$success) {
					//fail silently
					return false;
	    		}
	    	} else {
				//fail silently
				return false;
	    	}
    	}
    	$this->filehandle = @fopen($this->filename, 'a');
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001868 end

    }

    /**
     * Short description of method getAvailableArchiveFileName
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    private function getAvailableArchiveFileName()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-57e432c8:135336d8bea:-8000:0000000000004762 begin
    	$filebase = basename($this->filename);
    	$dotpos = strrpos($filebase, ".");
    	if ($dotpos === false) {
    		$dotpos = strlen($filebase);
    	}
    	$prefix = $this->directory.DIRECTORY_SEPARATOR.substr($filebase, 0, $dotpos)."_".date('Y-m-d');
    	$sufix = substr($filebase, $dotpos).($this->compression === self::COMPRESSION_ZIP ? '.zip' : '');
    	$count_string = "";
    	$count = 0;
    	while (file_exists($prefix.$count_string.$sufix)) {
    		$count_string = "_".++$count;
    	}
        $returnValue = $prefix.$count_string.$sufix;
        // section 127-0-1-1-57e432c8:135336d8bea:-8000:0000000000004762 end

        return (string) $returnValue;
    }

} /* end of class common_log_ArchiveFileAppender */

?>
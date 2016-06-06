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
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Basic Appender that writes into a single file
 * If the file exceeds maxFileSize the part of file is truncated.
 * Size of part for truncate defines in reduceRatio property.
 * If ratio == 0 file will empty when size reaches max value.
 * When ratio >= 1 will be used default value equal 0.5.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 */
class common_log_SingleFileAppender extends common_log_BaseAppender
{
    /**
     * Name of file with log entries
     *
     * @access protected
     * @var string
     */
    protected $filename = '';

	/**
	 * Format for each log line
	 *
	 * %d datestring
	 * %m description(message)
	 * %s severity
	 * %b backtrace
	 * %r request
	 * %f file from which the log was called
	 * %l line from which the log was called
	 * %t timestamp
	 * %u user
	 *
	 * @access protected
	 * @var string
	 */
	protected $format = '%d [%s] \'%m\' %f %l';

	/**
	 * Prefix for each log line
	 *
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Maximum size of the logfile in bytes
	 *
	 * @access protected
	 * @var int
	 */
	protected $maxFileSize = 1048576;

	/**
	 * Ratio value that using for reducing logfile when size of it reach max value
	 *
	 * @var float
	 */
	protected $reduceRatio = 0.5;
    
    /**
     * File descriptor for R/W operations
     *
     * @access protected
     * @var resource
     */
    protected $filehandle = null;

    /**
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array $configuration
     * @return boolean
     */
    public function init($configuration)
    {
    	if (isset($configuration['file'])) {
    		$this->filename = $configuration['file'];
    	}
    	
    	if (isset($configuration['format'])) {
    		$this->format = $configuration['format'];
    	}
        
    	if (isset($configuration['prefix'])) {
    		$this->prefix = $configuration['prefix'];
    	}
    	
    	if (isset($configuration['max_file_size'])) {
    		$this->maxFileSize = $configuration['max_file_size'];
    	}

		if (isset($configuration['rotation-ratio'])
			&& abs($configuration['rotation-ratio']) < 1
		) {
			$this->reduceRatio = 1 - abs($configuration['rotation-ratio']);
		}

		return !empty($this->filename)
			? parent::init($configuration)
			: false;
    }

    /**
     * Initialises the logfile, and checks whenever the file require pruning
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initFile()
    {
        if ($this->maxFileSize > 0
			&& file_exists($this->filename)
			&& filesize($this->filename) >= $this->maxFileSize
		) {
        	// need to reduce the file size
        	$file = file($this->filename);
        	$file = array_splice($file, ceil(count($file) * $this->reduceRatio));
        	$this->filehandle = @fopen($this->filename, 'w');
        	foreach ($file as $line) {
        		@fwrite($this->filehandle, $line);
        	}
        } else {
    		$this->filehandle = @fopen($this->filename, 'a');
        }
    }

    /**
     * Prepares and saves log entries to file
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param common_log_Item $item
     * @return mixed
     */
    public function doLog(common_log_Item $item)
    {
    	if (is_null($this->filehandle)) {
    		$this->initFile();
    	}
    	
    	if ($this->filehandle !== false) {
	    	$map = array(
				'%d' => date('Y-m-d H:i:s',$item->getDateTime()),
				'%m' => $item->getDescription(),
				'%p' => $this->prefix,
				'%s' => $item->getSeverityDescriptionString(),
				'%t' => $item->getDateTime(),
				'%r' => $item->getRequest(),
				'%f' => $item->getCallerFile(),
				'%l' => $item->getCallerLine()
	    	);
	    	
	    	if (strpos($this->format, '%b')) {
	    		$map['%b'] = 'Backtrace not yet supported';
	    	}

			$str = strtr($this->format, $map) . PHP_EOL;

			@fwrite($this->filehandle, $str);
    	}
    }

    /**
     * Closes file descriptor when logger object was destroyed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
		if (!is_null($this->filehandle) && $this->filehandle !== false) {
    		@fclose($this->filehandle);
    	}
    }
}

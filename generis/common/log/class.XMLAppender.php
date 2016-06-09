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
 * A xml appender that stores the log events in an XML format
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class common_log_XMLAppender
    extends common_log_BaseAppender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filename
     *
     * @access public
     * @var string
     */
    public $filename = '';

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

        
    	if (isset($configuration['file'])) {
    		$this->filename = $configuration['file'];
    		$returnValue = parent::init($configuration);
    	} else {
    		$returnValue = false;
    	}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method doLog
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     */
    public function doLog( common_log_Item $item)
    {
        
    	$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$success = @$doc->load($this->filename);
		if (!$success) {
            $doc->loadXML('<events></events>');
        }

		$event_element = $doc->createElement("event");

		$message = $doc->createElement("description");
		$message->appendChild(
				$doc->createCDATASection($item->getDescription())
		);
		$event_element->appendChild($message);
		
		$file = $doc->createElement("file");
		$file->appendChild(
				$doc->createCDATASection($item->getCallerFile())
		);
		$event_element->appendChild($file);
		
		$line = $doc->createElement("line");
		$line->appendChild(
				$doc->createCDATASection($item->getCallerLine())
		);
		$event_element->appendChild($line);
		
		$datetime = $doc->createElement("datetime");
		$datetime->appendChild(
				$doc->createCDATASection($item->getDateTime())
		);
		$event_element->appendChild($datetime);
		
		$severity = $doc->createElement("severity");
		$severity->appendChild(
				$doc->createCDATASection($item->getSeverity())
		);
		$event_element->appendChild($severity);

		
		$doc->documentElement->appendChild($event_element);
		@$doc->save($this->filename);
        
    }

}
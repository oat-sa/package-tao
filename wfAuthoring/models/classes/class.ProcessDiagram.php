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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * TAO - wfAuthoring/models/classes/class.ProcessDiagram.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.10.2012, 12:08:57 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-includes begin
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-includes end

/* user defined constants */
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-constants begin
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-constants end

/**
 * Short description of class wfAuthoring_models_classes_ProcessDiagram
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage models_classes
 */
class wfAuthoring_models_classes_ProcessDiagram
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute arrow
     *
     * @access private
     * @var array
     */
    private $arrow = array();

    /**
     * Short description of attribute activity
     *
     * @access private
     * @var array
     */
    private $activity = array();

    /**
     * Short description of attribute connector
     *
     * @access private
     * @var array
     */
    private $connector = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003449 begin
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003449 end
    }

    /**
     * Short description of method addArrow
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource from
     * @param  Resource to
     * @param  string fromPort
     * @return mixed
     */
    public function addArrow( core_kernel_classes_Resource $from,  core_kernel_classes_Resource $to, $fromPort = "bottom")
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003454 begin
        $fromid = substr($from->getUri(), strpos($from->getUri(), "#")+1);
        $toid = substr($to->getUri(), strpos($to->getUri(), "#")+1);
        $this->arrow[] = array(
        		'from'	=> $fromid,
        		'to'	=> $toid,
        		'port'	=> $fromPort
        );
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003454 end
    }

    /**
     * Short description of method addActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  int xOffset
     * @param  int yOffset
     * @return mixed
     */
    public function addActivity( core_kernel_classes_Resource $activity, $xOffset, $yOffset)
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003456 begin
        $id = substr($activity->getUri(), strpos($activity->getUri(), "#")+1);
        $this->activity[$id] = array(
        		'x' => $xOffset,
        		'y' => $yOffset
        		);
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003456 end
    }

    /**
     * Short description of method addConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @param  int xOffset
     * @param  int yOffset
     * @return mixed
     */
    public function addConnector( core_kernel_classes_Resource $connector, $xOffset, $yOffset)
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003458 begin
    	$id = substr($connector->getUri(), strpos($connector->getUri(), "#")+1);
    	$this->connector[$id] = array(
    			'x' => $xOffset,
    			'y' => $yOffset
    	);
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003458 end
    }

    /**
     * Short description of method toJSON
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toJSON()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003464 begin
        $arrowData = array();
        $positionData = array();
        
        foreach ($this->arrow as $arrow) {
        	$type = null;
        	if (isset($this->activity[$arrow['from']])) {
        		$type = 'activity';
        	} elseif (isset($this->connector[$arrow['from']])) {
        		$type = 'connector';
        	} else {
        		throw new common_Exception('arrow starting from an inexistent activity or connector '.$arrow['from']);
        	}
        	$arrowData[] = array(
        			"id" => "connector_".$arrow['from']."_pos_".$arrow['port'],
        			"targetObject"=> $arrow['to'],
        			"type" => "top"
        	);
        }
        
        foreach ($this->activity as $id => $pos) {
        	$positionData[] = array(
        			'id' => $id,
        			'left' => $pos['x'],
        			'top' => $pos['y']
        	);
        }
        foreach ($this->connector as $id => $pos) {
        	$positionData[] = array(
        			'id' => $id,
        			'left' => $pos['x'],
        			'top' => $pos['y']
        	);
        }

        $returnValue = json_encode(
        		array(
        				"arrowData" => $arrowData,
        				"positionData" => $positionData
        		)
        );
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003464 end

        return (string) $returnValue;
    }

} /* end of class wfAuthoring_models_classes_ProcessDiagram */

?>
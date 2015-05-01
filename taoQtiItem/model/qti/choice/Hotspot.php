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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\choice;

use oat\taoQtiItem\model\qti\choice\Hotspot;
use oat\taoQtiItem\model\qti\choice\Choice;

/**
 * QTI Hotspot definition
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class Hotspot extends Choice
{

    public function setContent($content){
	$this->setAttribute('shape', $content);
    }

    public function getContent(){
	return $this->getAttibute('shape');
    }

    public function validateCoords($newAttributes = array()){

	$returnValue = true;

	$shape = '';
	if(isset($newAttributes['shape'])){
	    $shape = $newAttributes['shape'];
	}else{
	    $s = $this->getAttibute('shape');
	    if(!empty($s)){
		$shape = $s;
	    }
	}

	if(isset($newAttributes['coords'])){
	    $coords = $newAttributes['coords'];
	    if(is_array($coords)){
		//ok	
	    }elseif(is_string($coords)){
		$coords = explode($coords, ',');
	    }else{
		throw new InvalidArgumentException('the attribute "coords" is not in the right format');
	    }

	    if(empty($shape)){
		//validate coord format against shape type:
		switch($shape){
		    case 'rect':
			$returnValue = (count($coords) === 4);
			break;
		    case 'circle':
			$returnValue = (count($coords) === 3);
			break;
		    case 'ellipse':
			$returnValue = (count($coords) === 4);
			break;
		    case 'poly':
			$returnValue = (count($coords) % 2 === 0);
			break;
		    default:
			break;
		}
	    }
	}

	return $returnValue;
    }

}

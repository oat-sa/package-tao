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

use oat\taoQtiItem\model\qti\choice\ContainerChoice;
use oat\taoQtiItem\model\qti\choice\Choice;
use oat\taoQtiItem\model\qti\container\FlowContainer;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\container\ContainerStatic;

/**
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
abstract class ContainerChoice extends Choice implements FlowContainer
{

    protected $body = null;

    public function __construct($attributes = array(), Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->body = new ContainerStatic();
    }

    public function getBody(){
        return $this->body;
    }

    public function setContent($string){
        $this->getBody()->edit(strval($string));
    }

    public function getContent(){
        //@todo: manage nested qti objects..
        return $this->getBody()->getBody();
    }

}
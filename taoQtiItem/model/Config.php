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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 * 
 */

namespace oat\taoQtiItem\model;

/**
 * Interface defining required method for a controller's config
 *
 * @package taoQtiItem
 */
abstract Class Config
{

    protected $properties = array();
    protected $uiHooks = array();

    public function __construct(){
        
    }

    public function setProperty($key, $value){
        $this->properties[$key] = $value;
    }

    public function getProperty($key){
        if(isset($this->properties[$key])){
            return $this->properties[$key];
        }else{
            return null;
        }
    }

    public function getProperties(){
        return $this->properties;
    }

    public function addHook($hookFile){
        $this->uiHooks[] = $hookFile;
    }

    abstract public function toArray();

    abstract public function init();
}
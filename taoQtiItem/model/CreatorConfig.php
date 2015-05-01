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

use oat\taoQtiItem\model\Config;

/**
 * Interface defining required method for a plugin
 *
 * @package taoQtiItem
 */
Class CreatorConfig extends Config
{

    protected $interactions = array();
    protected $infoControls = array();

    public function addInteraction($interactionFile){
        $this->interactions[] = $interactionFile;
    }

    public function addInfoControl($infoControl){
        $this->infoControls[] = $infoControl;
    }

    public function toArray(){

        $interactions = array();
        foreach($this->interactions as $interaction){
            unset($interaction['directory']);
            $interactions[] = $interaction;
        }

        $infoControls = array();
        foreach($this->infoControls as $infoControl){
            unset($infoControl['directory']);
            $infoControls[] = $infoControl;
        }

        return array(
            'properties' => $this->properties,
            'uiHooks' => $this->uiHooks,
            'interactions' => $interactions,
            'infoControls' => $infoControls
        );
    }

    public function init(){

        foreach($this->interactions as $interaction){
            $this->prepare($interaction);
        }
        foreach($this->infoControls as $infoControl){
            $this->prepare($infoControl);
        }
    }

    protected function prepare($hook){

        if(isset($this->properties['uri'])){

            $item = new \core_kernel_classes_Resource($this->properties['uri']);

            //check for implementation in debugging state:
            if(isset($hook['debug']) && $hook['debug']){

                //add required resources:
                $registry = new $hook['registry'];
                $registry->addRequiredResources($hook['typeIdentifier'], $item);
                \common_Logger::d('added '.$hook['registry'].' '.$hook['typeIdentifier']);
            }
        }else{
            throw new \common_Exception('cannot prepare hook because of missing property in config : "uri" ');
        }
    }

}
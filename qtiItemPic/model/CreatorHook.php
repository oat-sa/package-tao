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

namespace oat\qtiItemPic\model;

use oat\taoQtiItem\model\Hook;
use oat\taoQtiItem\model\Config;
use oat\qtiItemPic\model\CreatorRegistry;

/**
 * The hook used in the item creator
 *
 * @package qtiItemPic
 */
class CreatorHook implements Hook
{

    /**
     * Affect the config
     * 
     * @param \oat\taoQtiItem\model\Config $config
     */
    public function init(Config $config){

        $registry = new CreatorRegistry();

        //get info controls directly located in views/js/pic/myInfoControl:
        $hooks = $registry->getDevImplementations();
        foreach($hooks as $hook){
            $config->addInfoControl($hook);
        }

        //finally add the info control manager "hook"
        $config->addHook('qtiItemPic/picManager/hook');
    }
    
}
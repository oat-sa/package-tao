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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoDevTools\actions;

use oat\taoQtiItem\model\qti\ImportService;
use oat\taoDevTools\helper\NameGenerator;
use oat\taoDevTools\helper\DataGenerator;

/**
 * The Main Module of tao development tools
 *
 * @package taoDevTools
 * @subpackage actions
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 *         
 */
class DataCreation extends \tao_actions_Main
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Work in progress
     */
    public function createItems()
    {
        $count = $this->hasRequestParameter('count') ? $this->getRequestParameter('count') : 100;
        
        DataGenerator::generateItems($count);

        echo 'created '.$count.' items';
    }
    
    public function createTesttakers()
    {
        $count = $this->hasRequestParameter('count') ? $this->getRequestParameter('count') : 1000;
        
        DataGenerator::generateTesttakers($count);
        echo 'created '.$count.' testakers';
    }
    
}
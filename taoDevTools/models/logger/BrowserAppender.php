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
 * Copyright (c) 2015 Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDevTools\models\logger;

use \ChromePhp;
use \common_log_BaseAppender;
use \common_log_Item;
use \common_Logger;

/**
 * Send log to the browser 
 * using either {@link https://craig.is/writing/chrome-logger} or firefox dev tools (from 43)
 *
 */
class BrowserAppender
    extends common_log_BaseAppender
{

    /**
     * wrap calls to the ChomePhp util
     *
     * @param  Item item
     */
    public function dolog( common_log_Item $item)
    {
        if(php_sapi_name() != 'cli'){
            switch($item->getSeverity()){
            case common_Logger::ERROR_LEVEL :
                ChromePhp::error($item->getDescription() . ' at ' . $item->getCallerFile() . ':' . $item->getCallerLine());
                break;
            case common_Logger::WARNING_LEVEL :
                ChromePhp::warn($item->getDescription());
                break;
            case common_Logger::INFO_LEVEL :
                ChromePhp::info($item->getDescription());
                break;
            default :
                ChromePhp::log($item->getDescription());
                break;
            }
        }
    }
}

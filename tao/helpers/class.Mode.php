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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Helps you to check in which mode the current TAO instance states: production, development.
 * The pupose is to wrap the use of DEBUG_MODE to replace this system in the future...
 * 
 * @todo Enable more than those 2 modes
 * 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 
 */
class tao_helpers_Mode {

    /**
     * Development mode
     */
    const DEVELOPMENT = 2;

    /**
     * Alias for development
     */
    const DEBUG = 2;

    /**
     *  Production mode
     */
    const PRODUCTION = 3;

    /**
     * @var int the current mode
     */
    private static $currentMode;
    
    /**
     * Check the TAO instance current mode
     * @example tao_helpers_Mode::is('production')
     * @param int|string $mode 
     * @return boolean
     */
    public static function is($mode){
       
        if(is_int($mode) && self::get() == $mode){
             
            return true;
        }
        if(is_string($mode) && self::get() == self::getModeByName($mode)){
            return true;
        }
        return false;
    }	
    
    /**
     * Get the current mode
     * @example (tao_helpers_Mode::get() == tao_helpers_Mode::DEVELOPMENT)
     * @return int matching the constants
     */
    public static function get(){
        if(empty(self::$currentMode)){
            self::$currentMode = self::getCurrentMode();
        }
        return self::$currentMode;
    }
    
    /**
     * Get the mode constant by name
     * @param string $name the mode name
     * @return int|boolean false if not exists
     */
    public static function getModeByName($name) {
        switch(strtolower($name)) {
            case 'development':
            case 'debug':
                return self::DEVELOPMENT;

            case 'production':
                return self::PRODUCTION;

            default:
                return false;
        }
    }
    
    /**
     * Reads the current value of DEBUG_MODE
     * 
     * @return int  matching the constants
     * @throws common_Exception
     */
    private static function getCurrentMode(){
        //read the mode from variable DEBUG_MODE
        if(!defined('DEBUG_MODE')){
            throw new common_Exception('The DEBUG MODE constant is not defined, it should never occurs');
        }
        
        if(DEBUG_MODE == true){
            return self::DEVELOPMENT;
        } 
        return self::PRODUCTION;
   }
} 
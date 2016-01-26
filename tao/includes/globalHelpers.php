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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

/**
 * Convenent function, helps you to the URI to access a framework action
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @param  string action the targeted action name
 * @param  string module the targeted module name
 * @param  string extension the targeted extension name
 * @param  array params an array of additionnal key/value query parameters
 * @return the URI
 */
function _url($action = null, $module = null, $extension = null, $params = array()){
    return tao_helpers_Uri::url($action, $module, $extension, $params);
}


/**
 * Conveniance function that calls tao_helpers_Display::htmlize
 *
 * @param  string $input The input string
 * @return string $output The htmlized string.
 */
function _dh($input){
    return tao_helpers_Display::htmlize($input);
}

/**
 * Convenience function clean the input string (replace all no alphanum chars).
 *
 * @param  string $input The input string.
 * @return string $output The output string without non alphanum characters.
 */
function _clean($input){
    return tao_helpers_Display::textCleaner($input);
}

/**
 * Experimental convenience function
 * @return boolean
 */
function _isRtl() {
    return tao_helpers_I18n::isLanguageRightToLeft(common_session_SessionManager::getSession()->getInterfaceLanguage());
}
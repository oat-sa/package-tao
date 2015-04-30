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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDevTools\helper;

use common_ext_ExtensionsManager;
use helpers_ExtensionHelper;
use helpers_TimeOutHelper;
use tao_scripts_TaoTranslate;

/**
 * Generate locales files
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class LocalesGenerator {
    
    private static $inputFormat = array(
        'min' => 1,
        'parameters' => array(
            array(
                'name' => 'verbose',
                'type' => 'boolean',
                'shortcut' => 'v',
                'description' => 'Verbose mode'
            ),
            array(
                'name' => 'action',
                'type' => 'string',
                'shortcut' => 'a',
                'description' => 'Action to undertake. Available actions are create, update, updateall, delete, deleteall, enable, disable, compile, compileall'
            ),
            array(
                'name' => 'language',
                'type' => 'string',
                'shortcut' => 'l',
                'description' => 'A language identifier like en-US, be-NL, fr, ...'
            ),
            array(
                'name' => 'output',
                'type' => 'string',
                'shortcut' => 'o',
                'description' => 'An output directory (PO and JS files)'
            ),
            array(
                'name' => 'input',
                'type' => 'string',
                'shortcut' => 'i',
                'description' => 'An input directory (source code)'
            ),
            array(
                'name' => 'build',
                'type' => 'boolean',
                'shortcut' => 'b',
                'description' => 'Sets if the language has to be built when created or not'
            ),
            array(
                'name' => 'force',
                'type' => 'boolean',
                'shortcut' => 'f',
                'description' => 'Force to erase an existing language if you use the create action'
            ),
            array(
                'name' => 'extension',
                'type' => 'string',
                'shortcut' => 'e',
                'description' => 'The TAO extension for which the script will apply'
            ),
            array(
                'name' => 'languageLabel',
                'type' => 'string',
                'shortcut' => 'll',
                'description' => 'Language label to use when creating a new language'
            ),
            array(
                'name' => 'targetLanguage',
                'type' => 'string',
                'shortcut' => 'tl',
                'description' => 'Target language code when you change the code of a locale'
            ),
            array(
                'name' => 'user',
                'type' => 'string',
                'shortcut' => 'u',
                'description' => 'TAO user (TaoManager Role)'
            ),
            array(
                'name' => 'password',
                'type' => 'string',
                'shortcut' => 'p',
                'description' => 'TAO password'
            )
        )
    );
    
    public function generateAll() {
        $exts = common_ext_ExtensionsManager::singleton()->getInstalledExtensions();
        foreach (common_ext_ExtensionsManager::singleton()->getAvailableExtensions() as $ext) {
            $exts[] = $ext;
        }
        
        $exts = helpers_ExtensionHelper::sortByDependencies($exts);
        
        $extIds = array();
        foreach ($exts as $ext) {
            if (file_exists($ext->getDir().'locales')) {
                $this->generateExtension($ext->getId());
            } else {
                // skip extension
            }
        }
    }
    
    public function generateExtension($extId) {
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::MEDIUM);
        $options = array(
            'argv' => array(
                "taoTranslate.php"
                ,"-e"
                ,$extId
                ,"-a"
                ,"updateAll"
            )
        );
        new tao_scripts_TaoTranslate(self::$inputFormat, $options);
        $options = array(
            'argv' => array(
                "taoTranslate.php"
                ,"-e"
                ,$extId
                ,"-a"
                ,"compileAll"
            )
        );
        new tao_scripts_TaoTranslate(self::$inputFormat, $options);
        helpers_TimeOutHelper::reset();
    }

}

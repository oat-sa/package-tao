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

namespace oat\taoDevTools\models;

/**
 * Creates a new extension
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class ExtensionCreator {
    
    private $id;
    
    private $label;
    
    private $version;
    
    private $author;
    
    private $authorNamespace;
    
    private $license;
    
    private $description;
    
    private $requires;
    
    private $options;

    public function __construct($id, $name, $version, $author, $namespace, $license, $description, $dependencies, $options) {
        $this->id = $id;
        $this->label = $name;
        $this->version = $version;
        $this->author = $author;
        $this->authorNamespace = $namespace;
        $this->license = $license;
        $this->description = $description;
        $this->requires = array();
        foreach ($dependencies as $extId) {
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
            $this->requires[$extId] = '>='.$ext->getVersion();
        }
        $this->options = $options;
    }
    
    private function validate() {
        // is root writable
        // does extension exist?
        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Extension can be created'));
    } 
    
    public function run() {
        try {
            $this->createDirectoryStructure();
            $this->writebaseFiles();
            $this->prepareLanguages();
            if (in_array('structure', $this->options)) {
                $this->addSampleStructure();
            }
            return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Extension %s created.', $this->label));
        } catch (Exception $e) {
            \common_Logger::w('Failed creating extension "'.$this->id.'": '.$e->getMessage());
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Unable to create extension %s, please consult log.', $this->label));
        }
    }
    
    protected function createDirectoryStructure() {
        $extDir = ROOT_PATH . $this->id. DIRECTORY_SEPARATOR;
        $dirs = array(
            $extDir.'locales',
            $extDir.'model'
        );
        
        foreach ($dirs as $dirPath) {
            if (!file_exists($dirPath) && !mkdir($dirPath, 0770, true)) {
                throw new \common_Exception('Could not create directory "'.$dirPath.'"');
            }
        }
        return $extDir;
    }
    
    protected function copyFile($file, $destination = null, $extra = array()) {
        $sample = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file);
        $destination = $this->getDestinationDirectory().(is_null($destination) ? $file : $destination);
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0770, true);
        }
        $map = array(
        	'{id}' => $this->id,
            '{name}' => self::escape($this->label),
            '{version}' => self::escape($this->version),
            '{author}' => self::escape($this->author),
            '{license}' => self::escape($this->license),
            '{description}' => self::escape($this->description),
            '{authorNs}' => $this->authorNamespace,
            '{dependencies}' => 'array(\''.implode('\',\'', array_keys($this->requires)).'\')',
            '{requires}' => \common_Utils::toPHPVariableString($this->requires),
            '{managementRole}' => GENERIS_NS.'#'.$this->id.'Manager',
            '{licenseBlock}' => $this->getLicense() 
        );
        $map = array_merge($map, $extra);
        $content = str_replace(array_keys($map), array_values($map), $sample);
        return file_put_contents($destination, $content);
    }
    
    protected function writeBaseFiles() {
        $this->copyFile('manifest.php');
        $this->copyFile('.htaccess');
        $this->copyFile('index.php');
    }
    
    protected function addSampleStructure() {
        $controllerName = ucfirst($this->id);
        $this->copyFile('actions'.DIRECTORY_SEPARATOR.'structures.xml', null, array('{classname}' => $controllerName));
        $this->copyFile('actions'.DIRECTORY_SEPARATOR.'extId.php', 'actions'.DIRECTORY_SEPARATOR.$controllerName.'.php', array('{classname}' => $controllerName));
        $this->copyFile(
            'views'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'extId'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'routes.js',
            'views'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'routes.js'
        );
        $this->copyFile(
            'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'sample.tpl'
        );
    }

    protected function prepareLanguages() {
        $options = array(
            'output_mode' => 'log_only',
            'argv' => array('placeholder', '-action=create','-extension='.$this->id, '-language=en-US')
        );
        new \tao_scripts_TaoTranslate(array(), $options);
        $options = array(
            'output_mode' => 'log_only',
            'argv' => array('placeholder', '-action=compile','-extension='.$this->id, '-language=en-US')
        );
        new \tao_scripts_TaoTranslate(array(), $options);
    }
    
    // UTILS
    
    protected function getDestinationDirectory() {
        return ROOT_PATH . $this->id. DIRECTORY_SEPARATOR;
    }
    
    protected function getLicense() {
        $licenseDirectory = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'licenses'.DIRECTORY_SEPARATOR;
        $candidate = $licenseDirectory.strtolower($this->license);
        if (file_exists($candidate)) {
            $content = file_get_contents($candidate);
        } else {
            $content = file_get_contents($licenseDirectory.'unknown');
        }
        return str_replace(
            array('{year}', '{author}', '{license}'),
            array(date("Y"), $this->author, $this->license),
            $content
        );
    }
    
    protected static function escape($value) {
        return str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value));
    }
}
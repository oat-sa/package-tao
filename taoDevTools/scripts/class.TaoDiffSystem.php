<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2); 2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER); 2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

/**
 * This Script class aims at providing tools to manage TAO extensions.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage scripts
 */
class taoDevTools_scripts_TaoDiffSystem extends tao_scripts_Runner
{

    /**
     * Instructions to execute to handle the action to perform.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function run()
    {
        // from install folder
        if (! file_exists($this->parameters['previous'])) {
            $this->err('Previous tao directory "' . $this->parameters['previous'] . '" found', true);
        }
        
        $outDir = $this->parameters['output'];
        if (! file_exists($outDir)) {
            if (! mkdir($outDir)) {
                $this->err('Could not create directory "' . $outDir, true);
            }
        }
        
        $oldExts = $this->getAllExtensionManifests($this->parameters['previous']);
        $newExts = $this->getAllExtensionManifests(ROOT_PATH);
        
        foreach ($oldExts as $extId => $manifest) {
            $extDiff = new taoDevTools_models_ExtDiff($manifest, isset($newExts[$extId]) ? $newExts[$extId] : null);
            file_put_contents($outDir . DIRECTORY_SEPARATOR . 'diff' . ucfirst($extId) . '.php', $extDiff->exportDiffToPhp());
            file_put_contents($outDir . DIRECTORY_SEPARATOR . 'diff' . ucfirst($extId) . '.sql', $extDiff->exportDiffToSql());
            if (! isset($newExts[$extId])) {
                $this->out('Extension ' . $extId . ' no longer exists.');
            }
        }
    }

    private function getAllExtensionManifests($directory)
    {
        $returnValue = array();
        $dir = new DirectoryIterator($directory);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDir() && ! $fileinfo->isDot() && substr($fileinfo->getBasename(), 0, 1) != '.') {
                $extId = $fileinfo->getBasename();
                $manifestPath = $fileinfo->getRealPath() . DIRECTORY_SEPARATOR . 'manifest.php';
                if (file_exists($manifestPath)) {
                    $manifest = new common_ext_Manifest($manifestPath);
                    if ($extId == $manifest->getName()) {
                        $returnValue[$extId] = $manifest;
                    } else {
                        throw new common_exception_InconsistentData('Manifest name "' . $manifest->getName() . '" does not match containing directory "' . $extId . '"');
                    }
                }
            }
        }
        return $returnValue;
    }

    /**
     * Create a new instance of the TaoExtensions script and executes it.
     * If the
     * inputFormat parameter is not provided, the script configures itself
     * to foster code reuse.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param
     *            array inputFormat
     * @param
     *            array options
     * @return mixed
     */
    public function __construct($inputFormat = array(), $options = array())
    {
        if (count($inputFormat) == 0) {
            // Autoconfigure the script.
            $inputFormat = array(
                'min' => 2,
                'parameters' => array(
                    array(
                        'name' => 'previous',
                        'type' => 'string',
                        'shortcut' => 'p',
                        'description' => 'Previous tao directorty'
                    ),
                    array(
                        'name' => 'output',
                        'type' => 'string',
                        'shortcut' => 'o',
                        'description' => 'Output folder'
                    )
                )
            );
        }
        
        parent::__construct($inputFormat, $options);
    }
}
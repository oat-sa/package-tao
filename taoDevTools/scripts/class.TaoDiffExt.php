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
class taoDevTools_scripts_TaoDiffExt extends tao_scripts_Runner
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
        if (file_exists($this->parameters['previous'])) {
            $oldManifest = new common_ext_Manifest($this->parameters['previous']);
        } else {
            $this->err('Manifest ' . $this->parameters['previous'] . ' not found', true);
        }
        if (file_exists($this->parameters['current'])) {
            $newManifest = new common_ext_Manifest($this->parameters['current']);
        } else {
            $this->err('Manifest ' . $this->parameters['current'] . ' not found', true);
        }
        
        $out = $this->parameters['output'];
        
        $diff = new taoDevTools_models_ExtDiff($oldManifest, $newManifest);
        file_put_contents($out, $diff->exportDiffToPhp());
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
                'min' => 3,
                'parameters' => array(
                    array(
                        'name' => 'previous',
                        'type' => 'string',
                        'shortcut' => 'p',
                        'description' => 'Previous extension manifest'
                    ),
                    array(
                        'name' => 'current',
                        'type' => 'string',
                        'shortcut' => 'c',
                        'description' => 'Current extension manifest'
                    ),
                    array(
                        'name' => 'output',
                        'type' => 'string',
                        'shortcut' => 'o',
                        'description' => 'Output file'
                    )
                )
            );
        }
        
        parent::__construct($inputFormat, $options);
    }
}
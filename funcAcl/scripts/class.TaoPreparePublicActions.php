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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * The taoPrepareActions script aims at updating the Extension model in the Ontology
 * depending on Action classes found in the extension at the file system level.
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class funcAcl_scripts_TaoPreparePublicActions
    extends tao_scripts_Runner
{

    public function preRun()
    {

    }

    /**
     * Main script logic.
     * 
     * * Recreate extension model.
     * * Grant access for the extension to the dedicated management role.
     *
     */
    public function run()
    {
        funcAcl_models_classes_Initialisation::run();
    }

    public function postRun()
    {

    }
}

?>
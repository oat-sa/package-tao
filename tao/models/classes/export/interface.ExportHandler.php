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
 * A class that handles everythign around the exportation of
 * several resources into a single file
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
interface tao_models_classes_export_ExportHandler
{

    /**
     * Returns a textual description of the export format
     * 
     * @return string
     */
    public function getLabel();
    
    /**
     * Returns a form in order to prepare the 
     * 
     * @param core_kernel_classes_Resource $resource the users selected resource or class
     * @return tao_helpers_form_Form
     */
    public function getExportForm(core_kernel_classes_Resource $resource);
    
    /**
     * Export the resources to a file stored in $destinations
     * 
     * @param array $formValues the values returned by the form provided by getForm
     * @param string $destPath path to export the resources to
     * @return common_report_Report report or string filepath
     * @throws common_exception_UserReadableException $exception
     */
    public function export($formValues, $destPath);
}

?>
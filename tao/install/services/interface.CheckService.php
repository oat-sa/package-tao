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
/*
 *  interface for checking service
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
interface tao_install_services_CheckService{
	/**
	 * Build a common_configuration_Component from a given set of input data.
	 * 
	 * @param tao_install_services_Data $data
	 * @return common_configuration_Component
	 */
	public static function buildComponent(tao_install_services_Data $data);
	
	/**
	 * Build the tao_install_services_Data result corresponding to the check
	 * performed by the service.
	 * 
	 * @param tao_install_services_Data $data
	 * @param common_configuration_Report $report
	 * @param common_configuration_Component $component
	 * @return tao_install_services_Data
	 */
	public static function buildResult(tao_install_services_Data $data,
									   common_configuration_Report $report,
									   common_configuration_Component $component);
}
?>
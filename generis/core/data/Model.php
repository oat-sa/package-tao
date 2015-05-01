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
 * Copyright (c) 2002-2008 (original work) 2014 Open Assessment Technologies SA
 * 
 */

namespace oat\generis\model\data;

/**
 * transitory class to manage the ontology driver
 * instead of managing full models, it only handles the rdfs interfaces
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
interface Model
{
    
	/**
	 * Creates a model from a configuration array provided by getConfig()
	 * 
	 * @param array $config
	 */
    function __construct($options = array());
    
	/**
	 * Returns a configuration array that can be used to instanciate the model
	 * should only contain scalars as values
	 * 
	 * @return array
	 */
	function getOptions();

	/**
	 * Experimental interface to access the data of the model
	 * Will throw an exception on all current implementations
	 * 
	 * @return RdfInterface
	 */
	function getRdfInterface();
	
	/**
	 * Expected interface to access the data of the model
	 * 
	 * @return RdfsInterface
	 */
	function getRdfsInterface();
	
}
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This abstract class represents a Data Binder that is able to bind data from a
 * source (e.g. a form) to another one (e.g. a persistent memory such as a
 *
 * Implementors have to implement the bind method to introduce their main logic
 * data binding.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
abstract class tao_models_classes_dataBinding_AbstractDataBinder
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Data binding from the data representation passed as a parameter and the
     * data source.
     *
     * If the DataBinding fails, a DataBindingException is thrown.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  array data An array of Data to bind from a data source to another.
     * @return mixed
     */
    public abstract function bind($data);

} /* end of abstract class tao_models_classes_dataBinding_AbstractDataBinder */

?>
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */


/**
 * Interface to implement by test models
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems

 */
interface taoTests_models_classes_TestModel
{

    /**
     * constructor called
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    public function __construct();

    /**
     * Called when the label of a test changes
     *
     * @param Resource $test
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test);

    /**
     * Prepare the content of the test,
     * using the provided items if possible
     *
     * @param core_kernel_classes_Resource $test
     * @param array $items an array of item resources
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array());

    /**
     * Delete the content of the test
     *
     * @param Resource $test
     */
    public function deleteContent( core_kernel_classes_Resource $test);

    /**
     * Returns all the items potenially used within the test
     *
     * @param Resource $test
     * @return array an array of item resources
     */
    public function getItems( core_kernel_classes_Resource $test);

    /**
     * returns the test authoring url
     *
     * @param core_kernel_classes_Resource $test the test instance
     * @return string the authoring url
     */
    public function getAuthoringUrl( core_kernel_classes_Resource $test);

    /**
     * Clones the content of one test to another test,
     * assumes that other test has already been cleaned (using deleteContent())
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Resource $source
     * @param core_kernel_classes_Resource $destination
     */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination);

    /**
     * Returns the compiler class of the test
     *
     * @return string
     */
    public function getCompilerClass();

	/**
	 * Return the Packable implementation for the given test model.
     * Packing is an alternative to Compilation. A Packer generates the
     * data needed to run a test where the compiler creates a stand alone
     * test.
	 *
	 * @return oat\taoTests\model\pack\Packable the packer class to instantiate
	 */
    public function getPackerClass();
}

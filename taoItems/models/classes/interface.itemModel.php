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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */


/**
 * Interface to implement by item models
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems

 */
interface taoItems_models_classes_itemModel
{

    /**
     * constructor called by itemService
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    public function __construct();

    /**
     * render used for deploy and preview
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string languageCode the langauge code the item should be rendered in
     * @return string the html representation of the item
     * @throws taoItems_models_classes_ItemModelException
     */
    public function render( core_kernel_classes_Resource $item, $languageCode);

    /**
     *
     * @param core_kernel_classes_Resource $item
     * @param unknown $languageCode
     */
    public function getPreviewUrl( core_kernel_classes_Resource $item, $languageCode);

    /**
     * Get the full url of the implementation authoring
     * @param core_kernel_classes_Resource $item
     * @return string the authoring URL
     */
    public function getAuthoringUrl( core_kernel_classes_Resource $item);

	/**
	 * Returns a compiler class for the item
	 *
	 * @return tao_models_classes_Compiler
	 */
	public function getCompilerClass();

	/**
	 * Return the Packable implementation for the given item model.
     * Packing is an alternative to Compilation. A Packer generates the
     * data needed to run an item where the compiler creates a stand alone
     * item.
	 *
	 * @return oat\taoItems\model\pack\ItemPacker the packer class to instantiate
	 */
    public function getPackerClass();
}

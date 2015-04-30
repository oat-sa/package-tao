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

namespace oat\taoQtiItem\model\qti\metadata\imsManifest;

use oat\taoQtiItem\model\qti\metadata\simple\SimpleMetadataValue;

/**
 * This implementation of MetadataValue represents MetadataValue objects in an IMS Manifest context.
 * 
 * To illustrate what an instance of a ImsManifestMetadataValue represents,
 * have a look at the IMS Manifest located at 'http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml'.
 * This manifest describes a single QTI Item with metadata.
 * 
 * As an example, the identifier metadata value 'qti_v2_item_01' can by represented by an implementation
 * of the MetadataValue interface returning the following information. Please note that the terms
 * "Path", "Resource Identifier", "Resource Type" and "Resource Hypertext Reference" in the example below are 
 * described in depth in the comments about the methods of this interface.
 * 
 * * A "Path" of 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom' -> 'http://www.imsglobal.org/xsd/imsmd_v1p2#general' -> 'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
 * * No particular "Language'
 * * 'choice' as its "Resource Identifier"
 * * 'imsqti_item_xmlv2p0' as its "Resource Type"
 * * 'choice.xml' as its "Resource Hypertext Reference"
 * 
 * Please see the description of the methods composing this interface for more information.
 * 
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class ImsManifestMetadataValue extends SimpleMetadataValue
{
    private $resourceType;
    private $resourceHref;

    /**
     * Create a new ImsManifestMetadataValue object.
     * 
     */
    public function __construct($resourceIdentifier, $resourceType, $resourceHref, $path, $value, $language = '')
    {
        parent::__construct($resourceIdentifier, $path, $value, $language);
        $this->setResourceType($resourceType);
        $this->setResourceHref($resourceHref);
    }
    
    /**
     * Returns an array of strings representing the path to a Metadata Value within
     * an IMS Manifest file.
     * 
     * As an example, the following array represents the path to the "imsmd:lom->general->identifier" metadata
     * value 'qti_v2_item_01' in the IMS Manifest example located at
     * http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml.
     * 
     * <code>
     *     array(
     *         'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
     *         'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
     *         'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
     *     )
     * </code>
     * 
     * 
     * Each entry of the array is a "Path Component" e.g. http://www.imsglobal.org/xsd/imsmd_v1p2#lom in the example
     * above. A Path Component is composed of 2 values separated by a # (sharp) character.
     * 
     * * The first value is called the "Base". It is the namespace URI of the related XML tag withing the IMS Manifest file.
     * * The second value is called the "Segment". It is the intrinsic XML tag name.
     * 
     * In the IMS Manifest example located at http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml,
     * the Path Component 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom' correspond to the <imsmd:lom> tag where 'imsmd' prefix
     * resolves the 'http://www.imsglobal.org/xsd/imsmd_v1p2' namespace.
     * 
     * 
     * @return array An array of strings representing the descriptive path to the metadata attribute.
     * @see http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml IMS Manifest example.
     */
    public function getPath()
    {
        return parent::getPath();
    }

    /**
     * Get the language, if specified, of the Metadata value. In the IMS Manifest example located at
     * http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml, the metadata value with
     * the given path
     * 
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#general'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#description'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
     * 
     * with an intrinsic value of 'This is a dummy item', has language 'en' (English), because of the use of the
     * existence of the xml:lang attribute. The fact that this attribute is set to the node makes the metadata value
     * to have a language. 
     * 
     * If a metadata value has no xml:lang defined, this method returns '' (empty string).
     * 
     * @return string The language of this metadata value or an empty string. 
     * @see http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml IMS Manifest example.
     */
    public function getLanguage()
    {
        return parent::getLanguage();
    }

    /**
     * Obtain the identifier of the <resource> this metadata value belongs to. As an example from
     * the IMS Manifest file located at http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml, 
     * the metadata value with the given path
     * 
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#general'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
     * 
     * belongs to resource with identifier 'choice', because its value is contained within the <resource>
     * node with identifer 'choice'.
     * 
     * @return string A resource identifier.
     * @see http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml IMS Manifest example.
     */
    public function getResourceIdentifier()
    {
        return parent::getResourceIdentifier();
    }

    /**
     * Obtain the type of the <resource> this metadata value belongs to. As an example from
     * the IMS Manifest file located at http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml, 
     * the metadata value with the given path
     * 
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#general'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
     * 
     * belongs to a resource with type 'imsqti_item_xmlv2p0', because its value is contained within a <resource>
     * node with a type attribute that has a value of 'imsqti_item_xmlv2p0'.
     * 
     * @return string A resource type.
     * @see http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml IMS Manifest example.
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Obtain the hypertext reference of the <resource> this metadata value belongs to. As an example from
     * the IMS Manifest file located at http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml,
     * the metadata value with the given path
     *
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#general'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
     *
     * belongs to a resource with href 'choice.xml', because its value is contained within a <resource>
     * node with a href attribute that has a value of 'choice.xml'.
     *
     * @return string An hypertext reference.
     * @see http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml IMS Manifest example.
     */
    public function getResourceHref()
    {
        return $this->resourceHref;
    }

    /**
     * Obtain the intrinsic value of the metadata. As an example from
     * the IMS Manifest file located at http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml,
     * the metadata value with the given path
     *
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#lom'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#general'
     * * 'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
     *
     * is 'qti_v2_item_01'.
     * 
     * If there is no actual value for this metadata (the node is empty), an empty string is returned.
     *
     * @return string A string
     * @see http://www.imsglobal.org/question/qti_v2p0/examples/mdexample/imsmanifest.xml IMS Manifest example.
     */
    public function getValue()
    {
        return parent::getValue();
    }
    
    /**
     * @param string $resourceHref
     */
    public function setResourceHref($resourceHref)
    {
        $this->resourceHref = $resourceHref;
    }
    
    /**
     * @param string $resourceType
     */
    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;
    }
}
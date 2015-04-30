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

use \InvalidArgumentException;

/**
 * Describes a Mapping between a given XML namespace, its prefix, and a XSD (XML Schema Definition) location.
 * 
 * As an example, in an IMS Manifest File, such a mapping could be performed to represent
 * how metadata from the IMS Metadata domain should be serialized from an XML perspective.
 * 
 * namespace: "http://www.imsglobal.org/xsd/imsmd_v1p2"
 * prefix: "imsmd"
 * schemaLocation: "http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd"
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 */
class ImsManifestMapping
{
    /**
     * An XML namespace.
     * 
     * @var string
     */
    private $namespace;
    
    /**
     * An XML prefix.
     * 
     * @var string
     */
    private $prefix;
    
    /**
     * An XSD (XML Schema Definition) schema location.
     * 
     * @var string
     */
    private $schemaLocation;
    
    /**
     * Create a new ImsManifestMapping object.
     * 
     * @param string $namespace An XML namespace.
     * @param string $prefix An XML prefix.
     * @param string $schemaLocation An XSD (XML Schema Definition) schema location.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($namespace, $prefix, $schemaLocation)
    {
        $this->setNamespace($namespace);
        $this->setPrefix($prefix);
        $this->setSchemaLocation($schemaLocation);
    }
    
    /**
     * Set the XML namespace of the mapping.
     * 
     * @param string $namespace An XML namespace e.g. "http://www.imsglobal.org/xsd/imsmd_v1p2p2".
     * @throws InvalidArgumentException If $namespace is not a string or an empty string.
     */
    public function setNamespace($namespace)
    {
        if (is_string($namespace) === false) {
            $msg = "The namespace argument must be a string.";
            throw new InvalidArgumentException($msg);
        } elseif ($namespace === '') {
            $msg = "The namespace argument must be a non-empty string.";
            throw new InvalidArgumentException($msg);
        } else {
            $this->namespace = $namespace;
        }
    }
    
    /**
     * Get the XML namespace.
     * 
     * @return string An XML namespace e.g. "http://www.imsglobal.org/xsd/imsmd_v1p2p2".
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    /**
     * Set the XML prefix of the mapping.
     * 
     * @param string $prefix An XML prefix e.g. "imsmd".
     * @throws InvalidArgumentException If $prefix is not a string or an empty string.
     */
    public function setPrefix($prefix)
    {
        if (is_string($prefix) === false) {
            $msg = "The prefix argument must be a string.";
            throw new InvalidArgumentException($msg);
        } elseif ($prefix === '') {
            $msg = "The prefix argument must be a non-empty string.";
            throw new InvalidArgumentException($msg);
        } else {
            $this->prefix = $prefix;
        }
    }
    
    /**
     * Get the XML prefix of the mapping.
     * 
     * @return string An XML prefix e.g. "imsmd".
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * Set the XSD (XML Schema Definition) schema location of the mapping.
     * 
     * @param string $schemaLocation A schema location e.g. "http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd".
     * @throws InvalidArgumentException If $schemaLocatuion is not a string or an empty string.
     */
    public function setSchemaLocation($schemaLocation)
    {
        if (is_string($schemaLocation) === false) {
            $msg = "The schemaLocation argument must be a string.";
            throw new InvalidArgumentException($msg);
        } elseif ($schemaLocation === '') {
            $msg = "The schemaLocation argument cannot be empty.";
            throw new InvalidArgumentException($msg);
        } else {
            $this->schemaLocation = $schemaLocation;
        }
    }
    
    /**
     * Get the XSD (XML Schema Definition) schema location of the mapping.
     * 
     * @return string A schema location e.g. "http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd".
     */
    public function getSchemaLocation()
    {
        return $this->schemaLocation;
    }
}
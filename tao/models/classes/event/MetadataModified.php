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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\tao\model\event;


use oat\oatbox\event\Event;

class MetadataModified implements Event
{

    /**
     *
     * Resource that have been modified/created
     *
     * @var \core_kernel_classes_Resource
     */
    private $resource;

    /**
     * Metadata uri that changed
     *
     * @var string
     */
    private $metadataUri;

    /**
     * Metadata value that changed
     *
     * @var string
     */
    private $metadataValue;

    /**
     * MetadataInjected constructor.
     * @param $item
     * @param string $metadataUri
     * @param string $metadataValue
     */
    public function __construct($resource, $metadataUri, $metadataValue)
    {
        $this->resource = $resource;
        $this->metadataUri = $metadataUri;
        $this->metadataValue = $metadataValue;
    }


    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * @return \core_kernel_classes_Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getMetadataUri()
    {
        return $this->metadataUri;
    }

    /**
     * @return string
     */
    public function getMetadataValue()
    {
        return $this->metadataValue;
    }




}
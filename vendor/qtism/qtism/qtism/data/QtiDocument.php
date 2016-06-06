<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data;

use qtism\data\storage\StorageException;

abstract class QtiDocument {

    /**
     *
     * @var string
     */
    private $version = '2.1';

    /**
     *
     * @var QtiComponent
     */
    private $documentComponent;

    /**
     *
     * @var string
     */
    private $url;

    public function __construct($version = '2.1', QtiComponent $documentComponent = null) {
        $this->setVersion($version);
        $this->setDocumentComponent($documentComponent);
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setDocumentComponent(QtiComponent $documentComponent = null) {
        $this->documentComponent = $documentComponent;
    }

    /**
     *
     * @return QtiComponent
     */
    public function getDocumentComponent() {
        return $this->documentComponent;
    }

    protected function setUrl($url) {
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    /**
     *
     * @param string $url
     * @throws StorageException
     */
    abstract public function load($url);

    /**
     *
     * @param string $url
     * @throws StorageException
     */
    abstract public function save($url);

    /**
     * Load the document content from a string.
     *
     * @param string $data
     * @return string
     * @throws StorageException
     */
    abstract public function loadFromString($data);

    /**
     * Save the document content as a string.
     *
     * @return string
     */
    abstract public function saveToString();
}

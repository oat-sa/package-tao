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

use qtism\runtime\pci\json\Marshaller;
use qtism\common\datatypes\File;

/**
 * This specialization of the QTISM JSON PCI Marshaller aims at marshalling
 * QTI file datatypes in a different manner than the original one.
 * 
 * Indeed, when a candidate comes back on a previously answered item,
 * we cannot afford to transfer large amounts of data to its client. This specialization
 * of the marshaller provides a placeholder for QTI file datatypes which actually does
 * not contain the data. The produced placeholders will have the following JSON structure:
 * 
 * { "base" : { "file" : { "mime" : "qti+application/octet-stream", "data" : "placeholder_data" } } }
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_PciJsonMarshaller extends Marshaller {
    
    /**
     * An arbitrary QTI File Datatype MIME type.
     * 
     * @var integer
     */
    const FILE_PLACEHOLDER_MIMETYPE = 'qti+application/octet-stream';
    
    /**
     * An arbitrary QTI File Content placeholder.
     * 
     * @var integer
     */
    const FILE_PLACEHOLDER_DATA = 'qti_file_datatype_placeholder_data';
    
    /**
     * Create a new PciJsonMarshaller object.
     * 
     */
    protected function construct() {
        parent::__construct();
    }
    
    /**
     * Marshall a file into a JSON placeholder.
     * 
     * @param File $file
     * @return array
     */
    protected function marshallFile(File $file) {
        return array('base' => array('file' => array('mime' => self::FILE_PLACEHOLDER_MIMETYPE, 'data' => base64_encode(self::FILE_PLACEHOLDER_DATA))));
    }
}
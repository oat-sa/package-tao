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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

use oat\taoQtiItem\helpers\QtiFile;

use qtism\data\storage\FileResolver;
use qtism\common\ResolutionException;

/**
 * The ItemResolver class implements the logic to resolve TAO Item URIs to
 * paths to the related QTI-XML files.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_helpers_ItemResolver extends FileResolver {
    
    /**
     * Resolve the given TAO Item URI in the path to
     * the related QTI-XML file.
     * 
     * @param string $url The URI of the TAO Item to resolve.
     * @return string The path to the related QTI-XML file.
	 * @throws ResolutionException If an error occurs during the resolution of $url.
     */
    public function resolve($url) {
        
        $taoItem = new core_kernel_classes_Resource($url);
        if ($taoItem->exists() === false) {
            $msg = "The QTI Item with URI '${url}' cannot be found.";
            throw new ResolutionException($msg);
        }
        
        // The item is retrieved from the database.
        // We can try to reach the QTI-XML file by detecting
        // where it is supposed to be located.
        
        // strip xinclude, we don't need that at the moment.
        $file = QtiFile::getQtiFilePath(new core_kernel_classes_Resource($url));
        $tmpfile = sys_get_temp_dir() . '/' . md5($url) . '.xml';
        $raw = file_get_contents($file);
        $raw = preg_replace("/<xi:include(?:.*)>/u", '', $raw);
        
        file_put_contents($tmpfile, $raw);
        
        return $tmpfile;
    }
    
}
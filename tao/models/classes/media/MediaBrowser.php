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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */
namespace oat\tao\model\media;

/**
 * Read interface to the media source
 */
interface MediaBrowser {

    /**
     * @param string $parentLink
     * @param array $acceptableMime
     * @param int $depth
     * @return array ['label' => $label,
     *                'path' => $implIdentifier.'/'.$path,
     *                'children' => [['label' => $label, 'path', $implIdentifier.'/'.$path, 'parent' => $parentPath]]
     *               ]
     */
    public function getDirectory($parentLink = '/', $acceptableMime = array(), $depth = 1);

    /**
     * @param string $link
     * @return array  ['name' => $filename,
     *                'mime' => $mimeType,
     *                'uri' => $uri,
     *                'filePath' => $filePath,
     *                'size' => $fileSize,
     *               ]
     * filePath : relative path to the file (to get a tree)
     * @throws \tao_models_classes_FileNotFoundException
     */
    public function getFileInfo($link);

    /**
     * @param string $link
     * @return string path of the file to download
     * @throws \tao_models_classes_FileNotFoundException
     */
    public function download($link);

} 
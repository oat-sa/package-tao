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
 *
 */

namespace oat\tao\model\media;


interface MediaBrowser {

    /**
     * @param string $parentLink
     * @param array $acceptableMime
     * @param int $depth
     * @return array ['label' => $label,
     *                'path' => $implIdentifier.'/'.$path,
     *                'children' => [['label' => $label, 'path', $implIdentifier.'/'.$path, 'url' => $continueUrl]]
     *               ]
     */
    public function getDirectory($parentLink = '/', $acceptableMime = array(), $depth = 1);

    /**
     * @param string $link
     * @return array  ['name' => $filename,
     *                'mime' => $mimeType,
     *                'size' => $fileSize,
     *               ]
     */
    public function getFileInfo($link);

    /**
     * @param string $link
     * @return string path of the file to download
     */
    public function download($link);

} 
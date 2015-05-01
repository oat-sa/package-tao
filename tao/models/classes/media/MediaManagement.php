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


interface MediaManagement {

    /**
     * @param string $source file to add
     * @param string $fileName new name of the file
     * @param string $parent the directory in which to upload / the class where to create the instance etc.
     * @return array of file info ['name' => $filename,
     *                              'mime' => $mimeType,
     *                              'size' => $fileSize
     *                             ]
     * @throws \common_exception_Error
     * @throws \tao_models_classes_FileNotFoundException
     */
    public function add($source, $fileName, $parent);


    /**
     * @param $filename
     * @return boolean the removal was successful
     */
    public function delete($filename);


} 
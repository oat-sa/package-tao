<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Filesource represents a directory structure
 * accessible by tao
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_FileSourceService
    extends tao_models_classes_GenerisService
{

    /**
     * Returns a filesource
     * 
     * @param string $uri
     * @return core_kernel_fileSystem_FileSystem
     */
    public function getFileSource($uri) {
        return new core_kernel_fileSystem_FileSystem($uri);
    }
    
    /**
     * Add a local Filesource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string label
     * @param  string path
     * @return core_kernel_versioning_Repository
     */
    public function addLocalSource($label, $path)
    {
        $returnValue = core_kernel_fileSystem_FileSystemFactory::createFileSystem(
			new core_kernel_classes_Resource(INSTANCE_GENERIS_VCS_TYPE_LOCAL),
			'', '', '', $path, $label, true
		);
        return $returnValue;
    }

    /**
     * delete a local Filesource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource fileSource
     * @return boolean
     */
    public function deleteFileSource( core_kernel_classes_Resource $fileSource)
    {
        throw new common_exception_NoImplementation('Delete of FileSource not implemented');
    }

}
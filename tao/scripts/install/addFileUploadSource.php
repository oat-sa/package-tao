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

/*
 * This post-installation script creates a new local file source for file uploaded
 * by end-users through the TAO GUI.
 */

$dataPath = FILES_PATH.'tao'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR;
if (file_exists($dataPath)) {
    helpers_File::emptyDirectory($dataPath);
}

$source = tao_models_classes_FileSourceService::singleton()->addLocalSource('fileUploadDirectory', $dataPath);
tao_models_classes_TaoService::singleton()->setUploadFileSource($source);

// add .htaccess to prevent php code execution
if(file_exists($dataPath) && is_dir($dataPath)){
        $accessFile = $dataPath . '.htaccess';
        if(!is_writable($dataPath) || (file_exists($accessFile && !is_writable($accessFile)))){
                        throw new tao_install_utils_Exception("Unable to write .htaccess file into : ${accessFile}.");
        }
        file_put_contents($accessFile, "php_flag engine off\n");
}


<?php
use oat\tao\model\websource\TokenWebSource;
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

$publicDataPath = FILES_PATH.'tao'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
$privateDataPath = FILES_PATH.'tao'.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR;

if (file_exists($publicDataPath)) {
    helpers_File::emptyDirectory($publicDataPath);
}
if (file_exists($privateDataPath)) {
    helpers_File::emptyDirectory($privateDataPath);
}

$publicFs = tao_models_classes_FileSourceService::singleton()->addLocalSource('public service storage', $publicDataPath);
$privateFs = tao_models_classes_FileSourceService::singleton()->addLocalSource('private service storage', $privateDataPath);

$websource = TokenWebSource::spawnWebsource($publicFs);

tao_models_classes_service_FileStorage::configure($privateFs, $publicFs, $websource);
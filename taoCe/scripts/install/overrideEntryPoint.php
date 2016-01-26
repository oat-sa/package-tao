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
use oat\tao\model\entryPoint\EntryPointService;
use oat\taoCe\model\entryPoint\TaoCeEntrypoint;
use oat\oatbox\service\ServiceManager;

$serviceManager = ServiceManager::getServiceManager();
$entryPointService = $serviceManager->get(EntryPointService::SERVICE_ID);

// replace delivery server
$entryPointService->overrideEntryPoint('backoffice', new TaoCeEntrypoint());

$serviceManager->register(EntryPointService::SERVICE_ID, $entryPointService);

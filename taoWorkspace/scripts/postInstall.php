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
 * 
 */
use oat\taoWorkspace\model\lockStrategy\LockSystem;
use oat\generis\model\data\ModelManager;
use oat\tao\model\lock\LockManager;
use oat\taoWorkspace\model\generis\WrapperModel;
use oat\taoWorkspace\model\lockStrategy\SqlStorage;
use oat\oatbox\service\ServiceManager;
use oat\taoRevision\model\Repository;
use oat\taoWorkspace\model\RevisionWrapper;

SqlStorage::createTable();

$code = 666;
$workspaceModel = new \core_kernel_persistence_smoothsql_SmoothModel(array(
    \core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'default',
    \core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => array($code),
    \core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => array($code),
    \core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => $code
));

$wrapedModel = WrapperModel::wrap(ModelManager::getModel(), $workspaceModel);
ModelManager::setModel($wrapedModel);

LockManager::setImplementation(new LockSystem());

$serviceManager = ServiceManager::getServiceManager();
$oldRepository = $serviceManager->get(Repository::SERVICE_ID);
$serviceManager->register('taoWorkspace/innerRevision', $oldRepository);

$newService = new RevisionWrapper(array(RevisionWrapper::OPTION_INNER_IMPLEMENTATION => 'taoWorkspace/innerRevision'));
$serviceManager->register(Repository::SERVICE_ID, $newService);

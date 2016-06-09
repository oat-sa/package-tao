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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
use oat\oatbox\service\ServiceManager;
use oat\oatbox\action\Action;
/**
 * Run the extension updater 
 *
 * @access public
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_ext_UpdateExtensions implements Action
{
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\action\Action::__invoke()
     */
    public function __invoke($params)
    {
        
        $merged = array_merge(
            common_ext_ExtensionsManager::singleton()->getInstalledExtensions(),
            $this->getMissingExtensions()
        );
        
        $sorted = \helpers_ExtensionHelper::sortByDependencies($merged);
        
        $report = new common_report_Report(common_report_Report::TYPE_INFO, 'Running extension update');
        foreach ($sorted as $ext) {
            try {
                if(!common_ext_ExtensionsManager::singleton()->isInstalled($ext->getId())) {
                    $installer = new \tao_install_ExtensionInstaller($ext);
                    $installer->install();
                    $report->add(new common_report_Report(common_report_Report::TYPE_SUCCESS, 'Installed '.$ext->getName()));
                } else {
                    $report->add($this->updateExtension($ext));
                }
            } catch (common_ext_MissingExtensionException $ex) {
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, $ex->getMessage()));
                break;
            } catch (common_ext_OutdatedVersionException $ex) {
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, $ex->getMessage()));
                break;
            } catch (Exception $e) {
                $report->setType(common_report_Report::TYPE_ERROR);
                $report->setTitle('Update failed');
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, 'Exception during update of '.$ext->getId().'.'));
                break;
            }
        }
        $this->logReport($report);
        return $report;
    }
    
    /**
     * Update a specific extension
     * 
     * @param common_ext_Extension $ext
     * @return common_report_Report
     */
    protected function updateExtension(common_ext_Extension $ext)
    {
        helpers_ExtensionHelper::checkRequiredExtensions($ext);
        $installed = common_ext_ExtensionsManager::singleton()->getInstalledVersion($ext->getId());
        $codeVersion = $ext->getVersion();
        if ($installed !== $codeVersion) {
            $report = new common_report_Report(common_report_Report::TYPE_INFO, $ext->getName().' requires update from '.$installed.' to '.$codeVersion);
            $updaterClass = $ext->getManifest()->getUpdateHandler();
            if (is_null($updaterClass)) {
                $report = new common_report_Report(common_report_Report::TYPE_WARNING, 'No Updater found for  '.$ext->getName());
            } elseif (!class_exists($updaterClass)) {
                $report = new common_report_Report(common_report_Report::TYPE_ERROR, 'Updater '.$updaterClass.' not found');
            } else {
                $updater = new $updaterClass($ext);
                $returnedVersion = $updater->update($installed);
                $currentVersion = common_ext_ExtensionsManager::singleton()->getInstalledVersion($ext->getId());
                
                if (!is_null($returnedVersion) && $returnedVersion != $currentVersion) {
                    common_ext_ExtensionsManager::singleton()->updateVersion($ext, $returnedVersion);
                    $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, 'Manually saved extension version'));
                    $currentVersion = $returnedVersion;
                }
                
                if ($currentVersion == $codeVersion) {
                    $report->add(new common_report_Report(common_report_Report::TYPE_SUCCESS, 'Successfully updated '.$ext->getName().' to '.$currentVersion));
                } else {
                    $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, 'Update of '.$ext->getName().' exited with version '.$currentVersion));
                }
                common_cache_FileCache::singleton()->purge();
            }
        } else {
            $report = new common_report_Report(common_report_Report::TYPE_INFO, $ext->getName().' already up to data');
        }
        return $report;
    }
    
    protected function getMissingExtensions()
    {
        $missingId = \helpers_ExtensionHelper::getMissingExtensionIds(common_ext_ExtensionsManager::singleton()->getInstalledExtensions());
        
        $missingExt = array();
        foreach ($missingId as $extId) {
            $ext= \common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
            $missingExt[$extId] = $ext;
        }
        return $missingExt;
    }
    
    protected function logReport(common_report_Report $report)
    {
        $folder = FILES_PATH.'updates'.DIRECTORY_SEPARATOR;
        $updateId = time();
        while (file_exists($folder.$updateId.'.log')) {
            $count = isset($count) ? $count + 1 : 0;
            $updateId = time().'_'.$count;
        }
        file_put_contents($folder.$updateId.'.log', helpers_Report::renderToCommandline($report, false));
    }
}

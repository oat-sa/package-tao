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
namespace oat\oatbox\install;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\SimpleConfigDriver;
use common_report_Report as Report;
 /**
 * A service to install oatbox functionality
 * 
 * Sets up:
 *   configuration
 *   filesystems
 */
class Installer extends ConfigurableService
{
    /**
     * run the install
     */
    public function install()
    {
        $this->validateOptions();
        
        $configPath = $this->getOption('root_path').'config/';
        $serviceManager = $this->setupServiceManager($configPath);
        
        // setup filesystem service
        $serviceManager->register(FileSystemService::SERVICE_ID,new FileSystemService(array(FileSystemService::OPTION_FILE_PATH => $this->getOption('file_path'))));
        
        return new Report(Report::TYPE_SUCCESS, 'Oatbox installed successfully');
    }
    
    protected function setupServiceManager($configPath)
    {
        if (!\helpers_File::emptyDirectory($configPath, true)) {
            throw new common_exception_Error('Unable to empty ' . $configPath . ' folder.');
        }
        
        $driver = new SimpleConfigDriver();
        $configService = $driver->connect('config', array(
            'dir' => $configPath,
            'humanReadable' => true
        ));
        
        return new ServiceManager($configService);
    }
    
    protected function validateOptions()
    {
        if (!$this->hasOption('root_path') || empty($this->getOption('root_path'))) {
            throw new \common_exception_MissingParameter('root_path', __CLASS__);
        }
        if (!$this->hasOption('file_path') || empty($this->getOption('file_path'))) {
            throw new \common_exception_MissingParameter('file_path', __CLASS__);
        }
        
    }
    
    
}

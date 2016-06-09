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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiItem\scripts\update;

use oat\taoQtiItem\install\scripts\addValidationSettings;
use oat\taoQtiItem\install\scripts\createExportDirectory;
use oat\taoQtiItem\model\flyExporter\extractor\OntologyExtractor;
use oat\taoQtiItem\model\flyExporter\extractor\QtiExtractor;
use oat\taoQtiItem\model\flyExporter\simpleExporter\ItemExporter;
use oat\taoQtiItem\model\flyExporter\simpleExporter\SimpleExporter;
use oat\taoQtiItem\model\SharedLibrariesRegistry;
use oat\tao\model\ThemeRegistry;
use oat\tao\model\websource\TokenWebSource;
use oat\tao\model\ClientLibRegistry;
use oat\taoQtiItem\model\update\ItemUpdateInlineFeedback;

/**
 * 
 * @author Sam <sam@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater
{

    /**
     * 
     * @param string $initialVersion
     * @return string
     */
    public function update($initialVersion){

        $currentVersion = $initialVersion;

        //add portable shared libraries:
        $libBasePath = ROOT_PATH.'taoQtiItem/views/js/portableSharedLibraries';
        $libRootUrl = ROOT_URL.'taoQtiItem/views/js/portableSharedLibraries';
        $installBasePath = ROOT_PATH.'taoQtiItem/install/scripts/portableSharedLibraries';
        $registry = new SharedLibrariesRegistry($libBasePath, $libRootUrl);

        //migrate from 2.6 to 2.7.0
        if($currentVersion == '2.6'){

            $registry->registerFromFile('IMSGlobal/jquery_2_1_1', $installBasePath.'/IMSGlobal/jquery_2_1_1.js');
            $registry->registerFromFile('OAT/lodash', $installBasePath.'/OAT/lodash.js');
            $registry->registerFromFile('OAT/async', $installBasePath.'/OAT/async.js');
            $registry->registerFromFile('OAT/raphael', $installBasePath.'/OAT/raphael.js');
            $registry->registerFromFile('OAT/scale.raphael', $installBasePath.'/OAT/scale.raphael.js');
            $registry->registerFromFile('OAT/util/xml', $installBasePath.'/OAT/util/xml.js');
            $registry->registerFromFile('OAT/util/math', $installBasePath.'/OAT/util/math.js');
            $registry->registerFromFile('OAT/util/html', $installBasePath.'/OAT/util/html.js');
            $registry->registerFromFile('OAT/util/EventMgr', $installBasePath.'/OAT/util/EventMgr.js');
            $registry->registerFromFile('OAT/util/event', $installBasePath.'/OAT/util/event.js');

            $currentVersion = '2.7.0';
        }

        //migrate from 2.7.0 to 2.7.1
        if($currentVersion == '2.7.0'){

            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');
            $registry->registerFromFile('OAT/interact', $installBasePath . '/OAT/interact.js');
            $registry->registerFromFile('OAT/interact-rotate', $installBasePath . '/OAT/interact-rotate.js');

            $currentVersion = '2.7.1';
        }

        //migrate from 2.7.0 to 2.7.1
        if($currentVersion == '2.7.1'){

            $registry->registerFromFile('OAT/sts/transform-helper', $installBasePath . '/OAT/sts/transform-helper.js');

            $currentVersion = '2.7.2';
        }

        //migrate from 2.7.2 to 2.7.3
        if($currentVersion == '2.7.2'){

            $registry->registerFromFile('IMSGlobal/jquery_2_1_1', $installBasePath.'/IMSGlobal/jquery_2_1_1.js');
            $registry->registerFromFile('OAT/lodash', $installBasePath.'/OAT/lodash.js');
            $registry->registerFromFile('OAT/async', $installBasePath.'/OAT/async.js');
            $registry->registerFromFile('OAT/raphael', $installBasePath.'/OAT/raphael.js');
            $registry->registerFromFile('OAT/scale.raphael', $installBasePath.'/OAT/scale.raphael.js');
            $registry->registerFromFile('OAT/util/xml', $installBasePath.'/OAT/util/xml.js');
            $registry->registerFromFile('OAT/util/math', $installBasePath.'/OAT/util/math.js');
            $registry->registerFromFile('OAT/util/html', $installBasePath.'/OAT/util/html.js');
            $registry->registerFromFile('OAT/util/EventMgr', $installBasePath.'/OAT/util/EventMgr.js');
            $registry->registerFromFile('OAT/util/event', $installBasePath.'/OAT/util/event.js');
            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');
            $registry->registerFromFile('OAT/interact', $installBasePath . '/OAT/interact.js');
            $registry->registerFromFile('OAT/interact-rotate', $installBasePath . '/OAT/interact-rotate.js');
            $registry->registerFromFile('OAT/sts/transform-helper', $installBasePath . '/OAT/sts/transform-helper.js');

            $currentVersion = '2.7.3';
        }

        //migrate from 2.7.3 to 2.7.4
        if($currentVersion == '2.7.3'){
            $registry->registerFromFile('OAT/handlebars', $installBasePath.'/OAT/handlebars.js');
            $currentVersion = '2.7.4';
        }

        if($currentVersion == '2.7.4'){
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $ext->setConfig('qtiCreator', array('multi-column' => false));
            $currentVersion = '2.7.5';
        }
        
        if($currentVersion == '2.7.5'){

            $registry->registerFromFile('OAT/sts/stsEventManager', $installBasePath . '/OAT/sts/stsEventManager.js');

            $currentVersion = '2.7.6';
        }
        
        if($currentVersion == '2.7.6'){

            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');

            $currentVersion = '2.7.7';
        }
        
        if($currentVersion == '2.7.7'){

            $itemThemesDataPath = FILES_PATH.'tao'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;
            $itemThemesDataPathFs = \tao_models_classes_FileSourceService::singleton()->addLocalSource('Theme FileSource', $itemThemesDataPath);

            $websource = TokenWebSource::spawnWebsource($itemThemesDataPathFs);
            ThemeRegistry::getRegistry()->setWebSource($websource->getId());

            ThemeRegistry::getRegistry()->createTarget('items', 'taoQtiItem/views/css/qti-runner.css');
            ThemeRegistry::getRegistry()->registerTheme('tao', 'TAO', 'taoQtiItem/views/css/themes/default.css', array('items'));
            ThemeRegistry::getRegistry()->setDefaultTheme('items', 'tao');

        	$currentVersion = '2.7.8';
        }

		if($currentVersion == '2.7.8'){

            $clientLibRegistry = ClientLibRegistry::getRegistry();
            $clientLibRegistry->register('qtiCustomInteractionContext', '../../../taoQtiItem/views/js/runtime/qtiCustomInteractionContext');
            $clientLibRegistry->register('qtiInfoControlContext', '../../../taoQtiItem/views/js/runtime/qtiInfoControlContext');

            $currentVersion = '2.7.9';
        }
        if($currentVersion == '2.7.9'){
             $currentVersion = '2.8.0';
        }
        
        if($currentVersion == '2.8.0'){
            $currentVersion = '2.8.1';
            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');
        }
        
        if ($currentVersion == '2.8.1') {
            $qtiItem = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $qtiItem->setConfig('userScripts', array());
            $currentVersion = '2.9.0';
        }

        if ($currentVersion === '2.9.0') {
            $registry->registerFromFile('OAT/waitForMedia', $installBasePath . '/OAT/waitForMedia.js');
            $currentVersion = '2.9.1';
        }
        if ($currentVersion === '2.9.1') {
            $currentVersion = '2.10.0';
        }
        if($currentVersion === '2.10.0') {
            $currentVersion = '2.11.0';
        }
        if($currentVersion === '2.11.0') {
            $registry->registerFromFile('OAT/util/asset', $installBasePath . '/OAT/util/asset.js');
            $registry->registerFromFile('OAT/util/tpl', $installBasePath . '/OAT/util/tpl.js');
            $currentVersion = '2.12.0';
        }

        $this->setVersion($currentVersion);

        if($this->isBetween('2.12.0','2.13.0')) {
            $itemQtiExt = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $compilerClassConfig = 'oat\taoQtiItem\model\QtiItemCompiler';

            $itemQtiExt->setConfig('compilerClass', $compilerClassConfig);
            $this->setVersion('2.13.0');
        }

	    if($this->isVersion('2.13.0')) {
            
            \oat\tao\model\ClientLibConfigRegistry::getRegistry()->register(
                'taoQtiItem/qtiRunner/core/QtiRunner',
                array(
                    'inlineModalFeedback' => false
                )
            );

            $fs = \taoItems_models_classes_ItemsService::singleton()->getDefaultFileSource();
            $itemUpdater = new ItemUpdateInlineFeedback($fs->getPath());
            $itemUpdater->update(true);
        
            $this->setVersion('2.14.0');
        }

		$this->skip('2.14.0','2.15.1');

        if($this->isVersion('2.15.1')){
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $validation = array(
                'default' => array(
                    __DIR__.'/../../model/qti/data/imscp_v1p1.xsd',
                    __DIR__.'/../../model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_imscpv1p2_v1p0.xsd'
                )
            );
            $ext->setConfig('manifestValidation', $validation);
            $this->setVersion('2.16.0');
        }

        if($this->isVersion('2.16.0')){
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $validation = array(
                'http://www.imsglobal.org/xsd/imsqti_v2p0' => array(
                    __DIR__.'/../../model/qti/data/qtiv2p0/imsqti_v2p0.xsd'
                ),
                'http://www.imsglobal.org/xsd/apip/apipv1p0/qtiitem/imsqti_v2p1' => array(
                    __DIR__.'/../../model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_qtiitemv2p1_v1p0.xsd'
                ),
                'default' => array(
                    __DIR__.'/../../model/qti/data/qtiv2p1/imsqti_v2p1.xsd',
                )
            );
            $ext->setConfig('contentValidation', $validation);
            $this->setVersion('2.17.0');
        }

		if($this->isVersion('2.17.0')){
			$this->setVersion('2.17.1');
		}

        if($this->isVersion('2.17.1')){
            $service = new addValidationSettings();
            $service([]);
            $this->setVersion('2.17.2');
        }

		$this->skip('2.17.2', '2.19.0');

        if ($this->isVersion('2.19.0')) {

            if (!$this->getServiceManager()->has(SimpleExporter::SERVICE_ID)) {
                $service = new ItemExporter(array(
                    'fileSystem' => 'taoQtiItem',
                    'fileLocation' => 'export' . DIRECTORY_SEPARATOR . 'export.csv',
                    'extractors' => array (
                        'OntologyExtractor' => new OntologyExtractor(),
                        'QtiExtractor' => new QtiExtractor()
                    ),
                    'columns' => array (
                        'label' => array (
                            'extractor' => 'OntologyExtractor',
                            'parameters' => array (
                                'property' => RDFS_LABEL
                            )
                        ),
                        'type' => array (
                            'extractor' => 'QtiExtractor',
                            'parameters' => array (
                                'callback' => 'getInteractionType'
                            )
                        ),
                        'nb choice' => array (
                            'extractor' => 'QtiExtractor',
                            'parameters' => array (
                                'callback' => 'getNumberOfChoices'
                            )
                        ),
                        'BR' => array (
                            'extractor' => 'QtiExtractor',
                            'parameters' => array (
                                'callback' => 'getRightAnswer',
                                'callbackParameters' => array(
                                    'delimiter' => '|'
                                )
                            )
                        ),
                        'choiceInteraction' => array (
                            'extractor' => 'QtiExtractor',
                            'parameters' => array (
                                'callback' => 'getChoices',
                                'valuesAsColumns' => true,
                            )
                        ),
                    )
                ));
                $service->setServiceManager($this->getServiceManager());
                $this->getServiceManager()->register(SimpleExporter::SERVICE_ID, $service);

                $createExportDirectoryScript = new createExportDirectory();
                $createExportDirectoryScript([]);
            }
            
            $this->setVersion('2.20.0');
        }

        $this->skip('2.20.0', '2.21.1');
    }

}

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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;
use qtism\data\storage\xml\XmlStorageException;
use qtism\runtime\rendering\markup\MarkupPostRenderer;
use qtism\runtime\rendering\css\CssScoper;
use qtism\data\storage\php\PhpDocument;
use qtism\data\QtiComponentIterator;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlCompactDocument;
use qtism\data\AssessmentTest;
use qtism\data\content\RubricBlockRef;
use qtism\data\content\RubricBlock;
use qtism\data\content\Stylesheet;
use qtism\data\content\StylesheetCollection;
use qtism\data\state\OutcomeDeclaration;
use qtism\data\state\DefaultValue;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\utils\Url;

/**
 * A Test Compiler implementation that compiles a QTI Test and related QTI Items.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiTest
 
 */
class taoQtiTest_models_classes_QtiTestCompiler extends taoTests_models_classes_TestCompiler
{
    
    /**
     * The list of mime types of files that are accepted to be put
     * into the public compilation directory.
     * 
     * @var array
     */
    private static $publicMimeTypes = array('text/css',
                                               'image/png', 
                                               'image/jpeg', 
                                               'image/gif', 
                                               'text/html',
                                               'application/x-shockwave-flash',
                                               'video/x-flv',
                                               'image/bmp',
                                               'image/svg+xml',
                                               'audio/mpeg',
                                               'audio/ogg',
                                               'video/quicktime',
                                               'video/webm',
                                               'video/ogg',
                                               'application/pdf',
                                               'application/x-font-woff',
                                               'application/vnd.ms-fontobject',
                                               'application/x-font-ttf',
                                               'image/svg+xml',
                                               'image/svg+xml');
   
    /**
     * The public compilation directory.
     * 
     * @var tao_models_classes_service_StorageDirectory
     */
    private $publicDirectory = null;
    
    /**
     * The private compilation directory.
     * 
     * @var tao_models_classes_service_StorageDirectory
     */
    private $privateDirectory = null;
    
    /**
     * The rendering engine that will be used to create rubric block templates.
     * 
     * @var XhtmlRenderingEngine
     */
    private $renderingEngine = null;
    
    /**
     * The Post renderer to be used in template oriented rendering.
     * 
     * @var MarkupPostRenderer
     */
    private $markupPostRenderer = null;
    
    /**
     * The CSS Scoper will scope CSS files to their related rubric block.
     * 
     * @var CssScoper
     */
    private $cssScoper = null;
    
    /**
     * An additional path to be used when test definitions are located in sub-directories.
     *
     * @var string
     */
    private $extraPath;
    
    /**
     * Get the public compilation directory.
     * 
     * @return tao_models_classes_service_StorageDirectory
     */
    protected function getPublicDirectory() {
        return $this->publicDirectory;
    }
    
    /**
     * Set the public compilation directory.
     * 
     * @param tao_models_classes_service_StorageDirectory $directory
     */
    protected function setPublicDirectory(tao_models_classes_service_StorageDirectory $directory) {
        $this->publicDirectory = $directory;
    }
    
    /**
     * Get the private compilation directory.
     * 
     * @return tao_models_classes_service_StorageDirectory
     */
    protected function getPrivateDirectory() {
        return $this->privateDirectory;
    }
    
    /**
     * Set the private compilation directory.
     * 
     * @param tao_models_classes_service_StorageDirectory $directory
     */
    protected function setPrivateDirectory(tao_models_classes_service_StorageDirectory $directory) {
        $this->privateDirectory = $directory;
    }
    
    /**
     * Get the rendering engine that will be used to render rubric block templates.
     * 
     * @return XhtmlRenderingEngine
     */
    protected function getRenderingEngine() {
        return $this->renderingEngine;
    }
    
    /**
     * Set the rendering engine that will be used to render rubric block templates.
     * 
     * @param XhtmlRenderingEngine $renderingEngine
     */
    protected function setRenderingEngine(XhtmlRenderingEngine $renderingEngine) {
        $this->renderingEngine = $renderingEngine;
    }
    
    /**
     * Get the markup post renderer to be used after template oriented rendering.
     * 
     * @return MarkupPostRenderer
     */
    protected function getMarkupPostRenderer() {
        return $this->markupPostRenderer;
    }
    
    /**
     * Set the markup post renderer to be used after template oriented rendering.
     * 
     * @param MarkupPostRenderer $markupPostRenderer
     */
    protected function setMarkupPostRenderer(MarkupPostRenderer $markupPostRenderer) {
        $this->markupPostRenderer = $markupPostRenderer;
    }
    
    /**
     * Get the CSS Scoper tool that will scope CSS files to their related rubric block.
     * 
     * @return CssScoper
     */
    protected function getCssScoper() {
        return $this->cssScoper;
    }
    
    /**
     * Set the CSS Scoper tool that will scope CSS files to their related rubric block.
     * 
     * @param CssScoper $cssScoper
     */
    protected function setCssScoper(CssScoper $cssScoper) {
        $this->cssScoper = $cssScoper;
    }
    
    /**
     * Get the extra path to be used when test definition is located
     * in sub-directories.
     * 
     * @return string
     */
    protected function getExtraPath() {
        return $this->extraPath;
    }
    
    /**
     * Set the extra path to be used when test definition is lovated in sub-directories.
     * 
     * @param string $extraPath
     */
    protected function setExtraPath($extraPath) {
        $this->extraPath = $extraPath;
    }
    
    /**
     * Initialize the compilation by:
     * 
     * * 1. Spawning public and private compilation directoryies.
     * * 2. Instantiating appropriate rendering engine and CSS utilities.
     * 
     * for the next compilation process.
     */
    protected function initCompilation() {
        $ds = DIRECTORY_SEPARATOR;
        
        // Initialize public and private compilation directories.
        $this->setPrivateDirectory($this->spawnPrivateDirectory());
        $this->setPublicDirectory($this->spawnPublicDirectory());
        
        // Extra path.
        $testService = taoQtiTest_models_classes_QtiTestService::singleton();
        $testContentPath = $testService->getDocPath($this->getResource());
        $testDataPath = $testService->getTestContent($this->getResource())->getAbsolutePath();
        $this->setExtraPath(str_replace(array($testDataPath, TAOQTITEST_FILENAME), '', $testContentPath));
        
        // Initialize rendering engine.
        $renderingEngine = new XhtmlRenderingEngine();
        $renderingEngine->setStylesheetPolicy(XhtmlRenderingEngine::STYLESHEET_SEPARATE);
        $renderingEngine->setXmlBasePolicy(XhtmlRenderingEngine::XMLBASE_PROCESS);
        $renderingEngine->setFeedbackShowHidePolicy(XhtmlRenderingEngine::TEMPLATE_ORIENTED);
        $renderingEngine->setViewPolicy(XhtmlRenderingEngine::TEMPLATE_ORIENTED);
        $renderingEngine->setPrintedVariablePolicy(XhtmlRenderingEngine::TEMPLATE_ORIENTED);
        $renderingEngine->setStateName(TAOQTITEST_RENDERING_STATE_NAME);
        $renderingEngine->setRootBase(TAOQTITEST_PLACEHOLDER_BASE_URI . rtrim($this->getExtraPath(), $ds));
        $renderingEngine->setViewsName(TAOQTITEST_VIEWS_NAME);
        $this->setRenderingEngine($renderingEngine);
        
        // Initialize CSS Scoper.
        $this->setCssScoper(new CssScoper());
        
        // Initialize Post Markup Renderer.
        $this->setMarkupPostRenderer(new MarkupPostRenderer(true, true, true));
    }
    
    /**
     * Compile a QTI Test and the related QTI Items.
     * 
     * The compilation process occurs as follows:
     * 
     * * 1. The resources composing the test are copied into the private compilation directory.
     * * 2. The test definition is packed (test and items put together in a single definition).
     * * 3. The items composing the test are compiled.
     * * 4. The rubric blocks are rendered into PHP templates.
     * * 5. The test definition is compiled into PHP source code for maximum performance.
     * * 6. The resources composing the test that have to be accessed at delivery time are compied into the public compilation directory.
     * * 7. The Service Call definition enabling TAO to run the compiled test is built.
     * 
     * @param core_kernel_file_File $destinationDirectory The directory where the compiled files must be put.
     * @return tao_models_classes_service_ServiceCall A ServiceCall object that represent the way to call the newly compiled test.
     * @throws taoQtiTest_models_classes_QtiTestCompilationFailedException If an error occurs during the compilation.
     */
    public function compile() {
        
        $report = new common_report_Report(common_report_Report::TYPE_INFO);
        
        try {
            // 0. Initialize compilation (compilation directories, renderers, ...).
            $this->initCompilation();
            
            // 1. Copy the resources composing the test into the private complilation directory.
            $this->copyPrivateResources();
            
            // 2. Compact the test definition itself.
            $compiledDoc = $this->compactTest();
            
            // 3. Compile the items of the test.
            $itemReport = $this->compileItems($compiledDoc);
            $report->add($itemReport);
            if ($itemReport->getType() != common_report_Report::TYPE_SUCCESS) {
                $msg = 'Failed item compilation.';
                $code = taoQtiTest_models_classes_QtiTestCompilationFailedException::ITEM_COMPILATION;
                throw new taoQtiTest_models_classes_QtiTestCompilationFailedException($msg, $this->getResource(), $code);
            }
            
            // 4. Explode the rubric blocks in the test into rubric block refs.
            $this->explodeRubricBlocks($compiledDoc);
            
            // 5. Update test definition with additional runtime info.
            $assessmentTest = $compiledDoc->getDocumentComponent();
            $this->updateTestDefinition($assessmentTest);
            
            // 6. Compile rubricBlocks and serialize on disk.
            $this->compileRubricBlocks($assessmentTest);
            
            // 7. Copy the needed files into the public directory.
            $this->copyPublicResources();
            
            // 8. Compile the test definition into PHP source code and put it
            // into the private directory.
            $this->compileTest($assessmentTest);
            
            // 9. Compile the test meta data into PHP array source code and put it
            // into the private directory.
            $this->compileMeta($assessmentTest);
            
            // 10. Build the service call.
            $serviceCall = $this->buildServiceCall();
            
            common_Logger::t("QTI Test successfully compiled.");

            $report->setType(common_report_Report::TYPE_SUCCESS);
            $report->setMessage(__('QTI Test "%s" successfully published.', $this->getResource()->getLabel()));
            $report->setData($serviceCall);
        }
        catch(XmlStorageException $e){
            $subReport = new common_report_Report(common_report_Report::TYPE_ERROR, __('The QTI Test XML or one of its dependencies is malformed or empty.'));
            $report->add($subReport);

            $report->setType(common_report_Report::TYPE_ERROR);
            $report->setMessage(__('QTI Test "%s" publishing failed.', $this->getResource()->getLabel()));
        }
        catch (Exception $e) {
            common_Logger::e($e->getMessage());
            // All exception that were not catched in the compilation steps
            // above have a last chance here.
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->setMessage(__('QTI Test "%s" publishing failed.', $this->getResource()->getLabel()));
        }
        
        // Reset time outs to initial value.
        helpers_TimeOutHelper::reset();
        
        return $report;
    }
    
    /**
     * Compact the test and items in a single QTI-XML Compact Document.
     * 
     * @return XmlCompactDocument.
     */
    protected function compactTest() {
        $testService = taoQtiTest_models_classes_QtiTestService::singleton();
        $test = $this->getResource();
        $testContent = $testService->getTestContent($test);
        
        common_Logger::t('Compacting QTI test ' . $test->getLabel() . '...');
        
        $itemResolver = new taoQtiTest_helpers_ItemResolver('');
        $originalDoc = $testService->getDoc($test);
        
        $compiledDoc = XmlCompactDocument::createFromXmlAssessmentTestDocument($originalDoc, $itemResolver);
        common_Logger::t("QTI Test XML transformed in a compact version.");
        
        return $compiledDoc;
    }
    
    /**
     * Compile the items referended by $compactDoc.
     * 
     * @param XmlCompactDocument $compactDoc An XmlCompactDocument object referencing the items of the test.
     * @throws taoQtiTest_models_classes_QtiTestCompilationFailedException If the test does not refer to at least one item.
     * @return common_report_Report
     */
    protected function compileItems(XmlCompactDocument $compactDoc) {
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Items Compilation'));
        $iterator = new QtiComponentIterator($compactDoc->getDocumentComponent(), array('assessmentItemRef'));
        $itemCount = 0;
        foreach ($iterator as $assessmentItemRef) {
            
            // Each item could take some time to be compiled, making the request to timeout.
            helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::SHORT);
            
            $itemToCompile = new core_kernel_classes_Resource($assessmentItemRef->getHref());
            $subReport = $this->subCompile($itemToCompile);
            $report->add($subReport);
            if ($subReport->getType() == common_report_Report::TYPE_SUCCESS) {
                $itemService = $subReport->getdata(); 
                $inputValues = tao_models_classes_service_ServiceCallHelper::getInputValues($itemService, array());
                $assessmentItemRef->setHref($inputValues['itemUri'] . '|' . $inputValues['itemPath'] . '|' . $inputValues['itemDataPath']);
            } else {
                $report->setType(common_report_Report::TYPE_ERROR);
            }

            // Count the item even if it fails to avoid false "no item" error.
            $itemCount++;
            common_Logger::t("QTI Item successfully compiled and registered as a service call in the QTI Test Definition.");
        }
        
        if ($itemCount === 0) {
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->setMessage(__("A QTI Test must contain at least one QTI Item to be compiled. None found."));
        }
        return $report;
    }
    
    /**
     * Explode the rubric blocks of the test definition into separate QTI-XML files and
     * remove the compact XML document from the file system (useless for
     * the rest of the compilation process).
     * 
     * @param XmlCompactDocument $compiledDoc
     */
    protected function explodeRubricBlocks(XmlCompactDocument $compiledDoc) {
        $savePath = $this->getPrivateDirectory()->getPath() . 'compact-test.xml';
        $compiledDoc->setExplodeRubricBlocks(true);
        $compiledDoc->save($savePath);
        unlink($savePath);
    }
    
    /**
     * Update the test definition with additional data, such as TAO specific
     * rules and variables.
     * 
     * @param AssessmentTest $assessmentTest
     */
    protected function updateTestDefinition(AssessmentTest $assessmentTest) {
        return;
    }
    
    /**
     * Copy the resources (e.g. images) of the test to the private compilation directory.
     */
    protected function copyPrivateResources() {
        $testService = taoQtiTest_models_classes_QtiTestService::singleton();
        $testPath = $testService->getTestContent($this->getResource())->getAbsolutePath();
        
        $subContent = tao_helpers_File::scandir($testPath, array('recursive' => false, 'absolute' => true));
        $privateDirPath = $this->getPrivateDirectory()->getPath();
        
        // Recursive copy of each root level resources.
        foreach ($subContent as $subC) {
            tao_helpers_File::copy($subC, $privateDirPath . basename($subC));
        }
    }
    
    /**
     * Build the Service Call definition that makes TAO able to run the compiled test
     * later on at delivery time.
     * 
     * @return tao_models_classes_service_ServiceCall
     */
    protected function buildServiceCall() {
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_QTITEST_TESTRUNNERSERVICE));
        $param = new tao_models_classes_service_ConstantParameter(
                        // Test Definition URI passed to the QtiTestRunner service.
                        new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTDEFINITION),
                        $this->getResource()
        );
        $service->addInParameter($param);
        
        $param = new tao_models_classes_service_ConstantParameter(
                        // Test Compilation URI passed to the QtiTestRunner service.
                        new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTCOMPILATION),
                        $this->getPrivateDirectory()->getId() . '|' . $this->getPublicDirectory()->getId()
        );
        $service->addInParameter($param);
        
        return $service;
    }
    
    /**
     * Compile the RubricBlocRefs' contents into a separate rubric block PHP template.
     * 
     * @param AssessmentTest $assessmentTest The AssessmentTest object you want to compile the rubrickBlocks.
     */
    protected function compileRubricBlocks(AssessmentTest $assessmentTest) {
        $rubricBlockRefs = $assessmentTest->getComponentsByClassName('rubricBlockRef');
        
        foreach ($rubricBlockRefs as $rubricRef) {
            
            $rubricRefHref = $rubricRef->getHref();
            $cssScoper = $this->getCssScoper();
            $renderingEngine = $this->getRenderingEngine();
            $markupPostRenderer = $this->getMarkupPostRenderer();
            $compiledDocDir = $this->getPrivateDirectory()->getPath();
            $publicCompiledDocDir = $this->getPublicDirectory()->getPath();
            
            // -- loading...
            common_Logger::t("Loading rubricBlock '" . $rubricRefHref . "'...");
            
            $rubricDoc = new XmlDocument();
            $rubricDoc->load($compiledDocDir . $rubricRefHref);
            
            common_Logger::t("rubricBlock '" . $rubricRefHref . "' successfully loaded.");
            
            // -- rendering...
            common_Logger::t("Rendering rubricBlock '" . $rubricRefHref . "'...");
            
            $pathinfo = pathinfo($rubricRefHref);
            $renderingFile = $compiledDocDir . $pathinfo['filename'] . '.php';
            
            $rubric = $rubricDoc->getDocumentComponent();
            $rubricStylesheets = $rubric->getStylesheets();
            $stylesheets = new StylesheetCollection();
            // In any case, include the base QTI Stylesheet.
            $stylesheets->merge($rubricStylesheets);
            $rubric->setStylesheets($stylesheets);
            
            // -- If the rubricBlock has no id, give it a auto-generated one in order
            // to be sure that CSS rescoping procedure works fine (it needs at least an id
            // to target its scoping).
            if ($rubric->hasId() === false) {
                // Prepend 'tao' to the generated id because the CSS
                // ident token must begin by -|[a-zA-Z]
                $rubric->setId('tao' . uniqid());
            }
            
            // -- Copy eventual remote resources of the rubricBlock.
            $this->copyRemoteResources($rubric);
            
            $domRendering = $renderingEngine->render($rubric);
            $mainStringRendering = $markupPostRenderer->render($domRendering);
            
            // Prepend stylesheets rendering to the main rendering.
            $styleRendering = $renderingEngine->getStylesheets();
            $mainStringRendering = $styleRendering->ownerDocument->saveXML($styleRendering) . $mainStringRendering;
            
            foreach ($stylesheets as $rubricStylesheet) {
                $stylesheetPath = taoQtiTest_helpers_Utils::storedQtiResourcePath($compiledDocDir . ltrim($this->getExtraPath(), '/'), $rubricStylesheet->getHref());
                file_put_contents($stylesheetPath, $cssScoper->render($stylesheetPath, $rubric->getId()));
            }
            
            // -- Replace the artificial 'tao://qti-directory' base path with a runtime call to the delivery time base path.
            $mainStringRendering = str_replace(TAOQTITEST_PLACEHOLDER_BASE_URI, '<?php echo $' . TAOQTITEST_BASE_PATH_NAME . '; ?>', $mainStringRendering);
            file_put_contents($renderingFile, $mainStringRendering);
            common_Logger::t("rubricBlockRef '" . $rubricRefHref . "' successfully rendered.");
            
            // -- Clean up old rubric block and reference the new rubric block template.
            unlink($compiledDocDir . $rubricRefHref);
            $rubricRef->setHref('./' . $pathinfo['filename'] . '.php');
        }
    }
    
    /**
     * Copy the test resources (e.g. images) that will be availabe at delivery time
     * in the public compilation directory.
     * 
     */
    protected function copyPublicResources() {
        
        $compiledDocDir = $this->getPrivateDirectory()->getPath();
        $publicCompiledDocDir = $this->getPublicDirectory()->getPath();
        
        foreach (tao_helpers_File::scandir($compiledDocDir, array('recursive' => true, 'only' => tao_helpers_File::$FILE, 'absolute' => true)) as $file) {
            $mime = tao_helpers_File::getMimeType($file, true);
            $pathinfo = pathinfo($file);
            
            // Exclude CSS files because already copied when dealing with rubric blocks.
            if (in_array($mime, self::getPublicMimeTypes()) === true && $pathinfo['extension'] !== 'php') {
                $file = str_replace($compiledDocDir, '', $file);
                
                common_Logger::t("Copying public resource '${file}'...");
                taoQtiTest_helpers_Utils::storeQtiResource($publicCompiledDocDir, $file, $compiledDocDir);
            }
        }
    }
    
    /**
     * Copy all remote resource (absolute URLs to another host) contained in a rubricBlock into a dedicated directory. Remote resources
     * can be refereced by the following QTI classes/attributes:
     * 
     * * a:href
     * * object:data
     * * img:src
     * 
     * @param AssessmentTest $assessmentTest An AssessmentTest object.
     * @throws taoQtiTest_models_classes_QtiTestCompilationFailedException If a remote resource cannot be retrieved.
     */
    protected function copyRemoteResources(RubricBlock $rubricBlock) {
        
        $publicCompiledDocDir = $this->getPublicDirectory()->getPath();
        $ds = DIRECTORY_SEPARATOR;
        $destination = $publicCompiledDocDir . trim($this->getExtraPath(), $ds) . $ds . TAOQTITEST_REMOTE_FOLDER . $ds;
        
        // If remote directory does not exist yet, create it.
        if (file_exists($destination) === false) {
            mkdir($destination, 0770, true);
        }
        
        // Search for all class-attributes in QTI-XML that might reference a remote file.
        $search = $rubricBlock->getComponentsByClassName(array('a', 'object', 'img'));
        foreach ($search as $component) {
            switch ($component->getQtiClassName()) {

                case 'object':
                    $url = $component->getData();
                break;
                
                case 'img':
                    $url = $component->getSrc();
                break;
            }
            
            if (isset($url) && !preg_match('@^' . ROOT_URL . '@', $url) && !Url::isRelative($url)) {
                $finalDestination = taoItems_helpers_Deployment::retrieveFile($url, $destination);
                $pathinfo = pathinfo($finalDestination);
                
                if ($finalDestination !== false) {
                    $newUrl =  TAOQTITEST_REMOTE_FOLDER . '/' . $pathinfo['basename'];
                     
                    switch ($component->getQtiClassName()) {
                        case 'object':
                            $component->setData($newUrl);
                        break;
                         
                        case 'img':
                            $component->setSrc($newUrl);
                        break;
                    }
                }
                else {
                    $msg = "The remote resource referenced by '${url}' could not be retrieved.";
                    throw new taoQtiTest_models_classes_QtiTestCompilationFailedException($msg, $this->getResource(), taoQtiTest_models_classes_QtiTestCompilationFailedException::REMOTE_RESOURCE);
                }
            }
        }
    }
    
    /**
     * Compile the given $test into PHP source code for maximum performance. The file will be stored
     * into PRIVATE_DIRECTORY/compact-test.php.
     * 
     * @param AssessmentTest $test
     */
    protected function compileTest(AssessmentTest $test) {
        // Compiling a test may require extra processing time.
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::SHORT);
        
        $compiledDocDir = $this->getPrivateDirectory()->getPath();
        $compiledDocPath = $compiledDocDir . TAOQTITEST_COMPILED_FILENAME;
        $phpCompiledDoc = new PhpDocument('2.1');
        $phpCompiledDoc->setDocumentComponent($test);
        $phpCompiledDoc->save($compiledDocPath);
        common_Logger::d("QTI-PHP Test Compilation file registered at '" . $compiledDocPath . "'.");
    }
    
    /**
     * Compile the $test meta-data into PHP source code for maximum performance. The file is
     * stored into PRIVATE_DIRECTORY/test-meta.php.
     * 
     * @param AssessmentTest $test
     */
    protected function compileMeta(AssessmentTest $test) {
        $compiledDocDir = $this->getPrivateDirectory()->getPath();
        $compiledDocPath = $compiledDocDir . TAOQTITEST_COMPILED_META_FILENAME;
        
        $meta = taoQtiTest_helpers_TestCompilerUtils::testMeta($test);
        $phpCode = common_Utils::toPHPVariableString($meta);
        $phpCode = '<?php return ' . $phpCode . '; ?>';
        
        file_put_contents($compiledDocPath, $phpCode);
    }
    
    /**
     * Get the list of mime types of files that are accepted to be put
     * into the public compilation directory.
     * 
     * @return array
     */
    static protected function getPublicMimeTypes() {
        return self::$publicMimeTypes;
    }
}
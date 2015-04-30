<?php
/**
 * Creates all resources related to the tao font from the icomoon export
 * Antoine Robin <antoine.robin@vesperiagroup.com>
 * based on work of Dieter Raber <dieter@taotesting.com>
 */

namespace oat\taoDevTools\actions;

use ZipArchive;

class FontConversion extends \tao_actions_CommonModule
{

    private $dir;
    private $assetDir;
    private $doNotEdit;
    private $currentSelection;
    private $taoDir;

    public function __construct()
    {
        $this->tmpDir           = \tao_helpers_File::createTempDir();
        $this->dir              = str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__));
        $this->taoDir           = dirname($this->dir) . '/tao';
        $this->assetDir         = $this->dir . '/fontConversion/assets';
        $this->doNotEdit        = file_get_contents($this->assetDir . '/do-not-edit.tpl');
        $this->currentSelection = $this->assetDir . '/selection.json';

        $writables = array(
            $this->taoDir . '/views/css/font/tao/',
            $this->taoDir . '/views/scss/inc/fonts/',
            $this->taoDir . '/views/js/lib/ckeditor/skins/tao/scss/inc/',
            $this->taoDir . '/helpers/',
            $this->assetDir
        );

        foreach($writables as $writable) {
            if(!is_writable($writable)) {
                throw new \Exception(implode("\n<br>", $writables) . ' must be writable');
            }
        }

        $this->setData('icon-listing', $this->loadIconListing());
    }

    public function index()
    {
        $this->setView('fontConversion/view.tpl');
    }

    /**
     * Process the font archive
     *
     * @return bool
     */
    public function processFontArchive()
    {

        //return array('error' => __('Unable to read the file : ') . $archiveDir . '/style.css');

        // upload result is either the path to the zip file or an array with errors
        $uploadResult = $this->uploadArchive();
        if (!empty($uploadResult['error'])) {
            $this -> returnJson($uploadResult);
            return false;
        }

        // extract result is either the path to the extracted files or an array with errors
        $extractResult = $this->extractArchive($uploadResult);
        if (!empty($extractResult['error'])) {
            $this -> returnJson($extractResult);
            return false;
        }

        // check if the new font contains at least al glyphs from the previous version
        $currentSelection = json_decode(file_get_contents($extractResult . '/selection.json'));
        $oldSelection     = json_decode(file_get_contents($this->currentSelection));
        $integrityCheck   = $this->checkIntegrity($currentSelection, $oldSelection);
        if (!empty($integrityCheck['error'])) {
            $this -> returnJson($integrityCheck);
            return false;
        }

        //generate tao scss
        $scssGenerationResult = $this->generateTaoScss($extractResult, $currentSelection->icons);
        if (!empty($scssGenerationResult['error'])) {
            $this -> returnJson($scssGenerationResult);
            return false;
        }

        $ckGenerationResult = $this->generateCkScss($extractResult, $currentSelection->icons);
        if (!empty($ckGenerationResult['error'])) {
            $this -> returnJson($ckGenerationResult);
            return false;
        }

        // php generation result is either the path to the php class or an array with errors
        $phpGenerationResult = $this->generatePhpClass($currentSelection->icons);
        if (!empty($phpGenerationResult['error'])) {
            $this -> returnJson($phpGenerationResult);
            return false;
        }

        $distribution = $this->distribute($extractResult);
        if (!empty($distribution['error'])) {
            $this -> returnJson($distribution);
            return false;
        }

        chdir($this -> taoDir . '/views/build');

        $compilationResult = $this -> compileCss();
        if(!empty($compilationResult['error'])) {
            $this -> returnJson($compilationResult);
            return false;
        }

        $this -> returnJson(array('success' => 'The TAO icon font has been updated'));
        return  true;

    }

    /**
     * Upload the zip archive to a tmp directory
     *
     * @return array|string
     */
    protected function uploadArchive()
    {

        if ($_FILES['content']['error'] !== UPLOAD_ERR_OK) {

            \common_Logger::w('File upload failed with error ' . $_FILES['content']['error']);
            switch ($_FILES['content']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = __('Archive size must be lesser than : ') . ini_get('post_max_size');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error = __('No file uploaded');
                    break;
                default:
                    $error = __('File upload failed');
                    break;
            }
            return array('error' => $error);
        }

        // upload ok but problem with data
        if (empty($_FILES['content']['type'])) {
            $finfo                     = finfo_open(FILEINFO_MIME_TYPE);
            $_FILES['content']['type'] = finfo_file($finfo, $_FILES['content']['tmp_name']);
        }

        if (!$_FILES['content']['type'] || preg_match('#application\/(x-?)?zip(-compressed)?#', $_FILES['content']['type']) !== 1) {
            return array('error' => __('Media must be a zip archive, got ' . $_FILES['content']['type']));
        }

        $filePath = $this->tmpDir . '/' . $_FILES['content']['name'];
        if (!move_uploaded_file($_FILES['content']['tmp_name'], $filePath)) {
            return array('error' => __('Unable to move uploaded file'));
        }

        return $filePath;
    }

    /**
     * Unzip archive from icomoon
     *
     * @param $archiveFile
     * @return array|string
     */
    protected function extractArchive($archiveFile)
    {
        $archiveDir    = dirname($archiveFile);
        $archiveObj    = new ZipArchive();
        $archiveHandle = $archiveObj->open($archiveFile);
        if (true !== $archiveHandle) {
            return array('error' => 'Could not open archive');
        }

        if (!$archiveObj->extractTo($archiveDir)) {
            $archiveObj->close();
            return array('error' => 'Could not extract archive');
        }
        $archiveObj->close();
        return $archiveDir;
    }

    /**
     * Checks whether the new font contains at least all glyphs from the previous version
     *
     * @param $currentSelection
     * @param $oldSelection
     * @return bool|array
     */
    protected function checkIntegrity($currentSelection, $oldSelection)
    {
        if($currentSelection->metadata->name !== 'tao'
          || $currentSelection->preferences->fontPref->metadata->fontFamily !== 'tao') {
            return array('error' => 'You need to change the font name to "tao" in the icomoon preferences');
        }
        $newSet = $this->dataToGlyphSet($currentSelection);
        $oldSet = $this->dataToGlyphSet($oldSelection);
        return !!count(array_diff($oldSet, $newSet))
            ? array('error', '<p>Font incomplete!</p><ul><li>Is the extension in sync width git?</li><li>Have you removed any glyphs?</li></ul>')
            : true;
    }

    /**
     * Generate a listing of all glyph names in a font
     *
     * @param $data
     * @return array
     */
    protected function dataToGlyphSet($data)
    {
        $glyphs = array();
        foreach ($data->icons as $iconProperties) {
            $glyphs[] = $iconProperties->properties->name;
        }
        return $glyphs;
    }

    /**
     * Generate TAO scss
     *
     * @param $archiveDir
     * @param $icons
     * @return bool
     */
    protected function generateTaoScss($archiveDir, $icons)
    {
        if (!is_readable($archiveDir . '/style.css')) {
            return array('error' => __('Unable to read the file : ') . $archiveDir . '/style.css');
        }
        $cssContent = file_get_contents($archiveDir . '/style.css');
        $iconCss    = array(
            'classes' => '',
            'def'     => '',
            'vars'    => ''
        );

        // font-face
        $cssContentArr  = explode('[class^="icon-"]', $cssContent);
        $iconCss['def'] = str_replace('fonts/tao.', 'font/tao/tao.', $cssContentArr[0]) . "\n";

        // font-family etc.
        $cssContentArr   = explode('.icon', $cssContentArr[1]);
        $iconCss['vars'] = str_replace(', [class*=" icon-"]', '@mixin tao-icon-setup', $cssContentArr[0]);

        // the actual css code
        $iconCss['classes'] = '[class^="icon-"], [class*=" icon-"] { @include tao-icon-setup; }' . "\n";

        // build code for PHP icon class and tao-*.scss files
        foreach ($icons as $iconProperties) {

            $properties = $iconProperties->properties;
            $icon       = $properties->name;
            $iconHex    = dechex($properties->code);

            // tao-*.scss data
            $iconCss['vars'] .= '@mixin icon-' . $icon . ' { content: "\\' . $iconHex . '"; }' . "\n";
            $iconCss['classes'] .= '.icon-' . $icon . ':before { @include icon-' . $icon . '; }' . "\n";
        }

        // compose and write SCSS files
        $retVal = array();
        foreach ($iconCss as $key => $value) {
            $retVal[$key] = $this->tmpDir . '/_tao-icon-' . $key . '.scss';
            file_put_contents($retVal[$key], $this->doNotEdit . $iconCss[$key]);
        }
        return $retVal;
    }

    /**
     * Generate scss for CK editor
     *
     * @return string
     */
    protected function generateCkScss()
    {

        $ckIni = parse_ini_file($this->assetDir . '/ck-editor-classes.ini');

        // ck toolbar icons
        $cssContent = '@import "inc/bootstrap";' . "\n";
        $cssContent .= '.cke_button_icon, .cke_button { @include tao-icon-setup;}' . "\n";

        foreach ($ckIni as $ckIcon => $taoIcon) {
            if (!$taoIcon) {
                continue;
            }
            $cssContent .= '.' . $ckIcon . ':before { @include ' . $taoIcon . ';}' . "\n";
        }

        file_put_contents($this->tmpDir . '/_ck-icons.scss', $this->doNotEdit . $cssContent);

        return $this->tmpDir . '/_ck-icons.scss';
    }

    /**
     * Generate PHP icon class
     *
     * @param $iconSet
     * @return array|string
     */
    protected function generatePhpClass($iconSet)
    {
        $phpClass     = file_get_contents($this->assetDir . '/class.Icon.tpl');
        $phpClassPath = $this->tmpDir . '/class.Icon.php';
        $constants    = '';
        $functions    = '';
        $patterns     = array('{CONSTANTS}', '{FUNCTIONS}', '{DATE}', '{DO_NOT_EDIT}');

        foreach ($iconSet as $iconProperties) {
            $icon = $iconProperties->properties->name;
            // constants
            $constName = 'CLASS_' . strtoupper(str_replace('-', '_', $icon));
            // functions
            $iconFn = strtolower(trim($icon));
            $iconFn = str_replace(' ', '', ucwords(preg_replace('~[\W_-]+~', ' ', $iconFn)));
            $functions .= '    public static function icon' . $iconFn . '($options=array()){' . "\n"
                . '        return self::buildIcon(self::' . $constName . ', $options);' . "\n" . '    }' . "\n\n";

            $constants .= '    const ' . $constName . ' = \'icon-' . $icon . '\';' . "\n";
        }

        $phpClass = str_replace(
            $patterns,
            array($constants, $functions, date('Y-m-d H:i:s'), $this->doNotEdit),
            $phpClass
        );

        file_put_contents($phpClassPath, $phpClass);

        ob_start();
        system('php -l ' . $phpClassPath);
        $parseResult = ob_get_clean();

        if (false === strpos($parseResult, 'No syntax errors detected')) {
            $parseResult = strtok($parseResult, PHP_EOL);
            return array('error' => $parseResult);
        }

        return $phpClassPath;
    }

    /**
     * Distribute generated files to their final destination
     *
     * @param $tmpDir
     * @return array|bool
     */
    protected function distribute($tmpDir)
    {
        // copy fonts
        foreach(glob($tmpDir . '/font/tao.*') as $font) {
            if(!copy($font, $this->taoDir . '/views/css/font/tao/' . basename($font))) {
                return array('error' => 'Failed to copy ' . $font);
            }
        };

        // copy icon scss
        foreach(glob($tmpDir . '/_tao-icon-*.scss') as $scss) {
            if(!copy($scss, $this->taoDir . '/views/scss/inc/fonts/' . basename($scss))) {
                return array('error' => 'Failed to copy ' . $scss);
            }
        };

        // copy ck editor styles
        if(!copy($tmpDir . '/_ck-icons.scss', $this->taoDir . '/views/js/lib/ckeditor/skins/tao/scss/inc/_ck-icons.scss')) {
            return array('error' => 'Failed to copy ' . $tmpDir . '/_ck-icons.scss');
        }

        // copy helper class
        if(!copy($tmpDir . '/class.Icon.php', $this->taoDir . '/helpers/class.Icon.php')) {
            return array('error' => 'Failed to copy ' . $tmpDir . '/class.Icon.php');
        }

        // copy selection to assets
        if(!copy($tmpDir . '/selection.json', $this->assetDir . '/selection.json')) {
            return array('error' => 'Failed to copy ' . $tmpDir . '/selection.json');
        }

        return true;
    }

    /**
     * Compile CSS
     *
     * @return bool
     */
    protected function compileCss(){
        system('grunt taosass', $result);
        return $result === 0;
    }

    /**
     * Download current selection to initialize icomoon
     */
    public function downloadCurrentSelection()
    {
        header('Content-disposition: attachment; filename=selection.json');
        header('Content-type: application/json');
        echo(file_get_contents($this->currentSelection));
    }

    /**
     * Sort existing icons by name
     *
     * @param $a
     * @param $b
     * @return bool
     */
    protected function sortIconListing($a, $b)
    {
        return $a->properties->name > $b->properties->name;
    }

    /**
     * List existing icons
     *
     * @return mixed
     */
    protected function loadIconListing()
    {
        $icons = json_decode(file_get_contents($this->currentSelection));
        $icons = $icons->icons;
        usort($icons, array($this, 'sortIconListing'));
        return $icons;
    }
}

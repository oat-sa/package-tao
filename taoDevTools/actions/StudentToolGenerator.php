<?php
/**
 * Creates a new student tool based on a template
 *
 * Date: 06/11/14
 * Time: 13:45
 */

namespace oat\taoDevTools\actions;

use Jig\Utils\StringUtils;


class StudentToolGenerator extends \tao_actions_CommonModule {


    /**
     * @var array
     */
    private $data = array();


    public function index()
    {
        $this->setView('studentToolGenerator/view.tpl');
        if($_POST){
            try {
                $targetPath = $this -> generateTool();
                $this->setData('message', 'Created skeleton in ' . $targetPath);
                if ($this->getRequestParameter('errorMessage')) {
                    $this->setData('errorMessage', $this->getRequestParameter('errorMessage'));
                }
                return false;
            }
            catch(\Exception $e) {
                $this->setData('errorMessage', $e -> getMessage());
            }
        }
    }


    /**
     * Take template and create the tool
     *
     * @throws \Exception
     */
    protected function generateTool() {

        $this->data = $this->getMappedArguments();
        $generatorPath = str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__)) . '/studentToolGenerator';
        $targetPath    = $generatorPath . '/generated-code/' . $this->data['client'] . '/' . $this->data['tool-id'];
        $templatePath  = $generatorPath . '/template';

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($templatePath), \RecursiveIteratorIterator::SELF_FIRST);

        $patterns = $this -> getPatterns();
        $replacements = $this -> getReplacements();

        foreach($objects as $tplFile => $cursor){
            if(in_array(basename($tplFile), array('.', '..'))) {
                continue;
            }

            $toolFile = str_replace($templatePath, $targetPath, $tplFile);

            if($cursor->isDir() && !is_dir($toolFile)) {
                mkdir($toolFile, 0755, true);
            }
            else if($cursor->isFile()){
                $toolFile = dirname($toolFile) . '/' . str_replace('template', $this->data['tool-base'], basename($toolFile));
                $toolContent = str_replace($patterns, $replacements, file_get_contents($tplFile));
                file_put_contents($toolFile, $toolContent);
            }
        }
        return $targetPath;
    }


    /**
     * Generate all required data from _POST
     *
     * @return array
     * @throws \Exception
     */
    protected function getMappedArguments()
    {
        $requiredArgs = array(
            'client'       => 'Prefix',
            'tool-title'   => 'Tool title',
            'transparent'  => '(1 or 0)',
            'rotatable'    => '(1 or 0)',
            'movable'      => '(1 or 0)',
            'adjustable-x' => '(1 or 0)',
            'adjustable-y' => '(1 or 0)',
        );
        $argHelp = "<p>Required arguments are:</p><ul>";
        foreach ($requiredArgs as $key => $value) {
            $argHelp .= '<li>' . $key . ': ' . $value . '</li>';
        }
        $argHelp .= '</ul>';

        foreach ($requiredArgs as $key => $value) {
            // !string '0' is a valid entry!
            if (!isset($_POST[$key]) || $_POST[$key] === '') {
                throw new \Exception($argHelp);
                break;
            }
            // trim all, cast 0|1 to bool
            $_POST[$key] = trim($_POST[$key]);
            if (in_array($_POST[$key], array('0', '1'))) {
                $_POST[$key] = (bool)$_POST[$key];
            }
        }

        $_POST['client']           = strtoupper($_POST['client']);
        $_POST['tool-base']        = StringUtils::removeSpecChars($_POST['tool-title']);
        $_POST['tool-fn']          = StringUtils::camelize($_POST['tool-base']);
        $_POST['tool-obj']         = ucfirst($_POST['tool-fn']);
        $_POST['tool-id']          = strtolower($_POST['client']) . $_POST['tool-obj'];
        $_POST['is-transparent']   = json_encode($_POST['transparent']);
        $_POST['is-rotatable-tl']  = json_encode($_POST['rotatable']); // default position of rotator

                                        // only visible when not adjustable
        $_POST['is-rotatable-tr']  = json_encode($_POST['rotatable'] && (!$_POST['adjustable-x'] && !$_POST['adjustable-y']));
        $_POST['is-rotatable-br']  = json_encode($_POST['rotatable'] && (!$_POST['adjustable-x'] && !$_POST['adjustable-y']));

        $_POST['is-rotatable-bl']  = json_encode($_POST['rotatable']); // also default position of rotator

        // alternative positions, need to be configured manually
        $_POST['is-rotatable-t']  = json_encode(false);
        $_POST['is-rotatable-r']  = json_encode(false);
        $_POST['is-rotatable-b']  = json_encode(false);
        $_POST['is-rotatable-l']  = json_encode(false);

        $_POST['is-movable']       = json_encode($_POST['movable']);
        $_POST['is-adjustable-x']  = json_encode($_POST['adjustable-x']);
        $_POST['is-adjustable-y']  = json_encode($_POST['adjustable-y']);
        $_POST['is-adjustable-xy'] = json_encode($_POST['adjustable-x'] && $_POST['adjustable-y']);

        unset($_POST['transparent']);
        unset($_POST['rotatable']);
        unset($_POST['movable']);
        unset($_POST['adjustable-x']);
        unset($_POST['adjustable-y']);

        return $_POST;
    }

    /**
     * First arg for str_replace
     *
     * @return array
     */
    protected function getPatterns() {
        $patterns = array();
        foreach($this->data as $pattern => $replacement) {
            $patterns[]     = '{' . $pattern . '}';
        }
        return $patterns;
    }

    /**
     * Second arg for str_replace
     *
     * @return array
     */
    protected function getReplacements() {
        return array_values($this -> data);
    }
} 
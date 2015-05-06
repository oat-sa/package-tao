<?php
{licenseBlock}

namespace {authorNs}\{id}\actions;

/**
 * Sample controller
 *
 * @author {author}
 * @package {id}
 * @subpackage actions
 * @license {license}
 *
 */
class {classname} extends \tao_actions_CommonModule {

    /**
     * initialize the services
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * A possible entry point to tao
     */
    public function index() {
        echo __("Hello World");
    }

    public function templateExample() {
        $this->setData('author', '{author}');
        $this->setView('sample.tpl');
    }
}
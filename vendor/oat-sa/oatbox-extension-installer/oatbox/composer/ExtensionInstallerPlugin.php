<?php

namespace oatbox\composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ExtensionInstallerPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io){
        $installer = new ExtensionInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}

?>
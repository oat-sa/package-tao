<?php
namespace oatbox\composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;


class ExtensionInstaller extends LibraryInstaller
{
    const EXTENSION_NAME_KEY = 'tao-extension-name';
       
    public function getPackageBasePath(PackageInterface $package){
        $ext = self::EXTENSION_NAME_KEY;
        $extra = $package->getExtra();
        if(!is_null($extra) && isset($extra[$ext])){
            return $extra[$ext];
        } else {
            throw new \InvalidArgumentException('could not find extension name in manifest');
           
        }
       
    }
    
    public function supports($packageType)
    {
        return $packageType === "tao-extension";
    }

}

?>
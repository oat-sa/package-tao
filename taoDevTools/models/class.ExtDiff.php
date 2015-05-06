<?php

class taoDevTools_models_ExtDiff extends taoDevTools_models_RdfDiff
{

    public function __construct($fromManifest, $toManifest)
    {
        parent::__construct();
        if (! is_null($fromManifest)) {
            foreach ($this->getAllModelFiles($fromManifest) as $file) {
                $this->removeRdf($file);
            }
        }
        if (! is_null($toManifest)) {
            foreach ($this->getAllModelFiles($toManifest) as $file) {
                $this->addRdf($file);
            }
        }
    }

    private function getAllModelFiles(common_ext_Manifest $manifest)
    {
        $returnValue = array();
        $localesPath = dirname($manifest->getFilePath()) . DIRECTORY_SEPARATOR . 'locales' . DIRECTORY_SEPARATOR;
        foreach ($manifest->getInstallModelFiles() as $rdfpath) {
            $returnValue[] = $rdfpath;
            if (file_exists($localesPath)) {
                $fileName = basename($rdfpath);
                foreach (new DirectoryIterator($localesPath) as $fileinfo) {
                    if (! $fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn' && $fileinfo->getFilename() != 'en-US') {
                        $candidate = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . $fileName;
                        if (file_exists($candidate)) {
                            $returnValue[] = $candidate;
                        }
                    }
                }
            }
        }
        return $returnValue;
    }
}

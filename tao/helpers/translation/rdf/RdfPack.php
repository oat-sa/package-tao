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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\tao\helpers\translation\rdf;

use tao_helpers_translation_POFileReader;
use common_Utils;
use core_kernel_classes_Triple;
use oat\generis\model\kernel\persistence\file\FileModel;

/**
 * Translation pack of the rdf resources
 * 
 * @access public
 * @author Joel
 * @package tao
 */
class RdfPack implements \IteratorAggregate {

    /**
     * @var string
     */
    private $langCode;

    /**
     * @var common_ext_Extension
     */
    private $extension;

    /**
     * Create a new bundle
     * @param string $langCode
     * @param common_ext_Extension
     * @throws commone_exception_InvalidArgumentType
     * @throws commone_exception_Errors
     */
    public function __construct($langCode, \common_ext_Extension $extension){
        if(!is_string($langCode)){
            throw new \common_exception_InvalidArgumentType(__CLASS__, __METHOD__, 0, 'string', $this);   
        }
        if(empty($langCode) || empty($extension)){
            throw new \common_exception_Error('$langCode and $extensions needs to be assigned.');
        }

        $this->langCode     = $langCode;
        $this->extension   = $extension;
    }
    
    public function getIterator() {
        $iterator = new \AppendIterator();
        // english pack is always empty since in default rdfs 
        if ($this->langCode != 'en-US') {
            foreach ($this->extension->getManifest()->getInstallModelFiles() as $rdfpath) {
                $modelId = FileModel::getModelIdFromXml($rdfpath);
                $candidate = $this->extension->getDir() . 'locales' . DIRECTORY_SEPARATOR . $this->langCode . DIRECTORY_SEPARATOR . basename($rdfpath) .'.po';
                if (file_exists($candidate)) {
                    $iterator->append($this->getTriplesFromFile($candidate, $modelId));
                }
            }
        }
        return $iterator;
    }
    
    protected function getTriplesFromFile($file, $modelId) {
    
        $translationFileReader = new tao_helpers_translation_POFileReader($file);
        $translationFileReader->read();
        $translationFile = $translationFileReader->getTranslationFile();
        /** @var  tao_helpers_translation_POTranslationUnit $tu */
        $triples = array();
        foreach ($translationFile->getTranslationUnits() as $tu) {
            $annotations = $tu->getAnnotations();
            $about = isset($annotations['po-translator-comments']) ? $annotations['po-translator-comments'] : null;
            if ($about && common_Utils::isUri($about) && in_array($tu->getContext(), array(RDFS_LABEL, RDFS_COMMENT))) {
                $triple = new \core_kernel_classes_Triple();
                $triple->subject = $about;
                $triple->predicate = $tu->getContext();
                $triple->object = $tu->getTarget() ? $tu->getTarget() : $tu->getSource();
                $triple->lg = $tu->getTargetLanguage();
                $triple->modelid = $modelId;
                $triples[] = $triple; 
            }
        }
        return new \ArrayIterator($triples);
    }
    
}

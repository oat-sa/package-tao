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

/**
 * An implementation of TranslationFileWriter aiming at writing JavaScript
 * files.
 *
 * @access public
 * @author Jerome Bogaerts
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 * @since 2.2
 
 */
class tao_helpers_translation_JSFileWriter extends tao_helpers_translation_TranslationFileWriter {

    /**
     * Write a javascript AMD module that provides translations 
     * for the target languages.
     *
     * @access public
     * @return mixed
     */
    public function write() {
        
        $path = $this->getFilePath();
        $strings = array();
        
        foreach ($this->getTranslationFile()->getTranslationUnits() as $tu) {
            if ($tu->getTarget() !== ''){
                $strings[$tu->getSource()] = $tu->getTarget();   
            }
        }

        $buffer = json_encode($strings, JSON_HEX_QUOT | JSON_HEX_APOS);
        if(!file_put_contents($path, $buffer)){
                throw new tao_helpers_translation_TranslationException("An error occured while writing Javascript " .
                                                                                                                           "translation file '${path}'.");
        }
        
    }

}

?>
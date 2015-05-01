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
 *
 */

/**
 * An implementation of TranslationFileReader aiming at reading PO files.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2

 * @version 1.0
 */
class tao_helpers_translation_POFileReader
    extends tao_helpers_translation_TranslationFileReader
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method read
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @throws tao_helpers_translation_TranslationException
     * @return mixed
     */
    public function read()
    {

        $file = $this->getFilePath();
        if (!file_exists($file)) {
            throw new tao_helpers_translation_TranslationException("The translation file '${file}' does not exist.");
        }

        // Create the translation file.
        $tf = new tao_helpers_translation_POFile();

        $fc = implode('',file($file));

        $matched = preg_match_all('/((?:#[\.:,\|]{0,1}\s+(?:.*?)\\n)*)'.
            '(msgctxt\s+(?:"(?:[^"]|\\\\")*?"\s*)+)?'.
            '(msgid\s+(?:"(?:[^"]|\\\\")*?"\s*)+)\s+' .
            '(msgstr\s+(?:"(?:[^"]|\\\\")*?(?<!\\\)"\s*)+)/',
            $fc, $matches);

        preg_match('/sourceLanguage: (.*?)\\n/s', $fc, $sourceLanguage);
        preg_match('/targetLanguage: (.*?)\\n/s', $fc, $targetLanguage);

        if (count($sourceLanguage)) {
            $tf->setSourceLanguage(substr($sourceLanguage[1],0,5));
        }
        if (count($targetLanguage)) {
            $tf->setTargetLanguage(substr($targetLanguage[1],0,5));
        }

        if ($matched) {

            for ($i = 0; $i < $matched; $i++) {

                $annotations = $matches[1][$i];
                $msgctxt = preg_replace('/\s*msgctxt\s*"(.*)"\s*/s','\\1',$matches[2][$i]);
                $msgid = preg_replace('/\s*msgid\s*"(.*)"\s*/s','\\1',$matches[3][$i]);
                $msgstr = preg_replace('/\s*msgstr\s*"(.*)"\s*/s','\\1',$matches[4][$i]);

                // Do not include meta data as a translation unit..
                if ($msgid !== ''){

                    // Sanitze the strings.
                    $msgid = tao_helpers_translation_POUtils::sanitize($msgid);
                    $msgstr = tao_helpers_translation_POUtils::sanitize($msgstr);
                    $msgctxt = tao_helpers_translation_POUtils::sanitize($msgctxt);
                    $tu = new tao_helpers_translation_POTranslationUnit();

                    // Set up source & target.
                    $tu->setSource($msgid);
                    if ($msgstr !== '') {
                        $tu->setTarget($msgstr);
                    }
                    if ($msgctxt){
                        $tu->setContext($msgctxt);
                    }

                    // Deal with annotations
                    $annotations = tao_helpers_translation_POUtils::unserializeAnnotations($annotations);
                    foreach ($annotations as $name => $value){
                        $tu->addAnnotation($name, $value);
                    }

                    $tf->addTranslationUnit($tu);
                }
			}
		}

		$this->setTranslationFile($tf);

    }

}
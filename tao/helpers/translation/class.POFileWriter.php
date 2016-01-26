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
 * An implementation of TranslationFileWriter aiming at writing PO files.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 
 * @version 1.0
 */
class tao_helpers_translation_POFileWriter
    extends tao_helpers_translation_TranslationFileWriter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method write
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function write()
    {
        
        $buffer = '';
        $file = $this->getTranslationFile();
        
        // Add PO Headers.
        $buffer .= 'msgid ""' . "\n";
        $buffer .= 'msgstr ""' . "\n";
        
        // If the TranslationFile is a specific POFile instance, we add PO Headers
        // to the output.
        if (get_class($this->getTranslationFile()) == 'tao_helpers_translation_POFile') {
        	foreach ($file->getHeaders() as $name => $value) {
        		$buffer .= '"' . $name . ': ' . $value . '\n"' . "\n";
        	}
        }
        
        // Write all Translation Units.
        $buffer .= "\n";
		foreach($this->getTranslationFile()->getTranslationUnits() as $tu) {
            
			$c = tao_helpers_translation_POUtils::sanitize($tu->getContext(), true);
			$s = tao_helpers_translation_POUtils::sanitize($tu->getSource(), true);
			$t = tao_helpers_translation_POUtils::sanitize($tu->getTarget(), true);
            $a = tao_helpers_translation_POUtils::serializeAnnotations($tu->getAnnotations());

            if (!empty($a)){
                $buffer .= "${a}\n";
            }

            if ($c) {
                $buffer .= "msgctxt \"{$c}\"\n";
            }

			$buffer .= "msgid \"{$s}\"\n";
			$buffer .= "msgstr \"{$t}\"\n";
			$buffer .= "\n";
		}
		
		return file_put_contents($this->getFilePath(), $buffer);
        
    }

}

?>
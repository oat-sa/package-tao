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
 * Short description of class tao_helpers_translation_POUtils
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_translation_POUtils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method sanitize
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string string
     * @param  boolean reverse
     * @return string
     */
    public static function sanitize($string, $reverse = false)
    {
        $returnValue = (string) '';

        
		if ($reverse) {
			$smap = array('"', "\n", "\t", "\r");
			$rmap = array('\\"', '\\n"' . "\n" . '"', '\\t', '\\r');
			$returnValue = (string) str_replace($smap, $rmap, $string);
		} else {
			$smap = array('/"\s+"/', '/\\\\n/', '/\\\\r/', '/\\\\t/', '/\\\"/');
			$rmap = array('', "\n", "\r", "\t", '"');
			$returnValue = (string) preg_replace($smap, $rmap, $string);
		}
        

        return (string) $returnValue;
    }

    /**
     * Unserialize PO message comments.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string annotations The PO message comments.
     * @return array
     */
    public static function unserializeAnnotations($annotations)
    {
        $returnValue = array();

        
        $matches = array();
        $e = TAO_DEFAULT_ENCODING;
        
        if (preg_match_all('/(#[\.\:,\|]{0,1}\s+(?:[^\\n]*))/', $annotations, $matches) !== false){
            if (isset($matches[1]) && count($matches[1] > 0)){
                foreach ($matches[1] as $match){
                    $match = trim($match);
                    $matchLen = mb_strlen($match, $e);
                    $annotationId = null;
                    $annotationValue = null;
                    
                    switch (mb_substr($match, 1, 1, $e)){
                        case "\t":
                        case ' ':
                            // Translator comment.
                            $annotationId = tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS;
                            $annotationValue = mb_substr($match, 2, $matchLen - 2, $e);
                        break;
                        
                        case '.':
                            $annotationId = tao_helpers_translation_POTranslationUnit::EXTRACTED_COMMENTS;
                            $annotationValue = mb_substr($match, 3, $matchLen - 3, $e);
                        break;
                        
                        case ':':
                            $annotationId = tao_helpers_translation_POTranslationUnit::REFERENCE;
                            $annotationValue = mb_substr($match, 3, $matchLen - 3, $e);
                        break;
                        
                        case ',':
                            $annotationId = tao_helpers_translation_POTranslationUnit::FLAGS;
                            $annotationValue = mb_substr($match, 3, $matchLen - 3, $e);
                        break;
                        
                        case '|':
                            if (($pos = mb_strpos($match, 'msgid_plural', 0, $e)) !== false){
                                $pos += mb_strlen('msgid_plural', $e) + 1;
                                $annotationId = tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID_PLURAL;
                                $annotationValue = mb_substr($match, $pos, $matchLen - $pos, $e);
                            }
                            else if(($pos = mb_strpos($match, 'msgid', 0, $e)) !== false){
                                $pos += mb_strlen('msgid', $e) + 1;
                                $annotationId = tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID;
                                $annotationValue = mb_substr($match, $pos, $matchLen - $pos, $e);
                            }
                            else if(($pos = mb_strpos($match, 'msgctxt', 0, $e)) !== false){
                                $pos += mb_strlen('msgctxt', $e) + 1;
                                $annotationId = tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGCTXT;
                                $annotationValue = mb_substr($match, $pos, $matchLen - $pos, $e);
                            }
                        break;
                    }
                    
                    if ($annotationId != null && $annotationValue != null){
                        if (!isset($returnValue[$annotationId])){
                            $returnValue[$annotationId] = $annotationValue;
                        }
                        else{
                            $returnValue[$annotationId] .= "\n${annotationValue}";
                        }
                    }
                }
            }
            
        }
        else{
            throw new tao_helpers_translation_TranslationException("An error occured while unserializing annotations '${annotations}'.");
        }
        

        return (array) $returnValue;
    }

    /**
     * Serialize an array of annotations in a PO compliant comments format.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array annotations An array of annotations where keys are annotation identifiers and values are annotation values.
     * @return string
     */
    public static function serializeAnnotations($annotations)
    {
        $returnValue = (string) '';

        
        // Buffer will contain each line of the serialized PO comment block.
        $buffer = array();
        
        foreach ($annotations as $name => $value){
            $prefix = null;
            
            switch ($name){
                case tao_helpers_translation_POTranslationUnit::TRANSLATOR_COMMENTS:
                    $prefix = '#';
                break;
                
                case tao_helpers_translation_POTranslationUnit::EXTRACTED_COMMENTS:
                    $prefix = '#.';    
                break;
                
                case tao_helpers_translation_POTranslationUnit::REFERENCE:
                    $prefix = '#:';
                break;
                
                case tao_helpers_translation_POTranslationUnit::FLAGS:
                    $prefix = '#,';
                break;
                
                case tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID:
                case tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGID_PLURAL:
                case tao_helpers_translation_POTranslationUnit::PREVIOUS_MSGCTXT:
                    $prefix = '#|';
                break;
            }
            
            if ($prefix !== null){
                // We have a PO compliant annotation that we have to serialize.
                foreach(explode("\n", $value) as $v){
                    $buffer[] = "${prefix} ${v}";
                }
            }
        }

        // Glue the annotation lines in a single PO comment block.
        $returnValue = implode("\n", $buffer);
        

        return (string) $returnValue;
    }

    /**
     * Append a flag to an existing PO comment flag value.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string comment A PO flag comment value in which you have to add the new flag.
     * @param  string flag The flag to add to the existing $comment.
     * @return string
     */
    public static function addFlag($comment, $flag)
    {
        $returnValue = (string) '';

        
        $returnValue = $comment;
        $flag = trim($flag);
        
        if (mb_strpos($returnValue, $flag, 0, TAO_DEFAULT_ENCODING) === false){
            $returnValue .= ((mb_strlen($returnValue, TAO_DEFAULT_ENCODING) > 0) ? " ${flag}" : $flag);
        }
        

        return (string) $returnValue;
    }

}

?>
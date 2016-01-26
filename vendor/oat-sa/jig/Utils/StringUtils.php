<?php

/**
 * This file is part of the Jig package.
 *
 * Copyright (c) 04-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Utils;

/**
 * StringUtils
 */
class StringUtils
{

    /**
     * Figures out whether a string is binary or not
     *
     * @param string $string
     * @return bool
     */
    public static function isBinary($string)
    {
        $string = str_replace(array("\n", "\r", "\t"), '', $string);
        return !ctype_print($string);
    }

    /**
     * Remove the byte order mark from a string if applicable
     *
     * @param string $string
     * @return string
     */
    public static function removeBom($string)
    {
        if (substr($string, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $string = substr($string, 3);
        }
        return $string;
    }


    /**
     * In French orthography some marks are preceded by a space which can lead to unwanted line breaks
     *
     * @param string $string
     * @param string $replacement
     * @return string
     */
    public static function avoidFrenchLineBreak($string, $replacement = '&nbsp;')
    {
        // first character in expression is thin space U+2009
        return preg_replace('~(\x{2009}| )([:!?;…€])~u', $replacement . '$2', $string);
    }

    /**
     * This function removes all special characters from a string. They are replaced by $replacement,
     * multiple $replacement are replaced by just one, $replacement is also trimmed from the beginning
     * and the end of the string.
     *
     * @param string $string the original text
     * @param string $replacement the replacement, - by default
     * @param bool $lower return string in lower case, true by default
     * @return string $string the modified string
     */
    public static function removeSpecChars($string, $replacement = '-', $lower = true)
    {
        $specChars = array(
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'Ae',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'E',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'Oe',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'Ue',
            'Ý' => 'Y',
            'Þ' => 'T',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'ae',
            'å' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'e',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'oe',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'ue',
            'ý' => 'y',
            'þ' => 't',
            'ÿ' => 'y',
            '_' => $replacement
        );
        $string    = strtr($string, $specChars);
        $string    = trim(preg_replace('~\W+~u', $replacement, $string), $replacement);
        return $lower ? strtolower($string) : $string;
    }

    /**
     * Convert a string with spaces or underscores to camelCase
     *
     * @param string $string
     * @param bool $firstToUpper
     * @return string
     */
    public static function camelize($string, $firstToUpper = false)
    {
        $string = 'x' . strtolower(trim($string));
        $string = ucwords(preg_replace('/[\s_-]+/', ' ', $string));
        $string = substr(str_replace(' ', '', $string), 1);
        return $firstToUpper ? ucfirst($string) : $string;
    }

    /**
     * Returns the given camelCasedWord as an underscored_word.
     * This is borrowed from the CakePHP framework
     *
     * @param string $camelCasedWord Camel-cased word to be "underscorized"
     * @return string Underscore-syntaxed version of the $camelCasedWord
     */
    public static function underscorize($camelCasedWord)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
    }


    /**
     * Quote a value (mainly for usage in CSV)
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function csvQuote($value, array $options = array())
    {
        $options = array_merge(
            array(
                'enclosure' => '"',
                'escape'    => '\\'
            ),
            $options
        );
        if (!is_numeric($value) && !is_bool($value) && !is_null($value) && !in_array(
                strtolower($value),
                array( 'true', 'false', 'null' )
            )
        ) {
            $value = str_replace($options['enclosure'], $options['escape'] . $options['enclosure'], $value);
            return $options['enclosure'] . $value . $options['enclosure'];
        }
        switch (true) {
            case is_null($value):
                return 'null';

            case false === $value:
                return 'false';

            case true === $value:
                return 'true';

            default:
                return $value;
        }

    }

    /**
     * Encodes text randomly to html entities of different styles
     * This code comes from Symfony 1.4
     *
     * @param string $text
     * @return string
     */
    public static function encodeText($text)
    {
        $encoded_text = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text{$i};
            $r    = rand(0, 100);

            # roughly 10% raw, 45% hex, 45% dec
            # '@' *must* be encoded. I insist.
            if ($r > 90 && $char != '@') {
                $encoded_text .= $char;
            } else if ($r < 45) {
                $encoded_text .= '&#x' . dechex(ord($char)) . ';';
            } else {
                $encoded_text .= '&#' . ord($char) . ';';
            }
        }
        return $encoded_text;
    }

    /**
     * Split long parts of the string to equal length chunks, multibyte safe
     *
     * @param $input string to be processed
     * @param string $threshold
     * @param string $glue
     *
     * @return string
     */
    static public function wrapLongWords( $input, $threshold = '20', $glue = ' ' )
    {
        $tokens = explode( $glue, $input );
        $result = array();

        foreach ($tokens as $str) {
            $mblen = mb_strlen( $str );
            if ($mblen < $threshold) {
                $result[] = $str;
                continue;
            }
            $array = array();
            for ($i = 0; $i < $mblen; $i ++) {
                $array[] = mb_substr( $str, $i, 1 );
            }
            $n   = 0;
            $new = '';
            foreach ($array as $char) {
                if ($n <= $threshold) {
                    $new .= $char;
                } else {
                    $result[] = $new;
                    $new      = $char;
                    $n        = 0;
                }
                $n ++;
            }
            if (end( $result ) !== $new) {
                $result[] = $new;
            }
        }

        return implode( $glue, $result );
    }

}

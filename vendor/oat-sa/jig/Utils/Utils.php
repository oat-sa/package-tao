<?php

/**
 * This file is part of the Jig package.
 *
 * Copyright (c) 03-Mar-2013 Dieter Raber <me@dieterraber.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jig\Utils;

/**
 * Collection of basic utility functions
 */
class Utils
{


    /**
     * Create a random password
     *
     * @param array $settings
     * @setting bool length, number of characters, default 8
     * @setting bool upper, whether or not to include upper case characters, default true
     * @setting bool lower, whether or not to include lower case characters, default true
     * @setting bool number, whether or not to include numbers, default true
     * @setting bool spec, whether or not to include special characters, default false
     * @return string $password
     */
    public static function createPassword(array $settings = array())
    {
        $defaults = array(
            'length' => 8,
            'upper'  => true,
            'lower'  => true,
            'number' => true,
            'spec'   => false
        );

        $settings   = array_merge($defaults, $settings);
        $characters = array(
            'upper'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'lower'  => 'abcdefghijklmnopqrstuvwxyz',
            'number' => '0123456789',
            'spec'   => '!$*@-+'
        );
        $password   = '';
        $availChars = '';
        foreach ($characters as $key => $value) {
            if ($settings[$key]) {
                $availChars .= $value;
            }
        }
        $avail_length = strlen($availChars);
        for ($i = 0; $i < $settings['length']; $i++) {
            $password .= $availChars{mt_rand(0, $avail_length - 1)};
        }
        return $password;
    }

    /**
     * Retrieve php ini settings in bytes
     * Refer to http://www.php.net/manual/en/function.ini-get.php
     * for more information
     *
     * @param string $key , the key in the php ini file
     * @return int $value, the value in bytes
     */
    public static function iniGetBytes($key)
    {
        $value = ini_get(trim($key));
        $last  = strtolower($value[strlen($value) - 1]);
        switch ($last) {
            // The 'g' modifier is available since PHP 5.1.0
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        return $value;
    }

    /**
     * Convert an object to an array
     *
     * @param object $object , the object to convert
     * @return array
     */
    public static function objectToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }
        return array_map(array(self, 'objectToArray'), $object);
    }

}

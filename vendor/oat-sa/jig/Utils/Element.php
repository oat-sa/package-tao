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
 * Element Generator
 */
class Element
{

    protected static $instance;

    protected static $exceptions = array(
        'autoclose' => array(
            'br',
            'img',
            'hr',
            'meta',
            'input',
            'link',
            'base'
        ),
        'wrap-link' => array(
            'table',
            'ul',
            'dl',
            'ol',
            'img'
        ),
        'href'      => array(
            'a',
            'link',
            'base'
        ),
    );

    /**
     * Global setup of the class
     * You can either override them in three ways
     * 1. by using an instance of the class and reset them globally (!implemented yet)
     * 2. by submitting two arrays as arguments for each function call,
     *    the first being attributes, the second one for settings
     * 3. by submitting only one array with everything, the application will sort them out
     */
    protected static $settings = array(
        // global settings, the space in the key avoids problems with XML elements
        // i.e. the could not be called <global settings />
        'global settings' => array(
            'content'          => '',
            'content-position' => '><',
            'indent'           => 2, // indentation, false for none, spaces or tabs are also fine
        ),
        // default setup for certain elements
        'a'               => array(
            'settings' => array(
                'content-position' => 'href'
            )
        ),
        'area'            => array(
            'settings' => array(
                'content-position' => 'href'
            )
        ),
        'form'            => array(
            'attributes' => array(
                'method'  => 'get',
                'enctype' => 'application/x-www-form-urlencoded',
                //'action' => $_SERVER['REQUEST_URI'], see __call() for details
            )
        ),
        'iframe'          => array(
            'attributes' => array(
                'frameborder' => 0,
                'src'         => 'about:blank',
                'style'       => 'border:none'
            ),
            'settings'   => array(
                'content-position' => 'src',
                'empties'          => array( // attributes that can be empty
                    'frameborder'
                )
            )
        ),
        'img'             => array(
            'attributes' => array(
                'alt' => ''
            ),
            'settings'   => array(
                'empties'          => array( // attributes that can be empty
                    'alt'
                ),
                'content-position' => 'src'
            )
        ),
        'input'           => array(
            'settings'   => array(
                'empties'          => array(
                    'value'
                ),
                'content-position' => 'value'
            ),
            'attributes' => array(
                'type' => 'text'
            )
        ),
        'link'            => array(
            'settings' => array(
                'content-position' => 'href'
            )
        ),
        'option'          => array(
            'settings' => array(
                'empties'          => array(
                    'value'
                ),
                'content-position' => 'value'
            )
        ),
        'script'          => array(
            'settings' => array(
                'content-position' => 'src'
            )
        ),
        'textarea'        => array(
            'attributes' => array(
                'cols' => 35,
                'rows' => 7
            )
        )
    );

    protected function __construct()
    {
    }


    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function __callStatic($element, $attributes)
    {
        $instance   = self::getInstance();
        $attributes = $attributes[0];
        if (is_string($attributes)) {
            $attributes = array('content' => $attributes);
        }
        return $instance->$element((array)$attributes);
    }

    public function __call($element, $arguments = array())
    {

        if ($element === 'input' && !empty($arguments[0]['type']) && $arguments[0]['type'] === 'textarea') {
            unset($arguments[0]['type']);
            $element = 'textarea';
        }

        $arguments[1]['element'] = $element;

        $element_setup = self::prepare($arguments[0], $arguments[1]);


        $specialMethod = $arguments[1]['element'] . 'Element';
        if (method_exists(__CLASS__, $specialMethod)) {
            return self::$specialMethod(
                $element_setup['attributes'],
                $element_setup['settings']
            );
        } else {
            return self::build(
                $element_setup['attributes'],
                $element_setup['settings']
            );
        }
    }


    /**
     * Prepare element - build element settings using global settings
     *
     * @param mixed $attributes , can be a string, an array of attributes or an array with both attributes and settings
     * @param array $settings , settings if not submitted via $arguments
     * @return array
     */
    protected static function prepare($attributes = array(), $settings = array())
    {

        // assign default form action since $_SERVER cannot be used in $instance
        if ($settings['element'] == 'form'
            && !isset(self::$settings['form']['attributes']['action'])
        ) {
            self::$settings['form']['attributes']['action'] = $_SERVER['REQUEST_URI'];
        }

        if (is_numeric(self::$settings['global settings']['indent'])) {
            self::$settings['global settings']['indent'] = str_repeat(
                ' ',
                self::$settings['global settings']['indent']
            );
        }

        // element preset
        $element_setup = !empty(self::$settings[$settings['element']])
            ? self::$settings[$settings['element']]
            : array();

        // merge global settings, elemnet settings and submitted settings
        $element_setup['settings'] = !empty($element_setup['settings'])
            ? array_merge(self::$settings['global settings'], $element_setup['settings'], $settings)
            : array_merge(self::$settings['global settings'], $settings);

        // allow single string as argument, this will be by default the content
        if (!empty($attributes) && is_string($attributes)) {
            $attributes = array(
                'content' => $attributes
            );
        }

        // stuff submitted as attributes
        $element_setup['attributes'] = !empty($element_setup['attributes'])
            ? array_merge($element_setup['attributes'], $attributes)
            : $attributes;

        // copy 'attributes' that are actually settings over
        $attr_settings             = array_intersect_key($element_setup['attributes'], $element_setup['settings']);
        $element_setup['settings'] = array_merge($element_setup['settings'], $attr_settings);

        // remove the above from attributes
        $element_setup['attributes'] = array_diff_key($element_setup['attributes'], $attr_settings);

        return $element_setup;
    }


    /**
     * Build element
     *
     * @param array $attributes - HTML attributes only
     * @param array $settings - other arguments
     * @return string $element
     */
    protected static function build($attributes, $settings)
    {

        if (empty($settings['element'])) {
            return false;
        }


        if (!empty($attributes['href']) && !self::allowsHref($settings['element'])) {

            $href = $attributes['href'];
            unset($attributes['href']);

            if (self::hasWrapLink($settings['element'])) {
                $content    = self::build($attributes, $settings);
                $attributes = array(
                    'href' => $href
                );
                $settings   = array(
                    'element' => 'a',
                    'content' => $content
                );
            } else {
                $settings['content'] = self::aElement(
                    array(
                        'href'    => $href,
                        'content' => $settings['content']
                    )
                );
            }
        }

        $content_position = self::getContentPosition($settings['element']);
        // next line can not use !empty as this is only true for empty strings and not 0 for instance
        if (isset($settings['content'])
            && $settings['content'] !== ''
            && $content_position !== '><'
            && empty($attributes[$content_position])
        ) {
            $attributes[$content_position] = strip_tags($settings['content']);

            // in 'a', content and href can be identical
            if ($settings['element'] !== 'a') {
                $settings['content'] = '';
            }
        } else if (!self::isAutoClose($settings['element'])) {
            $settings['content'] = StringUtils::avoidFrenchLinebreak($settings['content']);
        }

        $element = '<' . $settings['element']
            . self::attributes($attributes, $settings)
            // auto closed elements can not have content
            . (self::isAutoClose($settings['element'])
                ? ' />' . "\n"
                : '>' . $settings['content']
                . '</' . $settings['element'] . '>' . "\n");

        return $element;
    }


    /**
     * Build attribute string
     *
     * @param array $attributes
     * @param array $settings - other arguments
     * @return string $attribute_str
     */
    protected static function attributes($attributes, $settings)
    {
        $attribute_str = '';
        foreach ($attributes as $key => $value) {

            // handles selected and such
            if ($value === true) {
                $attribute_str .= ' ' . $key;
            } else {
                // attributes without a value and not allowed to be empty
                if ($value === '' && (empty($settings['empties']) || !in_array($key, $settings['empties']))) {
                    continue;
                } // this is thought as an override of default values
                else if ($value === false || $value === null) {
                    continue;
                }
                $attribute_str .= ' ' . $key . '="' . trim($value) . '"';
            }

        }
        return $attribute_str;
    }

    /**
     * Configure a hyperlink
     *
     * @param $attributes
     * @param array $settings
     * @return string the linked text
     */
    protected static function aElement($attributes, $settings = array())
    {
        if (empty($attributes['href'])) {
            $attributes['href'] = strip_tags($settings['content']);
        }

        if (empty($settings['content'])) {
            $settings['content'] = $attributes['href'];
        }

        $href_content_eq = $settings['content'] === $attributes['href'];

        // emails but not urls with password
        if (strpos($attributes['href'], '@') !== false && strpos($attributes['href'], '//') === false) {
            $address            = str_replace('mailto:', '', $attributes['href']);
            $attributes['href'] = StringUtils::encodeText('mailto:' . $address);
            if ($href_content_eq) {
                $settings['content'] = StringUtils::encodeText($address);
            }
        } else if ($href_content_eq && strpos($settings['content'], '//') !== false) {
            $settings['content'] = substr($settings['content'], strpos($settings['content'], '//') + 2);
            if (substr_count($settings['content'], '/') === 1) {
                $settings['content'] = rtrim($settings['content'], '/');
            }
        } else if (preg_match('~^(\(|\+){0,2}[\d\)\- ]{6,}+$~', $settings['content'])) {
            $protocol           = 'tel';
            $attributes['href'] = str_replace(array('tel:', ' ', ')', '(', '-'), '', $attributes['href']);
            if (strpos($attributes['href'], '00') !== false) {
                $attributes['href'] = '+' . substr($attributes['href'], 2);
            }

            $attributes['href'] = $protocol . ':' . str_replace(array('tel:', ' ', ')', '('), '', $attributes['href']);
        }
        return self::build($attributes, $settings);
    }

    /**
     * Add a css class
     *
     * @param $attributes
     * @param string $class the class to add
     * @internal param array $settings
     * @return array $settings
     */
    public static function addClass($attributes, $class)
    {
        if (!empty($attributes['class'])) {
            $attributes['class'] = explode(' ', $attributes['class']);
            foreach ($attributes['class'] as $key => $value) {
                $attributes['class'][$key] = trim($value);
            }
            $attributes['class'] = array_unique($attributes['class']);
            $attributes['class'] = implode(' ', $attributes['class']);
        } else {
            $attributes['class'] = $class;
        }
        return $attributes;
    }

    protected static function getContentPosition($element)
    {
        if (!empty(self::$settings[$element]['settings']['content-position'])) {
            return self::$settings[$element]['settings']['content-position'];
        }
        return '><';
    }

    protected static function hasWrapLink($element)
    {
        return in_array($element, self::$exceptions['wrap-link']);
    }

    protected static function isAutoClose($element)
    {
        return in_array($element, self::$exceptions['autoclose']);
    }

    protected static function allowsHref($element)
    {
        return in_array($element, self::$exceptions['href']);
    }

}
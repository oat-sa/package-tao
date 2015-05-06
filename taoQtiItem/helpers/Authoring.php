<?php
/*
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\helpers;

//use oat\taoQtiItem\helpers\Authoring;
use common_Logger;
use DOMDocument;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use oat\taoQtiItem\model\qti\Parser;

/**
 * Helper to provide methods for QTI authoring
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 */
class Authoring
{

    public static function normalizeAuthoringElementKey($text){
        return strtolower(preg_replace('~[\W]+~', '-', $text));
    }

    public static function getAvailableAuthoringElements(){

        $elements = array(
            //other possibilities : basic, common, usual ... 
            'Common Interactions' => array(
                array('title' => __('Choice Interaction'),
                    'icon' => 'choice',
                    'short' => __('Choice'),
                    'qtiClass' => 'choiceInteraction'
                ),
                array('title' => __('Order Interaction'),
                    'icon' => 'order',
                    'short' => __('Order'),
                    'qtiClass' => 'orderInteraction'
                ),
                array('title' => __('Associate Interaction'),
                    'icon' => 'associate',
                    'short' => __('Associate'),
                    'qtiClass' => 'associateInteraction'
                ),
                array('title' => __('Match Interaction'),
                    'icon' => 'match',
                    'short' => __('Match'),
                    'qtiClass' => 'matchInteraction'
                ),
                array('title' => __('Hottext Interaction'),
                    'icon' => 'hottext',
                    'short' => __('Hottext'),
                    'qtiClass' => 'hottextInteraction'
                ),
                array('title' => __('Gap Match Interaction'),
                    'icon' => 'gap-match',
                    'short' => __('Gap Match'),
                    'qtiClass' => 'gapMatchInteraction'
                ),
                array('title' => __('Slider Interaction'),
                    'icon' => 'slider',
                    'short' => __('Slider'),
                    'qtiClass' => 'sliderInteraction'
                ),
                array('title' => __('Extended Text Interaction'),
                    'icon' => 'extended-text',
                    'short' => __('Extended Text'),
                    'qtiClass' => 'extendedTextInteraction'
                ),
                array('title' => __('Upload Interaction'),
                    'icon' => 'upload',
                    'short' => __('File Upload'),
                    'qtiClass' => 'uploadInteraction'
                ),
                array('title' => __('Media Interaction'),
                    'icon' => 'media',
                    'short' => __('Media'),
                    'qtiClass' => 'mediaInteraction'
                )
            ),
            'Inline Interactions' => array(
                array('title' => __('Text Block'),
                    'icon' => 'font',
                    'short' => __('Block'),
                    'qtiClass' => '_container', //a pseudo class introduced in TAO
                ),
                array('title' => __('Inline Choice Interaction'),
                    'icon' => 'inline-choice',
                    'short' => __('Inline Choice'),
                    'qtiClass' => 'inlineChoiceInteraction',
                    'sub-group' => 'inline-interactions' // creates a panel with a subgroup for this element
                ),
                array('title' => __('Text Entry Interaction'),
                    'icon' => 'text-entry',
                    'short' => __('Text Entry'),
                    'qtiClass' => 'textEntryInteraction',
                    'sub-group' => 'inline-interactions'
                ),
            ),
            'Graphic Interactions' => array(
                array('title' => __('Hotspot Interaction'),
                    'icon' => 'hotspot',
                    'short' => __('Hotspot'),
                    'qtiClass' => 'hotspotInteraction'
                ),
                array('title' => __('Graphic Order Interaction'),
                    'icon' => 'graphic-order',
                    'short' => __('Graphic Order'),
                    'qtiClass' => 'graphicOrderInteraction'
                ),
                array('title' => __('Graphic Associate Interaction'),
                    'icon' => 'graphic-associate',
                    'short' => __('Graphic Associate'),
                    'qtiClass' => 'graphicAssociateInteraction'
                ),
                array('title' => __('Graphic Gap Interaction'),
                    'icon' => 'graphic-gap',
                    'short' => __('Graphic Gap'),
                    'qtiClass' => 'graphicGapMatchInteraction'
                ),
                array('title' => __('Select Point Interaction'),
                    'icon' => 'select-point',
                    'short' => __('Select Point'),
                    'qtiClass' => 'selectPointInteraction'
                )
            )
            /*
            , 'Block Containers' => array(
                array('title' => __('Text Block'),
                    'icon' => 'font',
                    'short' => __('Block'),
                    'qtiClass' => '_container' //a pseudo class introduced in TAO
                ),
                array('title' => __('Rubric Block'),
                    'icon' => 'rubric',
                    'short' => __('Rubric'),
                    'qtiClass' => 'rubricBlock'
                )
            )*/
        );
        foreach($elements as &$valueArr){
            foreach($valueArr as &$values){
                if(!isset($values['disabled'])){
                    $values['disabled'] = false;
                }
                if(!isset($values['sub-group'])){
                    $values['sub-group'] = '';
                }
            }
        }

        return $elements;
    }

    public static function validateQtiXml($qti){

        $returnValue = '';

        // render and clean the xml
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->validateOnParse = false;

        if($dom->loadXML($qti)){
            $returnValue = $dom->saveXML();

            //in debug mode, systematically check if the save QTI is standard compliant
            if(DEBUG_MODE){
                $parserValidator = new Parser($returnValue);
                $parserValidator->validate();
                if(!$parserValidator->isValid()){
                    common_Logger::w('Invalid QTI output: '.PHP_EOL.' '.$parserValidator->displayErrors());
                    common_Logger::d(print_r(explode(PHP_EOL, $returnValue), true));
                    throw new QtiModelException('invalid QTI item XML '.PHP_EOL.' '.$parserValidator->displayErrors());
                }
            }
        }else{
            $parserValidator = new Parser($qti);
            $parserValidator->validate();
            if(!$parserValidator->isValid()){
                throw new QtiModelException('Wrong QTI item output format');
            }
        }

        return (string) $returnValue;
    }

}

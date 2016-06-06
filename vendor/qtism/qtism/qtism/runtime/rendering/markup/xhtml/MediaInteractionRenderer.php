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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\rendering\markup\xhtml;

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * MediaInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with the 'qti-blockInteraction' and 'qti-mediaInteraction' additional CSS classes.
 * 
 * * If the object type describes a video media, a <video> tag will be appended to the rendering.
 * * If the object type describes an audio media, an <audio> tag will be appended to the rendering.
 * * If the object type describe an image media, an <img> tag will be appended to the rendering.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * * data-autostart = qti:mediaInteraction->autostart
 * * data-min-plays = qti:mediaInteraction->minPlays
 * * data-max-plays = qti:mediaInteraction->maxPlays
 * * data-loop = qti:mediaInteraction->loop
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MediaInteractionRenderer extends InteractionRenderer {
    
    /**
     * An array representing the supported audio mime-types.
     * 
     * @var array
     */
    private $audioTypes = array();
    
    /**
     * An array representing the supported video mime-types.
     * 
     * @var array
     */
    private $videoTypes = array();
    
    /**
     * An array representing the supported image mime-types.
     * 
     * @var array
     */
    private $imageTypes = array();
    
    /**
     * Set the array of supported audio mime-types.
     *
     * @param array $audioTypes An array of strings representing mime-types.
     */
    protected function setAudioTypes(array $audioTypes) {
        $this->audioTypes = $audioTypes;
    }
    
    /**
     * Get the array of supported audio mime-types.
     *
     * @return array An array of strings representing mime-types.
     */
    protected function getAudioTypes() {
        return $this->audioTypes;
    }
    
    /**
     * Set the array of supported video mime-types.
     *
     * @param array $videoTypes An array of strings representing mime-types.
     */
    protected function setVideoTypes(array $videoTypes) {
        $this->videoTypes = $videoTypes;
    }
    
    /**
     * Get the array of supported video mime-types.
     *
     * @return array An array of strings representing mime-types.
     */
    protected function getVideoTypes() {
        return $this->videoTypes;
    }
    
    /**
     * Set the array of supported image mime-types.
     *
     * @param array $imageTypes An array of strings representing mime-types.
     */
    protected function setImageTypes(array $imageTypes) {
        $this->imageTypes = $imageTypes;
    }
    
    /**
     * Get the array of supported image mime-types.
     *
     * @return array An array of strings representing mime-types.
     */
    protected function getImageTypes() {
        return $this->imageTypes;
    }
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->setVideoTypes(array('video/mp4', 'video/webm', 'video/ogg'));
        $this->setAudioTypes(array('audio/mpeg', 'audio/ogg', 'audio/wav'));
        $this->setImageTypes(array('image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/x-bmp'));
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-mediaInteraction');
        
        $fragment->firstChild->setAttribute('data-autostart', ($component->mustAutostart() === true) ? 'true' : 'false');
        $fragment->firstChild->setAttribute('data-min-plays', $component->getMinPlays());
        $fragment->firstChild->setAttribute('data-max-plays', $component->getMaxPlays());
        $fragment->firstChild->setAttribute('data-loop', ($component->mustLoop() === true) ? 'true' : 'false');
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        $width = null;
        $height = null;
        if ($component->getObject()->hasWidth() === true) {
            $width = $component->getObject()->getWidth();
        }
        
        if ($component->getObject()->hasHeight() === true) {
            $height = $component->getObject()->getHeight();
        }
        
        $media = null;
        
        if (in_array($component->getObject()->getType(), $this->getVideoTypes()) === true) {
            // Transform the object element representing the video.
            $media = $fragment->ownerDocument->createElement('video');
            $source = $fragment->ownerDocument->createElement('source');
            $source->setAttribute('type', $component->getObject()->getType());
            $source->setAttribute('src', $component->getObject()->getData());
            $media->appendChild($source);
        }
        else if (in_array($component->getObject()->getType(), $this->getAudioTypes()) === true) {
            $media = $fragment->ownerDocument->createElement('audio');
            $source = $fragment->ownerDocument->createElement('source');
            $source->setAttribute('type', $component->getObject()->getType());
            $source->setAttribute('src', $component->getObject()->getData());
            $media->appendChild($source);
        }
        else if (in_array($component->getObject()->getType(), $this->getImageTypes()) === true) {
            $media = $fragment->ownerDocument->createElement('img');
            $media->setAttribute('src', $component->getObject()->getData());
        }
        
        if (empty($media) !== true) {
            // Search for the <object> to be replaced.
            $objects = $fragment->firstChild->getElementsByTagName('object');
            $fragment->firstChild->replaceChild($media, $objects->item(0));
            
            if (empty($width) !== true) {
                $media->setAttribute('width', $width);
            }
            
            if (empty($height) !== true) {
                $media->setAttribute('height', $height);
            }
        }
    }
}
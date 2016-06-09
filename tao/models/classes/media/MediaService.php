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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */
namespace oat\tao\model\media;

/**
 * Service to manage the media sources
 * 
 * To be used as if it were a singleton until serviceManager in place
 */
class MediaService {
    
    /**
     * Scheme name used to identify media resource URLs
     * 
     * @var string
     */
    const SCHEME_NAME = 'taomedia';

    const CONFIG_KEY = 'mediaSources';
    
    private static $instance;
    
    /**
     * @return \oat\tao\model\media\MediaService
     */
    public static function singleton()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    
    /**
     * @var array
     */
    private $mediaSources = null;
    
    /**
     * Return all configured media sources
     * 
     * @return MediaBrowser
     */
    protected function getMediaSources()
    {
        if (is_null($this->mediaSources)) {
            $this->mediaSources = array();
            
            $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            $sources = $tao->getConfig(self::CONFIG_KEY);
            $sources = is_array($sources) ? $sources : array();
            
            foreach($sources as $mediaSourceId => $mediaSource){
                $this->mediaSources[$mediaSourceId] = is_object($mediaSource)
                    ? $mediaSource
                    : new $mediaSource(array());
            }
        }
        return $this->mediaSources;
    }
    
    /**
     * Returns the media source specified by $mediaSourceId
     * 
     * @param string $mediaSourceId
     * @throws \common_Exception
     * @return MediaBrowser
     */
    public function getMediaSource($mediaSourceId)
    {
        $sources = $this->getMediaSources();
        if (!isset($sources[$mediaSourceId])) {
            throw new \common_Exception('Media Sources Configuration for source '.$mediaSourceId.' not found');
        }
        return $sources[$mediaSourceId];
    }

    /**
     * Returns all media sources that are browsable 
     * 
     * @return MediaBrowser[]
     */
    public function getBrowsableSources()
    {
        $returnValue = array();
        foreach ($this->getMediaSources() as $id => $source) {
            if ($source instanceof MediaBrowser) {
                $returnValue[$id] = $source;
            }
        }
        return $returnValue;
    }

    /**
     * Returns all media sources that can write
     *
     * @return MediaManagement[]
     */
    public function getWritableSources()
    {
        $returnValue = array();
        foreach ($this->getMediaSources() as $id => $source) {
            if ($source instanceof MediaManagement) {
                $returnValue[$id] = $source;
            }
        }
        return $returnValue;
    }
    
    /**
     * Adds a media source to Tao
     * 
     * WARNING: Will always add the mediasource as 'mediamanager' as other 
     * identifiers are not supported by js widget
     * 
     * @param MediaBrowser $source
     * @return boolean
     */
    public function addMediaSource(MediaBrowser $source)
    {
        // ensure loaded
        $this->getMediaSources();
        // only mediaSource called 'mediamanager' supported
        $this->mediaSources['mediamanager'] = $source;
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        return $tao->setConfig(self::CONFIG_KEY, $this->mediaSources);
    }
    
    /**
     * Removes a media source for tao
     * 
     * @param string $sourceId
     * @return boolean
     */
    public function removeMediaSource($sourceId)
    {
        // ensure loaded
        $this->getMediaSources();
        unset($this->mediaSources[$sourceId]);
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        return $tao->setConfig(self::CONFIG_KEY, $this->mediaSources);
    }
} 
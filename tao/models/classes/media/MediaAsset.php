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
 * Represents a media asset
 */
class MediaAsset
{
    /**
     * @var MediaBrowser
     */
    protected $mediaSource = null;
    
    /**
     * @var string
     */
    protected $mediaId;

    /**
     * Create a new representation of a media asset
     * 
     * @param MediaBrowser $mediaSource
     * @param string $mediaId
     */
    public function __construct(MediaBrowser $mediaSource, $mediaId)
    {
        $this->mediaSource = $mediaSource;
        $this->mediaId = $mediaId;
    }
    
    /**
     * Returns the source of the asset
     * 
     * @return MediaBrowser
     */
    public function getMediaSource()
    {
        return $this->mediaSource;
    }
    
    /**
     * Gets the identifier to be used in the context of
     * the assets mediasource
     * 
     * @return string
     */
    public function getMediaIdentifier()
    {
        return $this->mediaId;
    }

}

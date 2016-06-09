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

use oat\tao\model\media\sourceStrategy\HttpSource;
/**
 * Base Media Resolver, that transforms an URL into a
 * Media Asset
 */
class TaoMediaResolver
{
    
    /**
     * Resolve a taomedia url to a media asset
     * 
     * @param string $url
     * @throws \Exception
     * @return \oat\tao\model\media\MediaAsset
     */
    public function resolve($url)
    {
        $urlParts = parse_url($url);
        if (isset($urlParts['scheme']) && $urlParts['scheme'] === MediaService::SCHEME_NAME && isset($urlParts['host'])) {
            $mediaService = new MediaService();
            $mediaSource = $mediaService->getMediaSource($urlParts['host']);
            $mediaId = (isset($urlParts['path'])) ? trim($urlParts['path'], '/') : '';
            return new MediaAsset($mediaSource, $mediaId);
        } elseif (isset($urlParts['scheme']) && in_array($urlParts['scheme'], array('http','https'))) {
            return new MediaAsset(new HttpSource(), $url);
        } else {
            throw new TaoMediaException('Cannot resolve '.$url);
        }
    }

}

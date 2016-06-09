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
 *
 */

namespace oat\taoItems\model\media;

use oat\tao\model\media\TaoMediaResolver;
use oat\tao\model\media\MediaAsset;

class ItemMediaResolver extends TaoMediaResolver {
    
    private $localMediaSource;

    public function __construct($item, $lang)
    {
        $this->localMediaSource = new LocalItemSource(array(
        	'item' => $item,
            'lang' => $lang
        ));
    }

    public function resolve($url)
    {
        $urlParts = parse_url($url);
        if (!isset($urlParts['scheme']) || !isset($urlParts['host'])) {
            return new MediaAsset($this->localMediaSource, ltrim($url, '/'));
        } else {
            return parent::resolve($url);
        }
    }

}

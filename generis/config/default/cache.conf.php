<?php
/**
 * The default cache implementation
 */

return new common_cache_KeyValueCache(array(
    common_cache_FileCache::OPTION_PERSISTENCE => 'cache'
));

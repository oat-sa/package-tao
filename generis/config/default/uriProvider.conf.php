<?php
/**
 * Default config header
 *
 * To replace this add a file generis/conf/header/uriProvider.conf.php
 */

return new core_kernel_uri_DatabaseSerialUriProvider(array(
    core_kernel_uri_DatabaseSerialUriProvider::OPTION_PERSISTENCE => 'default',
    core_kernel_uri_DatabaseSerialUriProvider::OPTION_NAMESPACE => LOCAL_NAMESPACE.'#'
));

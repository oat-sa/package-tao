<?php
/**
 * The Uri Provider configuration contains the service to be used
 * to generate unique URI for newly created resources
 * 
 * Providers must implement common_uri_UriProvider
 * 
 * Default uri provider is core_kernel_uri_DatabaseSerialUriProvider
 * 
 * Alternatives:
 *     core_kernel_uri_AdvKeyValueUriProvider
 *     common_uri_MicrotimeUriProvider
 *     common_uri_MicrotimeRandUriProvider
 * 
 * @see core_kernel_uri_UriService
 */
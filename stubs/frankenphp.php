<?php

/**
 * FrankenPHP stub file for IDE support
 * @see https://frankenphp.dev/
 */

if (!function_exists('frankenphp_handle_request')) {
    /**
     * Handle a FrankenPHP request
     * @param callable $callback
     * @return bool
     */
    function frankenphp_handle_request(callable $callback) {}
}

if (!function_exists('headers_send')) {
    /**
     * Send HTTP headers with a specific status code
     * @param int $code
     * @return void
     */
    function headers_send(int $code) {}
}

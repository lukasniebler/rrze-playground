<?php

namespace RRZE\Playground;
defined('ABSPATH') || exit;

/**
 * Your main plugin class. You can use it to embed different other classes. 
 */

class Main
{
    public function __construct()
    {
        /**
         * Loads the Helper class which offers a few helper functions. 
         * For example if you call the function Helper::debug('Hello World!'); 
         * It will print Hello World to the debug.log file.
         */
        new Helper();
        Helper::debug('Hello World!');

        /**
         * Load the Shortcode class for your shortcodes.
         */
        new Shortcode();
    }
}
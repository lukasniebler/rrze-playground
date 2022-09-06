<?php

namespace RRZE\Playground;

defined('ABSPATH') || exit;

class Shortcode
{
    public function __construct()
    {
        add_shortcode('rrze-playground', [$this, 'shortcode']);
        //add_shortcode('rrze-playground', array($this, 'shortcode');
    }

    //[rrze-playground]
    public function shortcode($atts, $content = null, $tag = '')
    {
        /**
         * $content spuckt den Inhalt zwischen öffnendem und schließenden Shortcode aus.
         * [rrze-playground]$content[/rrze-playground]
         */
        Helper::debug($content);

        /**
         * $atts ist das Array an übergebenen Shortcode parametern.
         */
        Helper::debug($atts);

        //Return steuert, was auf der Inhaltsseite ausgespuckt wird

        add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
        add_action('wp_enqueue_scripts', array($this, 'registerStyles'));
        return '<h2 class="rrze-playground">Ein Text der auf der Inhaltsseite erscheint, wenn der Shortcode verwendet wird.</h2>';
    }

    public function registerScripts()
    {
        Helper::debug(plugins_url('assets/js/app.js', RRZE_PLAYGROUND_FILE));
        wp_register_script(
            'rrze-playground',
            plugins_url('assets/js/app.js', RRZE_PLAYGROUND_FILE),
            ['jquery'],
            '0.0.1',
            true
        );
        wp_enqueue_script('rrze-playground');
    }

    public function registerStyles()
    {
        wp_register_style(
            'rrze-playground-css',
            plugins_url('assets/css/style.css', RRZE_PLAYGROUND_FILE),
            [],
            '0.0.1',
            'all'
        );
        wp_enqueue_style('rrze-playground-css');
    }
}

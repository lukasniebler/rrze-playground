<?php

/*
Plugin Name:     RRZE Playground
Description:     Base configuration for a simple plugin with Object Oriented Programming (OOP) and autoloading.
Version:         1.0.0
Author:          Firstname Lastname
Author URI:      https://www.wordpress.rrze.fau.de
License:         GNU General Public License v2
License URI:     http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:     rrze-playground
*/

namespace RRZE\Playground;

defined('ABSPATH') || exit;

use RRZE\Playground\Main;

const RRZE_PLAYGROUND_FILE = __FILE__;
const RRZE_PHP_VERSION = '7.4';
const RRZE_WP_VERSION = '5.8';

/**
 * SPL Autoloader (PSR-4)
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    $prefix = __Namespace__;
    $base_dir = __DIR__ . '/includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Registers hooks for plugin activation, deactivation or when the plugin is loaded. Runs the linked functions activation, deactivation and loaded during certain events.
 */
// Register plugin hooks.
register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');

add_action('plugins_loaded', __NAMESPACE__ . '\loaded');

/**
 * Loads a plugin's translated strings. In theory you can create your own translations with the free poedit software. ( https://poedit.net/)
 */
function loadTextdomain()
{
    load_plugin_textdomain('rrze-playground', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

/**
 * System requirements verification. Checks if System is compatible with the plugin and displays an error message if not.
 * @return string Return an error message.
 */
function systemRequirements(): string
{
    loadTextdomain();
    $error = '';
    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(
            /* translators: 1: Server PHP version number, 2: Required PHP version number. */
            __('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'rrze-playground'),
            PHP_VERSION,
            RRZE_PHP_VERSION
        );
    } elseif (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(
            /* translators: 1: Server WordPress version number, 2: Required WordPress version number. */
            __('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'rrze-playground'),
            $GLOBALS['wp_version'],
            RRZE_WP_VERSION
        );
    }
    return $error;
}

/**
 * Activation callback function. This function is run when the plugin is activated.
 */
function activation()
{
    if ($error = systemRequirements()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            sprintf(
                /* translators: 1: The plugin name, 2: The error string. */
                __('Plugins: %1$s: %2$s', 'rrze-playground'),
                plugin_basename(__FILE__),
                $error
            )
        );
    }
}

/**
 * Deactivation callback function. This function is run when the plugin is deactivated. If it's empty nothing will be executed during the deactivation phase.
 */
function deactivation()
{
    return;
}

/**
 * Instantiate Plugin class.
 * @return object Plugin
 */
function plugin()
{
    static $instance;
    if (null === $instance) {
        $instance = new Plugin(__FILE__);
    }
    return $instance;
}

/**
 * Loaded callback function. This function is run when the plugin is loaded. If it's empty nothing will be executed after the plugin was loaded completely within WordPress.
 * @return void
 */
function loaded()
{
    add_action('init', __NAMESPACE__ . '\loadTextdomain');
    plugin()->loaded();
    if ($error = systemRequirements()) {
        add_action('admin_init', function () use ($error) {
            if (current_user_can('activate_plugins')) {
                $pluginData = get_plugin_data(plugin()->getFile());
                $pluginName = $pluginData['Name'];
                $tag = is_plugin_active_for_network(plugin()->getBaseName()) ? 'network_admin_notices' : 'admin_notices';
                add_action($tag, function () use ($pluginName, $error) {
                    printf(
                        '<div class="notice notice-error"><p>' .
                            /* translators: 1: The plugin name, 2: The error string. */
                            __('Plugins: %1$s: %2$s', 'rrze-statistik') .
                            '</p></div>',
                        esc_html($pluginName),
                        esc_html($error)
                    );
                });
            }
        });
        return;
    }

    new Main;
}
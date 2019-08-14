<?php
/**
 * Plugin Name: Command Palette
 * Description: Bring power of text editor command palette to WordPress Admin.
 * Version:     1.0.0
 * Author:      Tung Du
 * Author URI:  https://profiles.wordpress.org/dinhtungdu/
 * Text Domain: command-palette
 * Domain Path: /languages
 * Copyright:   (c) 2019, Tung Du.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * @package   CommandPalette
 * @author    Tung Du
 * @copyright Copyright (c) 2019, Tung Du.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace CommandPalette;

defined( 'ABSPATH' ) || die;

if ( defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCP_VER', time() );
} else {
	define( 'SCP_VER', '1.0.0' );
}
define( 'SCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCP_URL', plugin_dir_url( __FILE__ ) );

require __DIR__ . '/vendor/autoload.php';

( new Plugin() )->registerServices();

load_plugin_textdomain( 'wc-vendors-tax', false, basename( __DIR__ ) . '/languages/' );

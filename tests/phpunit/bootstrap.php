<?php
/**
 * Unit test bootstrap file.
 *
 * @package CommandPalette
 */

// Define test environment.
define( 'CP_PHPUNIT', true );

// Define fake ABSPATH.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', sys_get_temp_dir() );
}
// Define fake PLUGIN_ABSPATH.
if ( ! defined( 'PLUGIN_ABSPATH' ) ) {
	define( 'PLUGIN_ABSPATH', sys_get_temp_dir() . '/wp-content/plugins/command-palette/' );
}

// Defind plugin constant
define( 'SCP_VER', '1.0.1' );
define( 'SCP_DIR', PLUGIN_ABSPATH );
define( 'SCP_URL', 'https://example.com/' );

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/includes/CPTestCase.php';

WP_Mock::setUsePatchwork( true );
WP_Mock::bootstrap();

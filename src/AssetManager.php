<?php
/**
 * Manage assets used for the plugin.
 *
 * @package CommandPalette
 */

namespace CommandPalette;

/**
 * Class: AssetManager
 */
class AssetManager {
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'registerScripts' ] );
	}

	public function registerScripts() {
		wp_enqueue_script( 'command-palette-main', SCP_URL . 'assets/js/main.js', [ 'jquery' ], SCP_VER, true );
		wp_enqueue_style( 'command-palette-main', SCP_URL . 'assets/css/main.css', [], SCP_VER );

		do_action( 'command_palette_enqueue_scripts' );
	}
}

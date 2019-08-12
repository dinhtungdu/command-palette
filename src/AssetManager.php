<?php
namespace CommandPalette;

class AssetManager {
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
	}

	public function register_scripts() {
		wp_enqueue_script( 'command-palette-main', SCP_URL . 'assets/js/main.js', [ 'jquery' ], SCP_VER, true );
		wp_enqueue_style( 'command-palette-main', SCP_URL . 'assets/css/main.css', [], SCP_VER );

		do_action( 'command_palette_enqueue_scripts' );
	}
}

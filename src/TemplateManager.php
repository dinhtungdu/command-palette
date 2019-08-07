<?php
namespace CommandPalette;

class TemplateManager {
	public function hooks() {
		add_action( 'admin_footer', [ $this, 'commandBoxTemplate' ] );
	}

	public function commandBoxTemplate() {
		include_once SCP_DIR . 'templates/command-palette.php';
	}
}

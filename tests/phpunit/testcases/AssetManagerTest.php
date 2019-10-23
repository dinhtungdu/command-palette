<?php
use CommandPalette\AssetManager;

class AssetManagerTest extends CPTestCase {
	public function test_hooks() {
		$instance = new AssetManager();
		WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $instance, 'register_scripts' ] );
		$instance->hooks();
	}
}

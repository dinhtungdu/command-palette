<?php
use CommandPalette\AssetManager;

class AssetManagerTest extends CPTestCase {
	public function test_hooks() {
		$instance = new AssetManager();
		WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $instance, 'register_scripts' ] );
		$instance->hooks();
	}

	public function test_register_scripts() {
		WP_Mock::userFunction( 'wp_enqueue_style' )
			->with( Mockery::type( 'string' ), Mockery::type( 'string' ), Mockery::type( 'array' ), Mockery::type( 'string' ) )
			->andReturn( true );
		WP_Mock::userFunction( 'wp_enqueue_script' )
			->with( Mockery::type( 'string' ), Mockery::type( 'string' ), Mockery::type( 'array' ), Mockery::type( 'string' ), Mockery::type( 'bool' ) )
			->andReturn( true );
		WP_Mock::expectAction( 'command_palette_enqueue_scripts' );
		$instance = new AssetManager();
		$instance->register_scripts();
	}
}

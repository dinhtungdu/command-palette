<?php
use CommandPalette\CacheManager;

class CacheManagerTest extends CPTestCase {
	public function testHooks() {
		$instance = new CacheManager();
		WP_Mock::userFunction( 'plugin_basename' )
			->with( Mockery::type( 'string' ) )
			->once()
			->andReturn( 'command-palette' );

		WP_Mock::expectActionAdded( 'load-plugins.php', [ $instance, 'deleteCachedItems' ] );
		WP_Mock::expectFilterAdded( 'plugin_action_links_command-palette', [ $instance, 'addDeleteCacheLink' ] );
		WP_Mock::expectActionAdded( 'admin_notices', [ $instance, 'deleteCacheNotice' ] );

		$instance->hooks();
	}

	public function testDeleteCachedItems_WithoutNonce_ReturnFalse() {
		$instance = new CacheManager();
		$this->assertFalse( $instance->deleteCachedItems() );
	}

	public function testDeleteCachedItems_WrongNonce_ReturnFalse() {
		WP_Mock::userFunction( 'wp_verify_nonce' )->andReturnFalse();
		WP_Mock::passthruFunction( 'sanitize_key' );
		WP_Mock::passthruFunction( 'wp_unslash' );

		$_GET['cp_cache_nonce'] = 'nonce';

		$instance = new CacheManager();

		$this->assertFalse( $instance->deleteCachedItems() );
	}

	public function testDeleteCachedItems_DeleteActionSetToNo_ReturnFalse() {
		WP_Mock::userFunction( 'wp_verify_nonce' )->andReturnTrue();
		WP_Mock::passthruFunction( 'sanitize_key' );
		WP_Mock::passthruFunction( 'wp_unslash' );

		$_GET['cp_cache_nonce']  = 'nonce';
		$_GET['cp_delete_cache'] = 'no';

		$instance = new CacheManager();

		$this->assertFalse( $instance->deleteCachedItems() );
	}

	public function testDeleteCachedItems_DeleteActionSetToYes_ReturnFalse() {
		WP_Mock::userFunction( 'wp_verify_nonce' )->andReturnTrue();
		WP_Mock::userFunction( 'delete_transient' )->andReturnTrue();
		WP_Mock::passthruFunction( 'sanitize_key' );
		WP_Mock::passthruFunction( 'wp_unslash' );

		$_GET['cp_cache_nonce']  = 'nonce';
		$_GET['cp_delete_cache'] = 'yes';

		$instance = new CacheManager();

		$this->assertNull( $instance->deleteCachedItems() );
	}

	public function testAddDeleteCacheLink() {
		WP_Mock::passthruFunction( 'wp_create_nonce' );
		WP_Mock::passthruFunction( 'admin_url' );
		WP_Mock::passthruFunction( 'esc_url' );
		WP_Mock::userFunction( 'add_query_arg' )
			->with( Mockery::type( 'array' ), Mockery::type( 'string' ) )
			->andReturn( 'http://parsed-url.com' );

		$instance = new CacheManager();
		$links    = $instance->addDeleteCacheLink( [] );

		$this->assertFalse( empty( $links ) );
		$this->assertStringContainsString( 'parsed-url', array_values( $links )[0] );
	}

	public function testDeleteCacheNotice_NotRender() {
		WP_Mock::userFunction( 'wp_verify_nonce' )->andReturnTrue();
		WP_Mock::userFunction( 'delete_transient' )->andReturnTrue();
		WP_Mock::passthruFunction( 'sanitize_key' );
		WP_Mock::passthruFunction( 'wp_unslash' );

		$_GET['cp_cache_nonce']  = 'nonce';
		$_GET['cp_delete_cache'] = 'no';

		$instance = new CacheManager();
		$this->assertNull( $instance->deleteCacheNotice() );
	}

	public function testDeleteCacheNotice() {
		WP_Mock::userFunction( 'wp_verify_nonce' )->andReturnTrue();
		WP_Mock::userFunction( 'delete_transient' )->andReturnTrue();
		WP_Mock::passthruFunction( 'sanitize_key' );
		WP_Mock::passthruFunction( 'wp_unslash' );

		$_GET['cp_cache_nonce']  = 'nonce';
		$_GET['cp_delete_cache'] = 'yes';

		$instance = new CacheManager();
		$this->expectOutputRegex( '/.*notice-success.*/', $instance->deleteCacheNotice() );
	}
}

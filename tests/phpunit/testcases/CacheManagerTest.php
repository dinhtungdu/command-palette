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
}

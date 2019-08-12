<?php
namespace CommandPalette;

class CacheManager {

	public function hooks() {
		add_action( 'load-plugins.php', [ $this, 'deleteCachedItems' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( SCP_DIR . '/command-palette.php' ), [ $this, 'addDeleteCacheLink' ] );
	}

	public function deleteCachedItems() {
		if (
			! isset( $_GET['cp_nonce'] )
			|| ! wp_verify_nonce( $_GET['cp_nonce'], 'delete-items' )
			|| ! isset( $_GET['cp_delete_cache'] )
			|| 'yes' != $_GET['cp_delete_cache']
		) {
			return;
		}

		delete_transient( 'command_palette_items' );
	}

	public function addDeleteCacheLink( $links ) {
		$deleteCacheLink = add_query_arg(
			[
				'cp_delete_cache' => 'yes',
				'cp_nonce'        => wp_create_nonce( 'delete-items' ),
			],
			admin_url( 'plugins.php' )
		);

		$links['delete_cache'] = sprintf(
			'<a href="%s">%s</a>',
			$deleteCacheLink,
			__( 'Delete cache', 'command-palette' )
		);

		return $links;
	}

}

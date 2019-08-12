<?php
namespace CommandPalette;

class CacheManager {

	public function hooks() {
		add_action( 'load-plugins.php', [ $this, 'deleteCachedItems' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( SCP_DIR . '/command-palette.php' ), [ $this, 'addDeleteCacheLink' ] );
		add_action( 'admin_notices', [ $this, 'deleteCacheNotice' ] );
	}

	public function deleteCachedItems() {
		if ( ! $this->isDeletingCache() ) {
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
			esc_url( $deleteCacheLink ),
			__( 'Delete cache', 'command-palette' )
		);

		return $links;
	}

	public function deleteCacheNotice() {
		if ( ! $this->isDeletingCache ) {
			return;
		}

		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			'notice notice-success is-dismissible',
			__( 'Deleted cache successfully!', 'command-palette' )
		);
	}

	private function isDeletingCache() {
		if (
			! isset( $_GET['cp_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['cp_nonce'] ) ), 'delete-items' )
			|| ! isset( $_GET['cp_delete_cache'] )
			|| 'yes' != sanitize_text_field( wp_unslash( $_GET['cp_delete_cache'] ) )
		) {
			return false;
		}

		return true;
	}

}

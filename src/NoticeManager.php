<?php
namespace CommandPalette;

class NoticeManager {
	public function hooks() {
		add_action( 'admin_notices', [ $this, 'deleteCache' ] );
	}

	public function deleteCache() {
		if (
			! isset( $_GET['cp_nonce'] )
			|| ! wp_verify_nonce( $_GET['cp_nonce'], 'delete-items' )
			|| ! isset( $_GET['cp_delete_cache'] )
			|| 'yes' != $_GET['cp_delete_cache']
		) {
			return;
		}

		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			'notice notice-success is-dismissible',
			__( 'Delete cache successfully!', 'command-palette' )
		);
	}
}

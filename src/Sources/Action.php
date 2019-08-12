<?php
namespace CommandPalette\Sources;

class Action extends Base {

	public function __construct() {
		add_action( 'command_palette_enqueue_scripts', [ $this, 'printScriptForAction' ] );
	}

	public function get_id() {
		return 'Action';
	}

	public function getItemsData() {
		return apply_filters(
			'command_palette_items_action',
			[
				[
					'id'          => 'delete-cache',
					'title'       => __( 'Delete Cache', 'command-palette' ),
					'description' => __( 'Clear/pure Command Palette cache.', 'command-palette' ),
					'capability'  => 'manage_options',
					'url'         => add_query_arg(
						[
							'cp_delete_cache' => 'yes',
							'cp_nonce'        => wp_create_nonce( 'delete-items' ),
						],
						admin_url( 'plugins.php' )
					),
				],
				[
					'id'          => 'update-permalink',
					'title'       => __( 'Update Permalink', 'command-palette' ),
					'description' => __( 'Reset permalink structure. Fix the 404 error with pretty URL.', 'command-palette' ),
					'url'         => admin_url( 'options-permalink.php' ),
					'capability'  => 'manage_options',
				],
				[
					'id'          => 'update-core',
					'title'       => __( 'Update WordPress', 'command-palette' ),
					'description' => __( 'Upgrade or reinstall WordPress Core.', 'command-palette' ),
					'url'         => admin_url( 'update-core.php' ),
					'capability'  => 'update_core',
				],
				[
					'id'         => 'update-plugins',
					'title'      => __( 'Update all plugins', 'command-palette' ),
					'url'        => admin_url( 'update-core.php' ),
					'capability' => 'update_plugins',
				],
				[
					'id'          => 'update-themes',
					'title'       => __( 'Update all themes', 'command-palette' ),
					'description' => __( 'Please Note: Any customizations you have made to theme files will be lost', 'command-palette' ),
					'url'         => admin_url( 'update-core.php' ),
					'capability'  => 'update_themes',
				],
				[
					'id'         => 'allow-search-engine',
					'title'      => __( 'Allow search engines from indexing this site.', 'command-palette' ),
					'url'        => admin_url( 'options-reading.php' ),
					'capability' => 'manage_options',
				],
				[
					'id'         => 'disallow-search-engine',
					'title'      => __( 'Discourage search engines from indexing this site.', 'command-palette' ),
					'url'        => admin_url( 'options-reading.php' ),
					'capability' => 'manage_options',
				],
			]
		);
	}

	public function getActionsData() {
		return apply_filters(
			'command_palette_actions_data',
			[
				'update-permalink'       => [
					'click' => '.button#submit',
				],
				'update-core'            => [
					'click' => '.button#upgrade',
				],
				'update-plugins'         => [
					'click' => '#plugins-select-all',
					'click' => '.button#upgrade-plugins',
				],
				'update-themes'          => [
					'click' => '#themes-select-all',
					'click' => '.button#upgrade-themes',
				],
				'allow-search-engine'    => [
					'uncheck' => '#blog_public',
					'click'   => '.button#submit',
				],
				'disallow-search-engine' => [
					'check' => '#blog_public',
					'click' => '.button#submit',
				],
			]
		);
	}

	public function printScriptForAction() {
		if ( ! isset( $_GET['cp_action'] ) ) {
			return;
		}
		$action = sanitize_text_field( wp_unslash( $_GET['cp_action'] ) );
		$steps  = $this->getActionSteps( $action );
		if ( ! $steps ) {
			return;
		}
		$script = apply_filters(
			'command_palette_action_script',
			$this->prepareScriptForAction( $steps ),
			$action
		);

		$script = 'jQuery(document).ready(function(){' . $script . '});';

		wp_add_inline_script( 'command-palette-main', $script );
	}

	protected function prepareItems() {
		$items = $this->getItemsData();

		$items = array_map(
			[ $this, 'addActionProperties' ],
			$items
		);

		array_map(
			[ $this, 'addItem' ],
			$items
		);
	}

	private function getActionSteps( $action ) {
		$actions = $this->getActionsData();
		if ( isset( $actions[ $action ] ) ) {
			return $actions[ $action ];
		}
		return false;
	}

	private function prepareScriptForAction( $steps ) {
		$script = '';
		foreach ( $steps as $action => $target ) {
			switch ( $action ) {
				case 'click':
					$script .= sprintf( "jQuery('%s').click();", $target );
					break;
				case 'check':
					$script .= sprintf( "jQuery('%s').prop('checked', true).trigger('change');", $target );
					break;
				case 'uncheck':
					$script .= sprintf( "jQuery('%s').prop('checked', false).trigger('change');", $target );
					break;
			}
		}
		return $script;
	}

	private function addActionProperties( $item ) {
		$item['url'] = add_query_arg(
			[ 'cp_action' => $item['id'] ],
			$item['url']
		);

		if ( ! isset( $item['type'] ) ) {
			$item['type'] = __( 'Action', 'command-palette' );
		}

		return $item;
	}
}

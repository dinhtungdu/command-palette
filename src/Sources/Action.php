<?php
namespace CommandPalette\Sources;

class Action extends Base {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'printScriptForAction' ] );
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
					'description' => __( 'Pure Command Palette cache.', 'command-palette' ),
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
				],
				[
					'id'          => 'update-core',
					'title'       => __( 'Update WordPress', 'command-palette' ),
					'description' => __( 'Upgrade or reinstall WordPress Core.', 'command-palette' ),
					'url'         => admin_url( 'update-core.php' ),
				],
			]
		);
	}

	public function getActionsData() {
		return apply_filters(
			'command_palette_actions_data',
			[
				'update-permalink' => [
					'click' => '.button#submit',
				],
				'update-core'      => [
					'click' => '.button#upgrade',
				],
			]
		);
	}

	public function printScriptForAction() {
		if ( ! isset( $_GET['cp_action'] ) ) {
			return;
		}
		$action = $_GET['cp_action'];
		$steps  = $this->getActionSteps( $action );
		if ( ! $steps ) {
			return;
		}
		$script = apply_filters(
			'command_palette_action_script',
			$this->prepareScriptForAction( $steps ),
			$action
		);

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
					$script .= sprintf( 'jQuery("%s").click();', $target );
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
			$item['type'] = 'action';
		}

		return $item;
	}
}

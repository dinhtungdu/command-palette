<?php
namespace CommandPalette\Sources;

class Custom extends Base {

	public function get_id() {
		return 'Custom';
	}

	public function getItemsData() {
		return apply_filters(
			'command_palette_items_custom',
			[
				[
					'id'          => 'wordpress',
					'title'       => __( 'WordPress', 'command-palette' ),
					'description' => __( 'WordPress home page', 'command-palette' ),
					'url'         => 'https://wordpress.org',
					'target'      => '_blank',
					'category'    => __( 'External', 'command-palette' ),
				],
			]
		);
	}

	protected function prepareItems() {
		array_map(
			[ $this, 'addItem' ],
			$this->getItemsData()
		);
	}
}

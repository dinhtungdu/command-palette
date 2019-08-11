<?php
namespace CommandPalette\Sources;

class Custom extends Base {

	public function get_id() {
		return 'Custom';
	}

	public function getItemsData() {
		return apply_filters(
			'command_palette_custom_items',
			[
				'wordpress' => [
					'title'       => 'WordPress',
					'description' => 'WordPress home page.',
					'url'         => 'https://wordpress.org',
					'target'      => '_blank',
					'category'    => 'External',
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

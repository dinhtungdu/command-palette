<?php
namespace CommandPalette\Sources;

class Custom extends Base {

	public function get_id() {
		return 'Custom';
	}

	public function getItemsData() {
		return apply_filters( 'command_palette_custom_items', [] );
	}

	protected function prepareItems() {
		array_map(
			[ $this, 'addItem' ],
			$this->getItemsData()
		);
	}
}

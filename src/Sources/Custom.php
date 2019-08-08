<?php
namespace CommandPalette\Sources;

class Custom extends Base {

	public function get_id() {
		return 'Custom';
	}

	protected function prepareItems() {
		$this->addItem(
			[
				'id'       => 'google',
				'title'    => __( 'Google', 'command-palette' ),
				'url'      => 'https://www.google.com',
				'category' => __( 'Custom', 'command-palette' ),
			]
		);
	}
}

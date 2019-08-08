<?php
namespace CommandPalette\Sources;

abstract class Base {
	protected $items;

	abstract public function get_id();

	abstract protected function prepareItems();

	public function getItems() {
		return $this->items;
	}

	protected function addItem( $data ) {
		$default = [
			'type'     => 'url',
			'key'      => '',
			'url'      => '',
			'script'   => '',
			'category' => __( 'General', 'command-palette' ),
		];

		$data = wp_parse_args( $data, $default );

		if ( ! $data['key'] && ! $data['url'] ) {
			return;
		}

		$this->items[ $data['key'] ] = [
			'url'      => $data['url'],
			'category' => $data['category'],
		];
	}
}

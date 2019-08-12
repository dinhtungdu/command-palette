<?php
namespace CommandPalette\Sources;

abstract class Base {
	protected $items = [];

	abstract public function get_id();

	abstract protected function prepareItems();

	public function getItems() {
		$this->prepareItems();
		return $this->items;
	}

	protected function addItem( $data ) {
		$default = [
			'type'        => __( 'Link', 'command-palette' ),
			'id'          => '',
			'url'         => '',
			'title'       => '',
			'capability'  => 'read',
			'category'    => '',
			'description' => '',
			'target'      => '',
		];

		$data = wp_parse_args( $data, $default );

		if ( ! $data['id'] && ! $data['url'] && ! $data['title'] ) {
			return;
		}

		$this->items[ $data['id'] ] = $data;
	}
}

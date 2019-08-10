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
			'type'       => __( 'Link', 'command-palette' ),
			'id'         => '',
			'url'        => '',
			'title'      => '',
			'script'     => '',
			'capability' => 'read',
			'category'   => '',
		];

		$data = wp_parse_args( $data, $default );

		if ( ! $data['id'] && ! $data['url'] && ! $data['title'] ) {
			return;
		}

		$this->items[ $data['id'] ] = [
			'type'       => $data['type'],
			'id'         => $data['id'],
			'title'      => $data['title'],
			'url'        => $data['url'],
			'category'   => $data['category'],
			'capability' => $data['capability'],
		];
	}
}

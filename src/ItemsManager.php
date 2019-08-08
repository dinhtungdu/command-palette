<?php
namespace CommandPalette;

class ItemsManager {
	private $items;

	public function getAll() {
		return $this->items;
	}

	public function get( $key ) {
		if ( ! in_array( $key, $this->items ) ) {
			return null;
		}

		return $this->items[ $key ];
	}

	public function add( $data ) {
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

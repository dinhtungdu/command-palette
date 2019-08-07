<?php
namespace CommandPalette;

class ResultsManager {
	private $results;

	public function getAll() {
		return $this->results;
	}

	public function get( $key ) {
		if ( ! in_array( $key, $this->results ) ) {
			return null;
		}

		return $this->results[ $key ];
	}

	public function add( $data ) {
		$default = [
			'key'      => '',
			'url'      => '',
			'category' => __( 'General', 'command-palette' ),
		];

		$data = wp_parse_args( $data, $default );

		if ( ! $data['key'] && ! $data['url'] ) {
			return;
		}

		$this->results[ $data['key'] ] = [
			'url'      => $data['url'],
			'category' => $data['category'],
		];
	}

}

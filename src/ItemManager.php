<?php
namespace CommandPalette;

class ItemManager {
	private $sources;

	public function __construct( array $sources ) {
		$this->sources = $sources;
	}

	public function hooks() {
		// add_action( 'admin_enqueue_scripts', [ $this, 'printItemsJson' ] );
		add_action( 'admin_init', [ $this, 'maybeCacheItems' ] );
		add_action( 'wp_ajax_get_cp_items', [ $this, 'getItemsAjax' ] );
	}

	public function printItemsJson() {
		wp_localize_script( 'command-palette-main', 'CPItems', $this->getItemsForCurrentUser() );
	}

	public function getItemsAjax() {
		wp_send_json_success( $this->getItemsForCurrentUser() );
	}

	public function maybeCacheItems() {
		if ( $this->getCachedItems() ) {
			return;
		}

		$this->cacheItems();
	}

	private function getItemsForCurrentUser() {
		$items = $this->getCachedItems();
		if ( ! $items ) {
			return [];
		}
		$items = apply_filters( 'command_palette_items', $items );
		$items = array_filter( $items, [ $this, 'filterItemsByCapability' ] );
		return array_values( $items );
	}

	private function filterItemsByCapability( $item ) {
		return current_user_can( $item['capability'] );
	}

	private function getCachedItems() {
		return get_transient( 'command_palette_items' );
	}

	private function cacheItems() {
		$items = $this->getItemsFromSources();

		if ( ! is_array( $items ) || empty( $items ) ) {
			return [];
		}

		set_transient( 'command_palette_items', $items, DAY_IN_SECONDS * 30 );
	}

	private function getItemsFromSources() {
		$items = [];

		foreach ( $this->sources as $source ) {
			$items = array_merge( $items, $source->getItems() );
		}

		return $items;
	}
}

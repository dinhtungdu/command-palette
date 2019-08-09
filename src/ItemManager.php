<?php
namespace CommandPalette;

class ItemManager {
	private $sources;

	public function __construct( array $sources ) {
		$this->sources = $sources;
	}

	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'printItemsJson' ], 10 );
	}

	public function printItemsJson() {
		wp_localize_script( 'command-palette-main', 'CPItems', $this->getItemsForCurrentUser() );
	}

	private function getItemsForCurrentUser() {
		$items = $this->getCachedItems();
		$items = apply_filters( 'command_palette_items', $items );
		$items = array_filter( $items, [ $this, 'filterItemsByCapability' ] );
		return array_values( $items );
	}

	private function filterItemsByCapability( $item ) {
		return current_user_can( $item['capability'] );
	}

	private function getCachedItems() {
		$cachedItems = get_transient( 'command_palette_items' );
		if ( $cachedItems ) {
			return $cachedItems;
		}
		return $this->cacheItems();
	}

	private function cacheItems() {
		$items = $this->getItemsFromSources();

		if ( ! is_array( $items ) || empty( $items ) ) {
			return [];
		}

		set_transient( 'command_palette_items', $items, DAY_IN_SECONDS * 30 );
		return $items;
	}

	private function getItemsFromSources() {
		$items = [];

		foreach ( $this->sources as $source ) {
			$items = array_merge( $items, $source->getItems() );
		}

		return $items;
	}
}

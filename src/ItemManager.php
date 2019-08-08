<?php
namespace CommandPalette;

class ItemManager {
	private $sources;

	public function __construct( array $sources ) {
		$this->sources = $sources;
	}

	public function hooks() {
		add_action( 'command_palette_items_output', [ $this, 'printItems' ] );
	}

	public function printItems() {
		$items = $this->getCachedItems();
		$items = apply_filters( 'command_palette_items', $items );
		$items = array_filter( $items, [ $this, 'filterItemsByCapability' ] );
		foreach ( $items as $item ) {
			$this->printSingleItem( $item );
		}
	}

	private function filterItemsByCapability( $item ) {
		return current_user_can( $item['capability'] );
	}

	private function printSingleItem( $item ) {
		printf(
			'<a href="%s" class="item" data-id="%s" data-category="%s" data-type="%s">%s</a>',
			esc_url( $item['url'] ),
			esc_attr( $item['id'] ),
			esc_attr( $item['category'] ),
			esc_attr( $item['type'] ),
			wp_kses_post( $item['title'] )
		);
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

<?php
namespace CommandPalette\Sources;

class AdminPages extends Base {

	public function get_id() {
		return 'AdminPages';
	}

	protected function prepareItems() {
		global $menu, $submenu;
		$filteredMenu = array_filter( $menu, [ $this, 'removeInvalidItem' ] );
		foreach ( $filteredMenu as $menuItem ) {
			$this->addItem(
				[
					'id'         => $menuItem[5],
					'capability' => $menuItem[1],
					'title'      => $menuItem[0],
					'url'        => admin_url( $menuItem[2] ),
					'category'   => __( 'Admin pages', 'command-palette' ),
				]
			);
		}
	}

	private function removeInvalidItem( $menuItem ) {
		return (bool) $menuItem[0];
	}
}
